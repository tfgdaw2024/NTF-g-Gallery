<?php
/**
 * App
 *
 * NFT Common AccountsHandler.
 *
 * @category   Common, Core
 * @package    MoNft\AccountsHandler
 * @author     miniOrange <info@xecurify.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoNft;

use MoNft\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MoNft\AccountsHandler' ) ) {
	/**
	 * Class for NFT Settings.
	 *
	 * @category Common, Core
	 * @package  MoNft\Accounts\AccountsHandler
	 * @author   miniOrange <info@xecurify.com>
	 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
	 * @link     https://miniorange.com
	 */
	class AccountsHandler {

		/**
		 * NFT Marketplace Plugin Configuration
		 *
		 * @var Array $config
		 * */
		public $config;

		/**
		 * NFT utils
		 *
		 * @var \MoNft\mo_nft_utils $util
		 * */
		public $util;
		/**
		 * Constructor.
		 */
		public function __construct() {

			global $mo_nft_util;
			$this->util = $mo_nft_util;
			add_action( 'admin_init', array( $this, 'miniorange_nft_save_settings' ) );
			$this->config = $this->util->get_plugin_config();
		}



		/**
		 * Saves Settings.
		 *
		 * @return void
		 */
		public function miniorange_nft_save_settings() {
			if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) && current_user_can( 'administrator' ) ) {

				if ( isset( $_POST['mo_nft_change_miniorange_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_nft_change_miniorange_nonce'] ) ), 'mo_nft_change_miniorange' ) && isset( $_POST[ \MoNft\Constants::OPTION ] ) && 'mo_nft_change_miniorange' === sanitize_text_field( wp_unslash( $_POST[ \MoNft\Constants::OPTION ] ) ) ) {
					mo_nft_deactivate();
					return;
				}
				if ( isset( $_POST['mo_nft_account_error_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_nft_account_error_nonce'] ) ), 'mo_nft_account_error' ) && isset( $_POST[ \MoNft\Constants::OPTION ] ) && 'mo_nft_account_error' === sanitize_text_field( wp_unslash( $_POST[ \MoNft\Constants::OPTION ] ) ) ) {
					$this->util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'Please login through your account' );
					$this->util->show_error_message();
					return;
				}
				if ( isset( $_POST['mo_nft_branding_image_size_error_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_nft_branding_image_size_error_nonce'] ) ), 'mo_nft_branding_image_size_error' ) && isset( $_POST[ \MoNft\Constants::OPTION ] ) && 'mo_nft_branding_image_size_error' === sanitize_text_field( wp_unslash( $_POST[ \MoNft\Constants::OPTION ] ) ) ) {
					$this->util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'Please upload an image with a 16:9 aspect ratio for banner image.' );
					$this->util->show_error_message();
					return;
				}
				if ( isset( $_POST['mo_nft_import_success_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_nft_import_success_nonce'] ) ), 'mo_nft_import_success' ) && isset( $_POST[ \MoNft\Constants::OPTION ] ) && 'mo_nft_import_success' === sanitize_text_field( wp_unslash( $_POST[ \MoNft\Constants::OPTION ] ) ) ) {
					$this->util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'Successfully imported collection, you can upload more NFTs to your collection or else you can visit marketplace' );
					$this->util->show_success_message();
					return;
				}
				if ( isset( $_POST['mo_nft_deploy_success_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_nft_deploy_success_nonce'] ) ), 'mo_nft_deploy_success' ) && isset( $_POST[ \MoNft\Constants::OPTION ] ) && 'mo_nft_deploy_success' === sanitize_text_field( wp_unslash( $_POST[ \MoNft\Constants::OPTION ] ) ) ) {
					$this->util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'Successfully deployed your collection, you can upload more NFTs to your collection. Please upload NFTs to your collection for minting.' );
					$this->util->show_success_message();
					return;
				}

				if ( current_user_can( 'administrator' ) && isset( $_POST['mo_nft_verify_customer_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_nft_verify_customer_nonce'] ) ), 'mo_nft_verify_customer' ) && isset( $_POST[ \MoNft\Constants::OPTION ] ) && 'mo_nft_verify_customer' === sanitize_text_field( wp_unslash( $_POST[ \MoNft\Constants::OPTION ] ) ) ) {
					// login the admin to miniOrange.
					if ( 0 === $this->util->is_curl_installed() ) {
						return $this->util->show_curl_error();
					}
					// validation and sanitization.
					$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
					$password = isset( $_POST['password'] ) ? sanitize_text_field( wp_unslash( $_POST['password'] ) ) : '';
					if ( $this->util->check_empty_or_null( $email ) || $this->util->check_empty_or_null( $password ) ) {
						$this->util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'All the fields are required. Please enter valid entries.' );
						$this->util->show_error_message();
						return;
					}

					$this->util->update_option( 'mo_nft_admin_email', $email );
					$this->util->update_option( 'mo_nft_password', $password );
					$customer = new Customer();
					$content  = $customer->get_customer_key();

					$customer_key = json_decode( $content, true );
					if ( json_last_error() === JSON_ERROR_NONE ) {
						$this->util->update_option( 'mo_nft_admin_customer_key', $customer_key['id'] );
						$this->util->update_option( 'mo_nft_admin_api_key', $customer_key['apiKey'] );
						$this->util->update_option( 'mo_nft_customer_token', $customer_key['token'] );
						if ( isset( $customer_key['phone'] ) ) {
							$this->util->update_option( 'mo_nft_admin_phone', $customer_key['phone'] );
						}
						$result = $this->sync_account_with_dapp();
						if ( $result['status'] ) {
							$this->util->delete_option( 'mo_nft_password' );
							$this->util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'Customer retrieved successfully' );
							$this->util->delete_option( 'mo_nft_verify_customer' );
							$this->util->show_success_message();
						} else {
							$this->util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, $result['errorMessage'] );
							$this->util->show_error_message();
						}
					} else {
						$this->util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'Invalid username or password. Please try again.' );
						$this->util->show_error_message();
					}
				}

				if ( current_user_can( 'administrator' ) && isset( $_POST['mo_nft_contact_us_query_option_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_nft_contact_us_query_option_nonce'] ) ), 'mo_nft_contact_us_query_option' ) && isset( $_POST[ \MoNft\Constants::OPTION ] ) && 'mo_nft_contact_us_query_option' === sanitize_text_field( wp_unslash( $_POST[ \MoNft\Constants::OPTION ] ) ) ) {
					if ( 0 === $this->util->is_curl_installed() ) {
						return $this->util->show_curl_error();
					}
					// Contact Us query.
					$email    = isset( $_POST['mo_nft_contact_us_email'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_nft_contact_us_email'] ) ) : '';
					$phone    = isset( $_POST['mo_nft_contact_us_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_nft_contact_us_phone'] ) ) : '';
					$query    = isset( $_POST['mo_nft_contact_us_query'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_nft_contact_us_query'] ) ) : '';
					$customer = new Customer();
					if ( $this->util->check_empty_or_null( $email ) || $this->util->check_empty_or_null( $query ) ) {
						$this->util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'Please fill up Email and Query fields to submit your query.' );
						$this->util->show_error_message();
					} else {
						$send_config = false;
						$submited    = $customer->submit_contact_us( $email, $phone, $query, $send_config );
						if ( false === $submited ) {
							$this->util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'Your query could not be submitted. Please try again.' );
							$this->util->show_error_message();
						} else {
							$this->util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'Thanks for getting in touch! We shall get back to you shortly.' );
							$this->util->show_success_message();
						}
					}
				}

				if ( current_user_can( 'administrator' ) && isset( $_POST['mo_nft_register_customer_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_nft_register_customer_nonce'] ) ), 'mo_nft_register_customer' ) && isset( $_POST[ \MoNft\Constants::OPTION ] ) && 'mo_nft_register_customer' === sanitize_text_field( wp_unslash( $_POST[ \MoNft\Constants::OPTION ] ) ) ) {
					// register the admin to miniOrange
					// validation and sanitization.
					$email            = '';
					$phone            = '';
					$password         = '';
					$fname            = '';
					$lname            = '';
					$company          = '';
					$confirm_password = '';
					if ( ! isset( $_POST['email'] ) || ! filter_var( wp_unslash( $_POST['email'], FILTER_VALIDATE_EMAIL ) ) || ! isset( $_POST['password'] ) || ! isset( $_POST['confirmPassword'] ) || $this->util->check_empty_or_null( sanitize_text_field( wp_unslash( $_POST['email'] ) ) ) || $this->util->check_empty_or_null( sanitize_text_field( wp_unslash( $_POST['password'] ) ) ) || $this->util->check_empty_or_null( sanitize_text_field( wp_unslash( $_POST['confirmPassword'] ) ) ) ) {
						$this->util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'All the fields are required. Please enter valid entries.' );
						$this->util->show_error_message();
						return;
					}
					if ( strlen( sanitize_text_field( wp_unslash( $_POST['password'] ) ) ) < 8 || strlen( sanitize_text_field( wp_unslash( $_POST['confirmPassword'] ) ) ) < 8 ) {
						$this->util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'Choose a password with minimum length 8.' );
						$this->util->show_error_message();
						return;
					} else {

						$email            = filter_var( sanitize_email( wp_unslash( $_POST['email'] ) ), FILTER_SANITIZE_EMAIL );
						$phone            = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
						$password         = stripslashes( $_POST['password'] );//phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- As we are not storing password in the database, so we can ignore sanitization. Preventing use of sanitization in password will lead to removal of special characters.
						$fname            = isset( $_POST['fname'] ) ? sanitize_text_field( wp_unslash( $_POST['fname'] ) ) : '';
						$lname            = isset( $_POST['lname'] ) ? sanitize_text_field( wp_unslash( $_POST['lname'] ) ) : '';
						$company          = isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '';
						$confirm_password = stripslashes( $_POST['confirmPassword'] );//phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- As we are not storing password in the database, so we can ignore sanitization. Preventing use of sanitization in password will lead to removal of special characters.
					}

					$this->util->update_option( 'mo_nft_admin_email', $email );
					$this->util->update_option( 'mo_nft_admin_phone', $phone );
					$this->util->update_option( 'mo_nft_admin_fname', $fname );
					$this->util->update_option( 'mo_nft_admin_lname', $lname );
					$this->util->update_option( 'mo_nft_admin_company', $company );

					if ( $this->util->is_curl_installed() === 0 ) {// ?
						return $this->util->show_curl_error();
					}

					if ( strcmp( $password, $confirm_password ) === 0 ) {
						$this->util->update_option( 'mo_nft_password', $password );
						$customer = new Customer();
						$email    = $this->util->get_option( 'mo_nft_admin_email' );
						$content  = json_decode( $customer->check_customer(), true );
						if ( strcasecmp( $content['status'], 'CUSTOMER_NOT_FOUND' ) === 0 ) {
							$this->create_customer();
						} else {
							$this->mo_nft_get_current_customer();
						}
					} else {
						$this->util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'Passwords do not match.' );
						$this->util->update_option( 'mo_nft_verify_customer', false );
						$this->util->show_error_message();
					}
				}
			}
		}
		/**
		 * Function to get current customer info.
		 *
		 * @return void
		 */
		public function mo_nft_get_current_customer() {
			$customer     = new Customer();
			$content      = $customer->get_customer_key();
			$customer_key = json_decode( $content, true );
			if ( json_last_error() === JSON_ERROR_NONE ) {
				$result = $this->sync_account_with_dapp();
				if ( $result['status'] ) {
					$this->util->update_option( 'mo_nft_admin_customer_key', $customer_key['id'] );
					$this->util->update_option( 'mo_nft_admin_api_key', $customer_key['apiKey'] );
					$this->util->update_option( 'mo_nft_customer_token', $customer_key['token'] );
					$this->util->update_option( 'mo_nft_password', '' );
					$this->util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'Customer retrieved successfully' );
					$this->util->delete_option( 'mo_nft_verify_customer' );
					$this->util->delete_option( 'mo_nft_new_registration' );
					$this->util->show_success_message();
				} else {
					$this->util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, $result['errorMessage'] );
					$this->util->update_option( 'mo_nft_verify_customer', 'true' );
					$this->util->show_error_message();
				}
			} else {
				$this->util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'You already have an account with miniOrange. Please enter a valid password.' );
				$this->util->update_option( 'mo_nft_verify_customer', 'true' );
				$this->util->show_error_message();
			}
		}

		/**
		 * Create customer from API wrapper.
		 */
		public function create_customer() {
			global $mo_nft_util;
			$customer     = new Customer();
			$customer_key = json_decode( $customer->create_customer(), true );
			if ( 0 === strcasecmp( $customer_key['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS' ) ) {
				$this->mo_nft_get_current_customer();
				$this->util->delete_option( 'mo_nft_new_customer' );
			} elseif ( 0 === strcasecmp( $customer_key['status'], 'SUCCESS' ) ) {
				$this->util->update_option( 'mo_nft_admin_customer_key', $customer_key['id'] );
				$this->util->update_option( 'mo_nft_admin_api_key', $customer_key['apiKey'] );
				$this->util->update_option( 'mo_nft_customer_token', $customer_key['token'] );
				$this->util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'Registered successfully.' );
				$this->util->update_option( 'mo_nft_registration_status', 'mo_nft_REGISTRATION_COMPLETE' );
				$this->util->update_option( 'mo_nft_new_customer', 1 );
				$this->util->delete_option( 'mo_nft_verify_customer' );
				$this->util->delete_option( 'mo_nft_new_registration' );
				$result = $this->sync_account_with_dapp();
				if ( $result['status'] ) {
					$this->util->show_success_message();
				} else {
					$this->util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'Some error happened while creating your account, please try again.' );
					$this->util->show_error_message();
				}
			} elseif ( 0 === strcasecmp( $customer_key['status'], 'TRANSACTION_LIMIT_EXCEEDED' ) ) {
				$this->util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'Transaction limit exceeded, Please try after sometime.' );
				$this->util->show_error_message();
			} elseif ( 0 === strcasecmp( $customer_key['status'], 'NO_MX_RECORD' ) || 0 === strcasecmp( $customer_key['status'], 'INVALID_EMAIL_QUICK_EMAIL' ) ) {
				$this->util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'Please enter a valid email.' );
				$this->util->show_error_message();
			} else {
				$this->util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'Some error happened please try after sometime' );
				$this->util->show_error_message();
			}
		}

		/**
		 * Sync account with our cloud service so that we can call APIs
		 */
		public function sync_account_with_dapp() {
			$email    = $this->util->get_option( 'mo_nft_admin_email' );
			$password = $this->util->get_option( 'mo_nft_password' );
			$headers  = array(
				'Accept' => 'application/json',
			);
			$data     = array(
				'email'    => $email,
				'password' => $password,
			);
			$args     = array(
				'method'      => 'POST',
				'timeout'     => '30',
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $headers,
				'sslverify'   => false,
				'body'        => $data,
			);
			$response = wp_remote_post( \MoNft\Constants::MARKETPLACE_LOGIN_ENDPOINT, $args );
			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				wp_send_json_error( $error_message );
			}
			$res           = wp_remote_retrieve_body( $response );
			$metadata_body = json_decode( $res, true );
			return $metadata_body;
		}
	}
}

