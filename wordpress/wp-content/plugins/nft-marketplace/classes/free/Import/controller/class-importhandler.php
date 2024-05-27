<?php
/**
 * File containing MoNftImportHandler class which handles the ajax requests.
 *
 * @package MoNft\Controller
 */

namespace MoNft\controller;

use MoNft\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MoNft\controller\ImportHandler' ) ) {
	/**
	 * Class for handling ajax requests
	 */
	class ImportHandler {
		/**
		 * Private variable data
		 *
		 * @var data
		 */
		private $data;
		/**
		 * Private variable request
		 *
		 * @var request
		 */
		private $request;
		/**
		 * Private variable utils
		 *
		 * @var utils
		 */
		private $utils;

		/**
		 * Constructor for class MoNftImportHandler
		 */
		public function __construct() {

			$this->utils = new Utils();

			add_action( 'wp_ajax_monft_signed_list_wc_product', array( $this, 'signed_list_wc_product' ) );
			add_action( 'wp_ajax_nopriv_monft_signed_list_wc_product', array( $this, 'signed_list_wc_product' ), 1 );
			add_action( 'wp_ajax_monft_after_buy_wc_product', array( $this, 'after_buy_wc_product' ) );
			add_action( 'wp_ajax_nopriv_monft_after_buy_wc_product', array( $this, 'after_buy_wc_product' ), 1 );
			add_action( 'wp_ajax_monft_get_listed_voucher', array( $this, 'get_listed_voucher' ) );
			add_action( 'wp_ajax_nopriv_monft_get_listed_voucher', array( $this, 'get_listed_voucher' ), 1 );
			add_action( 'wp_ajax_monft_remove_listing', array( $this, 'remove_listing_action' ) );
		}

		/**
		 * Verify signature through API.
		 *
		 * @param message   $message The message which was signed by the user.
		 * @param signature $signature The signature of the user.
		 * @param address   $address The public address (wallet address) of the owner of the NFT.
		 */
		public function verify_signature( $message, $signature, $address ) {
			$url      = \MoNft\Constants::HASCOIN_ETHEREUM_SIGNATURE_VERIFICATION_API;
			$headers  = array(
				'Content-Type'  => 'application/json',
				'authorization' => \MoNft\Constants::HASCOIN_AUTHORIZATION_KEY,
			);
			$body     = array(
				'message'   => $message,
				'signature' => $signature,
			);
			$args     = array(
				'method'  => 'POST',
				'body'    => wp_json_encode( $body ),
				'headers' => $headers,
			);
			$response = wp_remote_post( $url, ( $args ) );
			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				$error         = new WP_Error( 'Failed', 'API verification of signature failed', $error_message );
				wp_send_json_error( $error );
				exit();
			}
			$response          = wp_remote_retrieve_body( $response );
			$response          = json_decode( $response, true );
			$retrieved_address = $response['address'];
			return strtolower( $address ) === strtolower( $retrieved_address );
		}

		/**
		 * Function to update listed nft details in database
		 *
		 * @return void
		 */
		public function signed_list_wc_product() {
			global $mo_nft_util;
			if ( isset( $_POST['mo_nft_verify_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_nft_verify_nonce'] ) ), 'mo_nft_wp_nonce' ) && isset( $_REQUEST['request'] ) ) {
				$request = sanitize_text_field( wp_unslash( $_REQUEST ['request'] ) );
				if ( 'monft_signed_list' === $request ) {
					$signed_data = isset( $_REQUEST['signedData'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['signedData'] ) ) : null;
					if ( isset( $signed_data['message'] ) && isset( $signed_data['Signature'] ) && isset( $signed_data['Token_Id'] ) ) {
						$message   = $signed_data['message'];
						$signature = $signed_data['Signature'];
						$token_id  = $signed_data['Token_Id'];
						$price     = $signed_data['Price'];
					} else {
						$error = wp_send_json_error( 'Invalid', 'Invalid voucher signed data', 'Signed list voucher has invalid data.' );
						wp_send_json_error( $error );
					}
					$tokenid_to_owner = $mo_nft_util->get_option( 'monft_tokenid_to_owner' );
					$address          = $tokenid_to_owner[ $token_id ];
					if ( $this->verify_signature( $message, $signature, $address ) ) {
						update_post_meta( $signed_data['Token_Id'], '_monft_listed_signed_voucher', $signed_data );
						update_post_meta( $signed_data['Token_Id'], '_monft_nft_listed', 'yes' );
						$listed_orders = $mo_nft_util->get_option( 'monft_listed_nft_order_ids' );
						if ( ! $listed_orders ) {
							$listed_orders = array();
						}
						$listed_orders[ $signed_data['Token_Id'] ] = $price;
						$mo_nft_util->update_option( 'monft_listed_nft_order_ids', $listed_orders );

						$prod_info             = array(
							'prod_id'    => $token_id,
							'list_price' => $price,
						);
						$signed_data['status'] = true;
						wp_send_json( $signed_data );
					}
				}
			}
		}

		/**
		 * Function to remove zero padding from Ethereum Address as returned by Moralis API.
		 *
		 * @param padded_address $padded_address The public address padded by zeros to 32 bytes.
		 */
		public function remove_zero_padding_from_eth_address( $padded_address ) {
			if ( 0 === strpos( $padded_address, '0x' ) ) {
				$padded_address = substr( $padded_address, 2 );
			}
			$address = ltrim( $padded_address, '\x00' );
			$address = '0x' . $address;
			return $address;
		}

		/**
		 * Function to update NFT details after purchasing NFT in database
		 *
		 * @return void
		 */
		public function after_buy_wc_product() {
			global $mo_nft_util;

			if ( isset( $_POST['mo_nft_verify_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_nft_verify_nonce'] ) ), 'mo_nft_wp_nonce' ) && isset( $_REQUEST['request'] ) ) {
				$request = sanitize_text_field( wp_unslash( $_REQUEST ['request'] ) );
				if ( 'monft_list' === $request ) {
					$token_id                = isset( $_POST['postId'] ) ? sanitize_text_field( wp_unslash( $_POST['postId'] ) ) : null;
					$tx_hash                 = isset( $_POST['txHash'] ) ? sanitize_text_field( wp_unslash( $_POST['txHash'] ) ) : null;
					$monft_collection_object = $mo_nft_util->get_option( 'mo_nft_token_config_details_store' );
					foreach ( $monft_collection_object as $key => $value ) {
						$chain            = $monft_collection_object[ $key ]['blockchain'];
						$contract_address = $monft_collection_object[ $key ]['contractAddress'];
					}
					$headers                  = array(
						'Accept'    => 'application/json',
						'X-API-Key' => \MoNft\Constants::MORALIS_API_KEY,
					);
					$args                     = array(
						'headers' => $headers,
					);
					$query                    = \MoNft\Constants::MORALI_TRANSACTION_ENDPONT . $tx_hash . '?chain=' . $chain;
					$response_moralis_tx_data = wp_remote_get( $query, $args );
					$moralis_tx_data_json     = wp_remote_retrieve_body( $response_moralis_tx_data );
					$moralis_tx_data          = json_decode( $moralis_tx_data_json, true );
					$item_bought_event_hash   = \MoNft\Constants::ITEM_BOUGHT_EVENT_HASH;
					if ( isset( $moralis_tx_data['logs'] ) ) {
						foreach ( $moralis_tx_data['logs'] as $log ) {
							if ( isset( $log['topic0'] ) ) {
								if ( $item_bought_event_hash === $log['topic0'] ) {
									if ( isset( $log['topic1'] ) && isset( $log['topic2'] ) && isset( $log['topic3'] ) ) {
										$padded_new_owner              = $log['topic1'];
										$new_owner                     = $this->remove_zero_padding_from_eth_address( $padded_new_owner );
										$padded_log_collection_address = $log['topic2'];
										$log_collection_address        = $this->remove_zero_padding_from_eth_address( $padded_log_collection_address );
										$padded_token_id               = $log['topic3'];
										break;
									} else {
										$error = new WP_Error( 'Invalid', 'Invalid Transaction Log', 'Transaction log received from Moralis is invalid. Cannot find NFT buy details.' );
										wp_send_json_error( $error );
									}
								}
							}
						}
						if ( ! empty( $log_collection_address ) && $contract_address === $log_collection_address && ! empty( $token_id ) ) {
							// need discussion regarding DB.
							update_post_meta( $token_id, '_monft_nft_listed', 'no' );
							$listed_orders = array();
							$listed_orders = $mo_nft_util->get_option( 'monft_listed_nft_order_ids' );
							unset( $listed_orders[ $token_id ] );
							$mo_nft_util->update_option( 'monft_listed_nft_order_ids', $listed_orders );
							$prod_info      = array(
								'prod_id' => $token_id,
							);
							$json_prod_info = wp_json_encode( $prod_info );
							wp_send_json( $json_prod_info );
						}
					}
				}
			}
		}
		/**
		 * Function to get listed NFT voucher
		 *
		 * @return void
		 */
		public function get_listed_voucher() {
			if ( isset( $_POST['tokenId'] ) && isset( $_POST['mo_nft_verify_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_nft_verify_nonce'] ) ), 'mo_nft_wp_nonce' ) ) {
				$voucher = get_post_meta( sanitize_text_field( wp_unslash( $_POST['tokenId'] ) ), '_monft_listed_signed_voucher' );
					wp_send_json( $voucher );
			}
		}
	}
}

