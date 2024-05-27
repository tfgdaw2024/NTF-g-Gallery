<?php
/**
 * File containing class that handles the actions to be taken after an NFT transfer.
 *
 * @package MoNft\controller
 */

namespace MoNft\controller;

use MoNft\Utils;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MoNft\controller\Transferhandler' ) ) {
	/**
	 * Class constrolling the backend of minting in NFT Marketplace.
	 */
	class TransferHandler {
		/**
		 * This function fires certain hooks upon class creation.
		 */
		public function __construct() {
			// Hook to save new owner in database after NFT Buy.
			add_action( 'wp_ajax_monft_moralis_get_data', array( $this, 'moralis_get_data_handler' ) );
			add_action( 'wp_ajax_nopriv_monft_moralis_get_data', array( $this, 'moralis_get_data_handler' ) );

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
		 * Handler function for verifying transaction details through Moralis API and updating Token ID => Owner mapping.
		 */
		public function moralis_get_data_handler() {
			global $mo_nft_util;
			if ( isset( $_REQUEST['mo_nft_verify_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_nft_verify_nonce'] ) ), 'mo_nft_wp_nonce' ) && isset( $_REQUEST['request'] ) ) {
				$request                 = sanitize_text_field( wp_unslash( $_REQUEST['request'] ) );
				$tx_hash                 = isset( $_REQUEST['txHash'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['txHash'] ) ) : null;
				$token_id                = isset( $_REQUEST['tokenId'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tokenId'] ) ) : null;
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
				if ( 'monft_save_owner_after_trade' === $request ) {
					// In our case (Trade NFT) the 'topic2' index of the first log gives the account the tokens have been transferred to.
					if ( isset( $moralis_tx_data['logs'][0] ) && isset( $moralis_tx_data['logs'][0]['topic2'] ) ) {
						$padded_new_owner              = $moralis_tx_data['logs'][0]['topic2'];
						$new_owner                     = $this->remove_zero_padding_from_eth_address( $padded_new_owner );
						$tokenid_to_owner              = $mo_nft_util->get_option( 'monft_tokenid_to_owner', array() );
						$tokenid_to_owner[ $token_id ] = $new_owner;
						$update_result                 = $mo_nft_util->update_option( 'monft_tokenid_to_owner', $tokenid_to_owner );
					} else {
						$error = new WP_Error( 'Unreadable', 'Transaction Logs unreadable', 'Either the logs were empty or in an unsupported format' );
						wp_send_json_error( $error );
					}
				} elseif ( 'monft_save_owner_after_buy' === $request ) {
					// In case of Buy NFT, we will use the hash of the event signature to search for the correct log.
					$item_bought_event_hash = \MoNft\Constants::ITEM_BOUGHT_EVENT_HASH;
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
						if ( $contract_address === $log_collection_address ) {
							$tokenid_to_owner              = $mo_nft_util->get_option( 'monft_tokenid_to_owner', array() );
							$tokenid_to_owner[ $token_id ] = $new_owner;
							$update_result                 = $mo_nft_util->update_option( 'monft_tokenid_to_owner', $tokenid_to_owner );
						}
					} else {
						$error = new WP_Error( 'Unreadable', 'Transaction Logs unreadable', 'Either the logs were empty or in an unsupported format' );
						wp_send_json_error( $error );
					}
				}
			}
		}
	}
}
