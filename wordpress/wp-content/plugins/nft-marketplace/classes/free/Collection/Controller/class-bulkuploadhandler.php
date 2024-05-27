<?php
/**
 * Core
 *
 * Create MoNft bulkupload Handler.
 *
 * @category   Common, Core
 * @package    MoNft\Controller
 * @author     miniOrange <info@xecurify.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoNft\controller;

use ZipArchive;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MoNft\controller\BulkUploadHandler' ) ) {
	/**
	 * Class to Create MoNft Bulk Upload Handler.
	 *
	 * @category Common, Core
	 * @package  MoNft\Controller\BulkUploadHandler
	 * @author   miniOrange <info@xecurify.com>
	 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
	 * @link     https://miniorange.com
	 */
	class BulkUploadHandler {

		/**
		 * Contructor for BulkUploadHandler
		 */
		public function __construct() {

			$this->utils = new \MoNft\Utils();

			add_action( 'admin_init', array( $this, 'create_bulk_products_as_nfts' ) );
			add_action( 'admin_init', array( $this, 'create_single_product_as_nft' ) );

		}

		/**
		 * Function to check whether admin API key exists.
		 */
		public function check_admin_key() {
			global $mo_nft_util;
			$admin_api_key = $mo_nft_util->get_option( 'mo_nft_admin_api_key' );
			if ( empty( $admin_api_key ) ) {
				$mo_nft_util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'Please login through your account' );
				$mo_nft_util->show_error_message();
				return false;
			}
			return true;
		}

		/**
		 * Function that checks the $_FILE object and uploads using wp_handle_upload.
		 *
		 * @param file_id         $file_id The id of the input element in the html form.
		 * @param extension_array $extension_array The list of extensions that are valid for this upload.
		 */
		public function check_and_upload_files( $file_id, $extension_array ) {
			$file_info = isset( $_FILES[ $file_id ]['name'] ) ? wp_check_filetype( basename( sanitize_file_name( wp_unslash( $_FILES[ $file_id ]['name'] ) ) ) ) : null;
			if ( ! empty( $file_info['ext'] ) && in_array( $file_info['ext'], $extension_array, true ) ) {
				$uploaded_file = $this->sanitize_callback( $_FILES[ $file_id ] ); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitization is done with function sanitize_callback.
			}
			$upload_overrides = array( 'test_form' => false );
			$movefile         = wp_handle_upload( $uploaded_file, $upload_overrides );
			$return_array     = array(
				'movefile'      => $movefile,
				'uploaded_file' => $uploaded_file,
			);
			return $return_array;
		}

		/**
		 * Function that creates a monft_product post.
		 *
		 * @param nft_name        $nft_name The name of the post that depicts the NFT.
		 * @param nft_description $nft_description The description (post content) of the NFT.
		 */
		public function create_nft_post( $nft_name, $nft_description ) {
			$new_nft     = array(
				'post_title'   => $nft_name,
				'post_content' => $nft_description,
				'post_status'  => 'publish',
				'post_type'    => 'monft_product',
			);
			$new_nft     = apply_filters(
				'mo_nft_create_woocommerce_product',
				$new_nft,
				$nft_name,
				$nft_description
			);
			$nft_post_id = wp_insert_post( $new_nft );
			return $nft_post_id;
		}

		/**
		 * Function to upload image to Uploads folder and create attachment.
		 *
		 * @param upload           $upload The object returned from wp_upload_bits.
		 * @param nft_name         $nft_name The name of the post that depicts the NFT.
		 * @param nft_post_id      $nft_post_id The post id of the post depicting the NFT.
		 * @param contract_address $contract_address The contract address of the NFT Collection.
		 */
		public function upload_and_attach( $upload, $nft_name, $nft_post_id, $contract_address ) {
			if ( ! $upload['error'] ) {
				$attachment_id = wp_insert_attachment(
					array(
						'post_mime_type' => wp_check_filetype( $upload['file'], null )['type'],
						'post_title'     => $nft_name,
						'post_name'      => $nft_name,
						'post_content'   => '',
						'post_status'    => 'inherit',
						'post_type'      => 'attachment',
						'guid'           => $upload['url'],
					),
					$upload['file']
				);

				if ( ! is_wp_error( $attachment_id ) ) {
					// Set the product details.
					if ( $nft_post_id ) {
						update_post_meta( $nft_post_id, '_thumbnail_id', $attachment_id );
						update_post_meta( $nft_post_id, '_monft_nft', 'yes' );
						update_post_meta( $nft_post_id, '_monft_nft_contract_address', $contract_address );
					}
				}
			}
		}

		/**
		 * Create products out of a zip file according to the Bulk Upload Feature.
		 */
		public function create_bulk_products_as_nfts() {
			global $mo_nft_util;
			if ( current_user_can( 'administrator' ) ) {
				if ( isset( $_POST['mo_nft_upload_zip_file_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_nft_upload_zip_file_nonce'] ) ), 'upload_zip_file' ) ) {
					// Handle the file upload.
					$api_key_exists = $this->check_admin_key();
					if ( ! $api_key_exists ) {
						return;
					}
					$mo_nft_collection = $mo_nft_util->get_option( 'mo_nft_token_config_details_store' );
					foreach ( $mo_nft_collection as $key => $value ) {
						$contract_address = $mo_nft_collection[ $key ]['contractAddress'];
					}
					if ( ! empty( $mo_nft_collection ) ) {
						if ( isset( $_FILES['zip_file'] ) && ! empty( $_FILES['zip_file']['name'] ) ) {
							if ( isset( $_POST['collection_dropdown'] ) ) {
								$collection_name = sanitize_text_field( wp_unslash( $_POST['collection_dropdown'] ) );
							}
							$extension_array    = array( 'zip' );
							$upload_file_object = $this->check_and_upload_files( 'zip_file', $extension_array );
							$movefile           = $upload_file_object['movefile'];
							$csv_file_info      = isset( $_FILES['csv_file']['name'] ) ? wp_check_filetype( basename( sanitize_file_name( wp_unslash( $_FILES['csv_file']['name'] ) ) ) ) : null;
							if ( ! empty( $csv_file_info['ext'] ) && 'csv' === $csv_file_info['ext'] ) {
								$csv_file = isset( $_FILES['csv_file']['tmp_name'] ) ? $_FILES['csv_file']['tmp_name'] : null; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitization for path name is already handled by php. 
							}
							if ( class_exists( 'ZipArchive' ) ) {
								WP_Filesystem();
								// Read the file using the WordPress Filesystem API.
								global $wp_filesystem;
								$file_path    = $csv_file;
								$file_content = $wp_filesystem->get_contents( $file_path );
								$rows         = explode( "\n", $file_content );
								$zip          = new ZipArchive();
								$i            = 0;
								if ( $zip->open( $movefile['file'] ) === true ) {
									foreach ( $rows as $row ) {
										$filename   = $zip->getNameIndex( $i );
										$image_data = $zip->getFromName( $filename );
										$i++;
										$data                = str_getcsv( $row, ',' );
										$product_name        = $data[0];
										$product_description = $data[1];
										$product_attributes  = explode( '|', $data[2] );
										$product_id          = $this->create_nft_post( $product_name, $product_description );
										$attributes          = array();
										$attribute_name      = array();
										$attribute_value     = array();
										if ( $product_attributes ) {
											foreach ( $product_attributes as $attribute ) {
												$attribute_name_value = explode( ':', $attribute );
												$attribute_name[]     = trim( $attribute_name_value[0] );
												$attribute_value[]    = trim( $attribute_name_value[1] );
												$attributes           = array(
													'name' => $attribute_name,
													'value' => $attribute_value,
													'is_visible' => 1,
													'position' => 0,
													'is_taxonomy' => 0,
												);
											}
											if ( $attribute_name && $attribute_value ) {
												update_post_meta( $product_id, '_product_attributes', $attributes );
											}
										}
										$upload = wp_upload_bits( $filename, null, $image_data );
										$this->upload_and_attach( $upload, $product_name, $product_id, $contract_address );
									}
									$zip->close();
									$mo_nft_util->update_option(
										\MoNft\Constants::PANEL_MESSAGE_OPTION,
										'NFT Metadata uploaded successfully'
									);
									$mo_nft_util->show_success_message();
								} else {
									$mo_nft_util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'Failed to open zip file' );
									$mo_nft_util->show_error_message();
								}
							} else {
								$mo_nft_util->update_option(
									\MoNft\Constants::PANEL_MESSAGE_OPTION,
									'You need to enable the ZipArchive extension. You can follow the steps to enable the extension using this link: ' . \MoNft\Constants::FAQ_ZIP_EXTENSION_LINK
								);
								$mo_nft_util->show_error_message();
							}
						}
					} else {
						$mo_nft_util->update_option(
							\MoNft\Constants::PANEL_MESSAGE_OPTION,
							'Please configure the contract details first to upload NFTs for minting'
						);
						$mo_nft_util->show_error_message();
					}
				}
			}
		}
		/**
		 * Function to sanitize array except path
		 *
		 * @param _file_array $_file_array array of zip file.
		 * @return _file_array
		 */
		public function sanitize_callback( $_file_array ) {

			foreach ( $_file_array as $key => $value ) {
				if ( 'tmp_name' === $key ) {
					sanitize_file_name( $value );
				}
				sanitize_text_field( $value );
			}
			return $_file_array;

		}
		/**
		 * Function to create single nft product
		 *
		 * @return void
		 */
		public function create_single_product_as_nft() {
			global $mo_nft_util;
			if ( current_user_can( 'administrator' ) ) {
				if ( isset( $_POST['mo_nft_upload_single_file_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_nft_upload_single_file_nonce'] ) ), 'upload_single_file' ) ) {
					$api_key_exists = $this->check_admin_key();
					if ( ! $api_key_exists ) {
						return;
					}
					$mo_nft_collection = $mo_nft_util->get_option( 'mo_nft_token_config_details_store' );
					foreach ( $mo_nft_collection as $key => $value ) {
						$contract_address = $mo_nft_collection[ $key ]['contractAddress'];
					}
					$img_file_info = isset( $_FILES['image_file']['name'] ) ? wp_check_filetype( basename( sanitize_file_name( wp_unslash( $_FILES['image_file']['name'] ) ) ) ) : null;
					if ( ! empty( $mo_nft_collection ) ) {
						if ( isset( $_FILES['image_file'] ) && ! empty( $_FILES['image_file']['name'] ) ) {
							$extension_array    = array( 'jpg', 'jpeg', 'png' );
							$upload_file_object = $this->check_and_upload_files( 'image_file', $extension_array );
							$uploaded_file      = $upload_file_object['uploaded_file'];
							$movefile           = $upload_file_object['movefile'];
							$file_name          = basename( $uploaded_file['name'] );
							$product_name       = pathinfo( $file_name, PATHINFO_FILENAME );
							// Create a new product for each image.
							$nft_name             = isset( $_POST['nft_name'] ) ? sanitize_text_field( wp_unslash( $_POST['nft_name'] ) ) : null;
							$nft_description      = isset( $_POST['monft_single_nft_description'] ) ? sanitize_text_field( wp_unslash( $_POST['monft_single_nft_description'] ) ) : '';
							$product_id           = $this->create_nft_post( $nft_name, $nft_description );
							$attributes           = array();
							$nft_attributes_name  = isset( $_POST['nft_attributes_name'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['nft_attributes_name'] ) ) : null;
							$nft_attributes_value = isset( $_POST['nft_attributes_value'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['nft_attributes_value'] ) ) : null;
							$attributes           = array(
								'name'        => $nft_attributes_name,
								'value'       => $nft_attributes_value,
								'is_visible'  => 1,
								'position'    => 0,
								'is_taxonomy' => 0,
							);
							update_post_meta( $product_id, '_product_attributes', $attributes );

							$this->upload_and_attach( $movefile, $product_name, $product_id, $contract_address );
							$mo_nft_util->update_option(
								\MoNft\Constants::PANEL_MESSAGE_OPTION,
								'NFT Metadata uploaded successfully'
							);
							$mo_nft_util->show_success_message();

						}
					} else {
						$mo_nft_util->update_option(
							\MoNft\Constants::PANEL_MESSAGE_OPTION,
							'Please configure the contract details first to upload NFTs for minting'
						);
						$mo_nft_util->show_error_message();
					}
				}
			}
		}

	}
}
