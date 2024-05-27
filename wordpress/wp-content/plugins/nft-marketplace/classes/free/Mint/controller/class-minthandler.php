<?php
/**
 * File containing class that handles minting process in NFT Marketplace.
 *
 * @package MoNft\controller
 */

namespace MoNft\controller;

use MoNft\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'MoNft\controller\MintHandler' ) ) {
	/**
	 * Class constrolling the backend of minting in NFT Marketplace.
	 */
	class MintHandler {
		/**
		 * This function fires certain hooks upon class creation.
		 */
		public function __construct() {
			global $mo_nft_util;
			add_action( 'wp_ajax_monft_import_collection_details', array( $this, 'import_collection_details' ) );
			add_action( 'wp_ajax_monft_free_settings', array( $this, 'free_settings' ) );
			add_action( 'wp_ajax_monft_import_collection', array( $this, 'import_collection' ) );
			add_action( 'wp_ajax_monft_deploy_contract', array( $this, 'deploy_contract' ) );
			add_action( 'wp_ajax_monft_mint_nft', array( $this, 'mint_nft' ) );
			add_action( 'wp_ajax_nopriv_monft_mint_nft', array( $this, 'mint_nft' ), 1 );
			add_action( 'wp_ajax_monft_api_minting_product', array( $this, 'api_minting_product' ) );
			add_action( 'wp_ajax_nopriv_monft_api_minting_product', array( $this, 'api_minting_product' ), 1 );
			add_action( 'wp_ajax_monft_redirect_users', array( $this, 'redirect_users' ) );
			add_action( 'wp_ajax_nopriv_monft_redirect_users', array( $this, 'redirect_users' ), 1 );
		}
		/**
		 * Update NFT stock to 1 once minting is done.
		 *
		 * @param post_id $post_id The post id of the current product.
		 */
		public function monft_update_stock( $post_id ) {
			update_post_meta( $post_id, '_stock', '1' );
			update_post_meta( $post_id, '_stock_status', 'instock' );
		}
		/**
		 * Check if product is valid
		 *
		 * @param post_id $post_id The post id of the current post.
		 */
		public function monft_validate_product_fields( $post_id ) {

			$product    = wc_get_product( $post_id );
			$sale_price = $product->get_sale_price();
			$image      = $product->get_image_id();
			if ( '' === $sale_price || '' === $image ) {
				$response = array(
					'edit_page' => admin_url() . 'post.php?post=' . $post_id . '&action=edit',
				);
				$response = wp_json_encode( $response );
				wp_send_json_error( $response );
			} else {
				wp_send_json( 'true' );
			}
		}
		/**
		 * Finding the home url and then appending the my nft profile page
		 */
		public function redirect_users() {
			if ( isset( $_POST['mo_nft_verify_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_nft_verify_nonce'] ) ), 'mo_nft_wp_nonce' ) ) {
				$redirect_url = home_url( '/my-nft-profile' );
				wp_send_json( $redirect_url );
			}
		}

		/**
		 * Add query argument to catch at admin notice hook
		 *
		 * @param Location $location The location to redirect to.
		 */
		public function monft_add_notice_query_var( $location ) {

			remove_filter( 'redirect_post_location', array( $this, 'add_notice_query_var' ), 99 );
			return add_query_arg( array( 'mo_nft_errorinnft' => 'mo_nft_validationerror' ), $location );

		}

		/**
		 * Function to handle mint on marketplace
		 *
		 * @return void
		 */
		public function mint_nft() {
			global $mo_nft_util;
			if ( isset( $_POST['mo_nft_verify_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_nft_verify_nonce'] ) ), 'mo_nft_wp_nonce' ) ) {
				$post_id          = isset( $_POST['monft_postId'] ) ? sanitize_text_field( wp_unslash( $_POST['monft_postId'] ) ) : null;
				$attachment_id    = get_post_meta( $post_id, '_thumbnail_id', true );
				$attachment_url   = wp_get_attachment_url( $attachment_id );
				$image_attributes = get_post_meta( $post_id, '_product_attributes' );
				$colleciton_name  = get_post_meta( $post_id, '_monft_nft_contract_address' );

				foreach ( $image_attributes as $key => $value ) {
					foreach ( $image_attributes[ $key ]['name'] as $index => $value ) {
						$data[] = array(
							'trait_type' => $image_attributes[ $key ]['name'][ $index ],
							'value'      => $image_attributes[ $key ]['value'][ $index ],
						);
					}
				}
				$media_body = $this->monft_ipfs_media_upload( $attachment_url );
				$this->monft_ipfs_metadata_upload_marketplace( $data, $media_body, $colleciton_name, $post_id );
			}
		}

		/**
		 * Upload file to ipfs and return cid
		 *
		 * @param media $media File for ipfs upload.
		 */
		public function monft_ipfs_media_upload( $media ) {

			$file         = wp_remote_get( $media, 'r' );
			$content_type = wp_remote_retrieve_header( $file, 'content-type' );

			if ( is_wp_error( $file ) ) {
				wp_send_json_error( 'no image found' );
				exit();

			}

			$headers = array(
				'Content-Type'  => 'image/jpeg',
				'Authorization' => \MoNft\Constants::NFTS_AUTH_TOKEN,
			);

			$args = array(
				'method'      => 'POST',
				'body'        => $file['body'],
				'timeout'     => '30',
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $headers,
				'sslverify'   => true,
			);

			$response = wp_remote_post( \MoNft\Constants::NFT_STORAGE_ENDPOINT, $args );

			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				wp_send_json_error( $error_message );
				exit();
			}

			$res = wp_remote_retrieve_body( $response );

			$media_body = json_decode( $res, true );

			if ( ! isset( $media_body['value'] ) ) {
				wp_send_json_error( 'NFT Storage API call malfunction (media)!' );
				exit();
			}

			if ( ! isset( $media_body['value']['cid'] ) ) {
				wp_send_json_error( 'NFT Storage API call malfunction (media)!' );
				exit();
			}

			return $media_body;
		}

		/**
		 * Upload metadata to ipfs and return cid on marketplace
		 *
		 * @param data            $data Data.
		 * @param media_body      $media_body Content of the nft to be uploaded to ipfs.
		 * @param colleciton_name $colleciton_name Collection name.
		 * @param post_id         $post_id Order id of current product.
		 */
		public function monft_ipfs_metadata_upload_marketplace( $data, $media_body, $colleciton_name, $post_id ) {
			$media_cid  = $media_body['value']['cid'];
			$post_title = get_the_title( $post_id );
			// Get the description of the NFT post.
			$post_object            = get_post( $post_id );
			$post_description       = $post_object->post_content;
			$product_metadata_array = array(
				'attributes'  => $data,
				'description' => $post_description,
				'image'       => 'ipfs://' . $media_cid,
				'name'        => $post_title,
			);

			$product_metadata_json = wp_json_encode( $product_metadata_array );

			$headers = array(
				'Content-Type'  => 'application/json',
				'Authorization' => \MoNft\Constants::NFTS_AUTH_TOKEN,
			);

			$args = array(
				'method'      => 'POST',
				'body'        => $product_metadata_json,
				'timeout'     => '30',
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $headers,
				'sslverify'   => true,
			);

			$response = wp_remote_post( \MoNft\Constants::NFT_STORAGE_ENDPOINT, $args );

			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				wp_send_json_error( $error_message );
				exit();
			}
			$res = wp_remote_retrieve_body( $response );

			$metadata_body = json_decode( $res, true );

			if ( ! isset( $metadata_body['value'] ) ) {
				wp_send_json_error( 'NFT Storage API call malfunction (metadata)!' );
				exit();
			}

			if ( ! isset( $metadata_body['value']['cid'] ) ) {
				wp_send_json_error( 'NFT Storage API call malfunction (metadata)!' );
				exit();
			}

			$product_metadata_cid = $metadata_body['value']['cid'];
			update_post_meta( $post_id, 'monft_metadata_cid', $product_metadata_cid );

			$json_cid_metadata = array(
				'cid'             => $product_metadata_cid,
				'prod_id'         => $post_id,
				'attributes'      => $data,
				'collection_name' => $colleciton_name,
			);

			$json_cid_metadata = wp_json_encode( $json_cid_metadata );
			wp_send_json( $json_cid_metadata );

		}
		/**
		 * Function to add configuration details to database.
		 *
		 * @return void
		 */
		public function free_settings() {
			if ( current_user_can( 'administrator' ) ) {
				if ( isset( $_REQUEST['mo_nft_verify_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_nft_verify_nonce'] ) ), 'mo_nft_wp_nonce' ) && isset( $_REQUEST['request'] ) ) {

					global $mo_nft_util;

					$request       = sanitize_text_field( wp_unslash( $_REQUEST['request'] ) );
					$token_details = isset( $_REQUEST['tokenDetails'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['tokenDetails'] ) ) : null;
					if ( 'addTokenDetails' === $request ) {

						$this->add_token_details( $token_details );

					}
					if ( 'deleteTokenDetails' === $request ) {

						$this->delete_token_details( $token_details );

					}
					if ( 'editTokenDetails' === $request ) {

						$this->update_token_details( $token_details );

					}
				}
			}

		}

		/**
		 * Function to add configuration details to database.
		 *
		 * @return void
		 */
		public function import_collection_details() {
			if ( current_user_can( 'administrator' ) ) {
				if ( isset( $_REQUEST['mo_nft_verify_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_nft_verify_nonce'] ) ), 'mo_nft_wp_nonce' ) && isset( $_REQUEST['request'] ) ) {

					$request            = sanitize_text_field( wp_unslash( $_REQUEST['request'] ) );
					$collection_details = isset( $_REQUEST['collectionDetails'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['collectionDetails'] ) ) : null;

					if ( 'getCollectionName' === $request ) {

						$headers = array(
							'Accept'    => 'application/json',
							'X-API-Key' => \MoNft\Constants::MORALIS_API_KEY,
						);

						$args = array(
							'method'      => 'GET',
							'timeout'     => '30',
							'redirection' => '5',
							'httpversion' => '1.0',
							'blocking'    => true,
							'headers'     => $headers,
							'sslverify'   => true,
						);

						$chain = null;
						if ( 'sepolia' === $collection_details['blockchain'] ) {
							$chain = 'sepolia';
						} else {
							$chain = 'mumbai';
						}

						$collection_sync_response = wp_remote_post( \MoNft\Constants::MORALI_ENDPONT . $collection_details['contractAddress'] . '/metadata/sync?chain=' . $chain, $args );
						if ( is_wp_error( $collection_sync_response ) ) {
							wp_send_json_error( 'API_ERROR' );
						} else {
							$collection_metadata_response = wp_remote_post( \MoNft\Constants::MORALI_ENDPONT . $collection_details['contractAddress'] . '/metadata?chain=' . $chain, $args );
							if ( is_wp_error( $collection_metadata_response ) ) {
								wp_send_json_error( 'API_ERROR' );
							}

							$res                     = wp_remote_retrieve_body( $collection_metadata_response );
							$metadata_body           = json_decode( $res, true );
							$metadata_body['status'] = 'SUCCESS';
							wp_send_json_success( $metadata_body );
						}
					}
				}
			}
		}
		/**
		 * Function to add token details.
		 *
		 * @param token_details $token_details Token details.
		 *
		 * @return void
		 */
		public function add_token_details( $token_details ) {

			global $mo_nft_util;
			$token_config_details = $mo_nft_util->get_option( 'mo_nft_token_config_details_store' );
			if ( $token_config_details ) {
				wp_send_json_error( 'RECORD_EXIST' );
			} else {
				$this->import_collection();
			}

		}
		/**
		 * Function to delete token details.
		 *
		 * @param token_details $token_details Token details.
		 *
		 * @return void
		 */
		public function delete_token_details( $token_details ) {

			global $mo_nft_util;

			$token_details = $token_details['contractAddress'];

			$token_config_details = $mo_nft_util->get_option( 'mo_nft_token_config_details_store' );

			foreach ( $token_details as $key => $value ) {

				$key                   = isset( $key ) ? sanitize_text_field( wp_unslash( $key ) ) : null;
				$value                 = isset( $value ) ? sanitize_text_field( wp_unslash( $value ) ) : null;
				$contract_address_name = $value;
				unset( $token_config_details[ $contract_address_name ] );
			}

			$mo_nft_util->update_option( 'mo_nft_token_config_details_store', $token_config_details );

			wp_send_json( 'success' );

		}
		/**
		 * Function to update token details.
		 *
		 * @param token_details $token_details Token details.
		 *
		 * @return void
		 */
		public function update_token_details( $token_details ) {
			global $mo_nft_util;

			$token_details_array = array();

			foreach ( $token_details as $key => $value ) {
				$key   = isset( $key ) ? sanitize_text_field( wp_unslash( $key ) ) : null;
				$value = isset( $value ) ? sanitize_text_field( wp_unslash( $value ) ) : null;

				$token_details_array[ $key ] = $value;
			}

			$token_config_details                           = $mo_nft_util->get_option( 'mo_nft_token_config_details_store' );
			$contract_address_name                          = $token_details_array['contractAddress'];
			$token_config_details[ $contract_address_name ] = $token_details_array;
			$mo_nft_util->update_option( 'mo_nft_token_config_details_store', $token_config_details );

			wp_send_json( 'success' );
		}

		/**
		 * Function to call API for deploying contract.
		 *
		 * @return void
		 */
		public function deploy_contract() {
			if ( current_user_can( 'administrator' ) ) {
				if ( isset( $_REQUEST['mo_nft_verify_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_nft_verify_nonce'] ) ), 'mo_nft_wp_nonce' ) && isset( $_REQUEST['request'] ) ) {
					global $mo_nft_util;
					$blockchain        = isset( $_REQUEST['blockchain'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['blockchain'] ) ) : null;
					$standard          = isset( $_REQUEST['standard'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['standard'] ) ) : null;
					$wallet_address    = isset( $_REQUEST['walletAddress'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['walletAddress'] ) ) : null;
					$blockchain        = isset( $_REQUEST['blockchain'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['blockchain'] ) ) : null;
					$collection_name   = isset( $_REQUEST['collectionName'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['collectionName'] ) ) : null;
					$collection_symbol = isset( $_REQUEST['collectionSymbol'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['collectionSymbol'] ) ) : null;
					$admin_api_key     = $mo_nft_util->get_option( 'mo_nft_admin_api_key' );
					$admin_email       = $mo_nft_util->get_option( 'mo_nft_admin_email' );
					if ( empty( $admin_api_key ) ) {
						wp_send_json_error( 'Please login through your account' );
					}

					$headers  = array(
						'Accept' => 'application/json',
					);
					$data     = array(
						'email'            => $admin_email,
						'apiKey'           => $admin_api_key,
						'walletId'         => $wallet_address,
						'standard'         => $standard,
						'uri'              => false,
						'blockchain'       => $blockchain,
						'collectionName'   => $collection_name,
						'collectionSymbol' => $collection_symbol,
					);
					$args     = array(
						'method'      => 'POST',
						'timeout'     => '40',
						'redirection' => '5',
						'httpversion' => '1.0',
						'blocking'    => true,
						'headers'     => $headers,
						'sslverify'   => false,
						'body'        => $data,
					);
					$response = wp_remote_post( \MoNft\Constants::DEPLOY_CONTRACT_ENDPOINT, $args );

					if ( is_wp_error( $response ) ) {
						$error_message = $response->get_error_message();
						wp_send_json_error( $error_message );
					}

					$res           = wp_remote_retrieve_body( $response );
					$metadata_body = json_decode( $res, true );
					wp_send_json( $metadata_body );
				}
			}
		}

		/**
		 * Function to call api for minting.
		 *
		 * @return void
		 */
		public function api_minting_product() {
			if ( isset( $_REQUEST['mo_nft_verify_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_nft_verify_nonce'] ) ), 'mo_nft_wp_nonce' ) && isset( $_REQUEST['request'] ) ) {
				global $mo_nft_util;
				$token_config_details = $mo_nft_util->get_option( 'mo_nft_token_config_details_store' );
				foreach ( $token_config_details as $key => $value ) {
					$contract_address = $token_config_details[ $key ]['contractAddress'];
					$contract_abi     = $token_config_details[ $key ]['contractABI'];
					$blockchain       = $token_config_details[ $key ]['blockchain'];
				}
				$admin_api_key = $mo_nft_util->get_option( 'mo_nft_admin_api_key' );
				$admin_email   = $mo_nft_util->get_option( 'mo_nft_admin_email' );
				$token_id      = isset( $_REQUEST['monft_postId'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['monft_postId'] ) ) : null;
				$token_id      = (int) $token_id;
				$wallet_id     = isset( $_REQUEST['wallet_id'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wallet_id'] ) ) : null;
				$token_uri     = isset( $_REQUEST['token_uri'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['token_uri'] ) ) : null;
				$headers       = array(
					'Content-Type' => 'application/json',
				);
				$data          = array(
					'email'           => $admin_email,
					'apiKey'          => $admin_api_key,
					'walletId'        => $wallet_id,
					'tokenId'         => $token_id,
					'tokenUri'        => $token_uri,
					'contractAddress' => $contract_address,
					'contractAbi'     => $contract_abi,
					'blockchain'      => $blockchain,
				);
				$args          = array(
					'method'      => 'POST',
					'timeout'     => '30',
					'redirection' => '5',
					'httpversion' => '1.0',
					'blocking'    => true,
					'headers'     => $headers,
					'sslverify'   => false,
					'body'        => wp_json_encode( $data ),
				);
				$response      = wp_remote_post( \MoNft\Constants::MINT_NFT_ENDPOINT, $args );
				if ( is_wp_error( $response ) ) {
					$error_message = $response->get_error_message();
					wp_send_json_error( $error_message );
				} elseif ( 401 === $response['response']['code'] ) {
					wp_send_json_error( $response['response']['message'] );
				} elseif ( 400 === $response['response']['code'] ) {
					wp_send_json_error( $response['response']['message'] );
				} else {
					$tokenid_to_owner              = get_option( 'monft_tokenid_to_owner', array() );
					$tokenid_to_owner[ $token_id ] = $wallet_id;
					$mo_nft_util->update_option( 'monft_tokenid_to_owner', $tokenid_to_owner );
					$res           = wp_remote_retrieve_body( $response );
					$metadata_body = json_decode( $res, true );
					wp_delete_post( $token_id, true );
					wp_send_json( $metadata_body );
				}
			}
		}
		/**
		 * Function to re import or re deploy collection
		 *
		 * @return void
		 */
		public function import_collection() {
			if ( current_user_can( 'administrator' ) ) {
				if ( isset( $_REQUEST['mo_nft_verify_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['mo_nft_verify_nonce'] ) ), 'mo_nft_wp_nonce' ) && isset( $_REQUEST['request'] ) ) {
					$token_details = isset( $_REQUEST['tokenDetails'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['tokenDetails'] ) ) : null;
					global $mo_nft_util;

					$token_details_array = array();

					foreach ( $token_details as $key => $value ) {
						$key   = isset( $key ) ? sanitize_text_field( wp_unslash( $key ) ) : null;
						$value = isset( $value ) ? sanitize_text_field( wp_unslash( $value ) ) : null;

						$token_details_array[ $key ] = $value;
					}

					$token_config_details  = $mo_nft_util->get_option( 'mo_nft_token_config_details_store' );
					$contract_address_name = $token_details_array['contractAddress'];

					if ( ! $token_config_details ) {
						$token_config_details = array();
					} else {
						$mo_nft_util->delete_option( 'mo_nft_token_config_details_store' );
						$mo_nft_util->delete_option( 'monft_tokenid_to_owner' );
						$token_config_details                           = array();
						$token_config_details[ $contract_address_name ] = $token_details_array;
						$branding_collection_name                       = $token_config_details[ $contract_address_name ]['collectionName'];
						$mo_nft_util->update_option( 'mo_nft_token_config_details_store', $token_config_details );
						$user_id = get_current_user_id();
						update_user_meta( $user_id, 'mo_nft_collection_name', $branding_collection_name );
						wp_send_json_error( 'RECORD_UPDATED' );
					}

					if ( array_key_exists( $contract_address_name, $token_config_details ) ) {
						wp_send_json( 'DUPLICATE_ENTRY' );
					}
					$token_config_details[ $contract_address_name ] = $token_details_array;
					$branding_collection_name                       = $token_config_details[ $contract_address_name ]['collectionName'];
					$mo_nft_util->update_option( 'mo_nft_token_config_details_store', $token_config_details );
					$user_id = get_current_user_id();
					update_user_meta( $user_id, 'mo_nft_collection_name', $branding_collection_name );

					wp_send_json( 'SUCCESS' );
				}
			}
		}
	}
}

