<?php
/**
 * Core
 *
 * Create MoNft Branding Handler.
 *
 * @category   Common, Core
 * @package    MoNft\Controller
 * @author     miniOrange <info@xecurify.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoNft\controller;

use MoNft\controller\BulkUploadHandler;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MoNft\controller\BrandingHandler' ) ) {
	/**
	 * Class to Create MoNft Branding Handler.
	 *
	 * @category Common, Core
	 * @package  MoNft\Controller\BrandingHandler
	 * @author   miniOrange <info@xecurify.com>
	 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
	 * @link     https://miniorange.com
	 */
	class BrandingHandler {


		/**
		 * Contructor for BrandingHandler
		 */
		public function __construct() {
			$this->utils = new \MoNft\Utils();

			add_action( 'admin_init', array( $this, 'save_nft_marketplace_branding_details' ) );

		}


		/**
		 * Save NFT Marketplace Branding Details
		 */
		public function save_nft_marketplace_branding_details() {
			global $mo_nft_util;
			$user_id = null;
			if ( current_user_can( 'administrator' ) ) {
				if ( isset( $_POST['mo_branding_upload_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_branding_upload_nonce'] ) ), 'branding_upload' ) ) {

					$upload_handler = new BulkUploadHandler();

					// Handle the file upload.
					$api_key_exists = $upload_handler->check_admin_key();
					if ( ! $api_key_exists ) {
						return;
					}

					$mo_nft_collection = $mo_nft_util->get_option( 'mo_nft_token_config_details_store' );

					if ( ! empty( $mo_nft_collection ) ) {
						$user_id = get_current_user_id();
						if ( isset( $_FILES['banner_image'] ) && ! empty( $_FILES['banner_image']['name'] ) ) {
							$extension_array    = array( 'jpg', 'jpeg', 'png' );
							$upload_file_object = $upload_handler->check_and_upload_files( 'banner_image', $extension_array );
							$movefile           = $upload_file_object['movefile'];
							$user_id            = get_current_user_id();
							update_user_meta( $user_id, 'mo_nft_collection_banner_image', $movefile['url'] );
						}

						if ( isset( $_FILES['profile_image'] ) && ! empty( $_FILES['profile_image']['name'] ) ) {
							$extension_array    = array( 'jpg', 'jpeg', 'png' );
							$upload_file_object = $upload_handler->check_and_upload_files( 'profile_image', $extension_array );
							$movefile           = $upload_file_object['movefile'];
							update_user_meta( $user_id, 'mo_nft_collection_profile_image', $movefile['url'] );
						}
						$nft_collection_name        = isset( $_POST['collection_name'] ) ? sanitize_text_field( wp_unslash( $_POST['collection_name'] ) ) : null;
						$nft_collection_description = isset( $_POST['collection_description'] ) ? sanitize_text_field( wp_unslash( $_POST['collection_description'] ) ) : null;
						update_user_meta( $user_id, 'mo_nft_collection_name', $nft_collection_name );
						update_user_meta( $user_id, 'mo_nft_collection_description', $nft_collection_description );
						$mo_nft_util->update_option(
							\MoNft\Constants::PANEL_MESSAGE_OPTION,
							'Branding details uploaded successfully'
						);
						$mo_nft_util->show_success_message();

					} else {
						$mo_nft_util->update_option(
							\MoNft\Constants::PANEL_MESSAGE_OPTION,
							'Please configure the contract details first to configure the branding details'
						);
						$mo_nft_util->show_error_message();
					}
				}
			}
		}

	}
}
