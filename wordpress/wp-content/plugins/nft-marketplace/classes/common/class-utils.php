<?php
/**
 * Utils
 *
 * NFT Marketplace Utility class.
 *
 * @category   Core
 * @package    MoNft\Common
 * @author     miniOrange <info@xecurify.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoNft;

use \DateTime;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'MoNft\Utils' ) ) {
	/**
	 * Class containing all utility and helper functions.
	 *
	 * @category Core, Utils
	 * @package  MoNft\Common\Utils
	 * @author   miniOrange <info@xecurify.com>
	 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
	 * @link     https://miniorange.com
	 */
	class Utils {

		/**
		 * Developer mode
		 *
		 * @var $is_developer_mode
		 */
		public $is_developer_mode = false;

		/**
		 * Constructor
		 */
		public function __construct() {

			remove_action( 'admin_notices', array( $this, 'success_message' ) );
			remove_action( 'admin_notices', array( $this, 'error_message' ) );
			add_action( 'mo_nft_clear_plug_cache', array( $this, 'manage_deactivate_cache' ) );
		}
		/**
		 * Function to manage cache
		 *
		 * @return void
		 */
		public function manage_deactivate_cache() {
			$customer = new \MoNft\Customer();
			$customer->manage_deactivate_cache();
		}


		/**
		 * Function to display success message
		 */
		public function success_message() {
			$class   = 'monft-notice monft-success is-dismissible';
			$message = $this->get_option( \MoNft\Constants::PANEL_MESSAGE_OPTION );
			echo "<div class='" . esc_html( $class ) . "'>" . esc_html( $message ) . "<div class='monft-notice-close dashicons dashicons-no-alt'></div></div>";
		}


		/**
		 * Function to display error message
		 */
		public function error_message() {
			$class   = 'monft-notice monft-error';
			$message = $this->get_option( \MoNft\Constants::PANEL_MESSAGE_OPTION );
			echo "<div class='" . esc_html( $class ) . "'>" . esc_html( $message ) . "<div class='monft-notice-close dashicons dashicons-no-alt'></div></div>";
		}

		/**
		 * Function to hook success message function
		 */
		public function show_success_message() {
			remove_action( 'admin_notices', array( $this, 'error_message' ) );
			add_action( 'admin_notices', array( $this, 'success_message' ) );
		}
		/**
		 * Function to send json response
		 *
		 * @param response $response Send response.
		 * @return void
		 */
		public function send_json_response( $response ) {
			$code = isset( $response['code'] ) ? $response['code'] : 302;
			wp_send_json( $response, $code );
		}

		/**
		 * Function to hook error message function
		 */
		public function show_error_message() {
			remove_action( 'admin_notices', array( $this, 'success_message' ) );
			add_action( 'admin_notices', array( $this, 'error_message' ) );
		}

		/**
		 * Is the customer registered?
		 */
		public function is_customer_registered() {
			$email        = $this->get_option( 'mo_nft_admin_email' );
			$customer_key = $this->get_option( 'mo_nft_admin_customer_key' );
			if ( ! $email || ! $customer_key || ! is_numeric( trim( $customer_key ) ) ) {
				return 0;
			} else {
				return 1;
			}
		}
		/**
		 * Function to get version.
		 *
		 * @return free
		 */
		public function get_versi_str() {
			return 'FREE';
		}

		/**
		 * Function to get the Config Object from DB
		 *
		 * @return mixed
		 * */
		public function get_plugin_config() {
			$config = $this->get_option( 'mo_nft_config_settings' );
			return ( ! $config || empty( $config ) ) ? array() : $config;
		}

		/**
		 * Function to update plugin configuration.
		 *
		 * @param config $config Configuration.
		 * @return void
		 */
		public function update_plugin_config( $config ) {
			$this->update_option( 'mo_nft_config_settings', $config );
		}
		/**
		 * Function to authorize.
		 *
		 * @return bool
		 */
		public function authorize() {

			if ( ! empty( $this->get_option( 'mo_nft_le' ) ) ) {
				$license_expiry_date      = $this->decrypt( $this->get_option( 'mo_nft_le' ) );
				$license_expiry_date      = new DateTime( $license_expiry_date );
				$todays_date              = new DateTime();
				$time_left_license_expire = $license_expiry_date->diff( $todays_date );
				$days_left_le             = $time_left_license_expire->days;

				if ( 0 === $days_left_le ) {
					return true;
				}
				return false;
			}
			return false;
		}

		/**
		 * Function to Decrypt.
		 *
		 * @param string $str String to dencc.
		 */
		public function decrypt( $str ) {
			$str  = base64_decode( $str ); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- base64_decode() can be used to obfuscate code which is strongly discouraged. base64_decode() is used here for licensing to decode.
			$pass = $this->get_option( 'mo_nft_customer_token' );
			if ( ! $pass ) {
				return 'false';
			}
			$pass = str_split( str_pad( '', strlen( $str ), $pass, STR_PAD_RIGHT ) );
			$stra = str_split( $str );
			foreach ( $stra as $k => $v ) {
				$tmp        = ord( $v ) - ord( $pass[ $k ] );
				$stra[ $k ] = chr( $tmp < 0 ? ( $tmp + 256 ) : $tmp );
			}
			return join( '', $stra );
		}
		/**
		 * Function to encrypt string
		 *
		 * @param str $str String.
		 * @return encoded
		 */
		public function encrypt( $str ) {
			$pass = $this->get_option( 'mo_nft_customer_token' );
			$pass = str_split( str_pad( '', strlen( $str ), $pass, STR_PAD_RIGHT ) );
			$stra = str_split( $str );
			foreach ( $stra as $k => $v ) {
				$tmp        = ord( $v ) + ord( $pass[ $k ] );
				$stra[ $k ] = chr( $tmp > 255 ? ( $tmp - 256 ) : $tmp );
			}
			return base64_encode( join( '', $stra ) );//phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- base64_encode() can be used to obfuscate code which is strongly discouraged. base64_encode() is used here for licensing to encode.
		}

		/**
		 * Function to check if given value is null or empty.
		 *
		 * @param mixed $value Thing to check.
		 */
		public function check_empty_or_null( $value ) {
			if ( ! isset( $value ) || empty( $value ) ) {
				return true;
			}
			return false;
		}

		/**
		 * Is cURL installed and enabled?
		 */
		public function is_curl_installed() {
			if ( in_array( 'curl', get_loaded_extensions(), true ) ) {
				return 1;
			} else {
				return 0;
			}
		}

		/**
		 * Is cURL installed and enabled?
		 */
		public function show_curl_error() {
			if ( $this->is_curl_installed() === 0 ) {
				$this->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, '<a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP CURL extension</a> is not installed or disabled. Please enable it to continue.' );
				$this->show_error_message();
				return;
			}
		}



		/**
		 * Get WP options.
		 *
		 * @param string $key Option to retrieve.
		 * @param string $default Option to retrieve default value.
		 * @return mixed
		 * */
		public function get_option( $key, $default = false ) {
			$value = ( is_multisite() ) ? get_site_option( $key, $default ) : get_option( $key, $default );
			if ( ! $value || $default === $value ) {
				return $default;
			}
			return $value;
		}

		/**
		 * Update WP options.
		 *
		 * @param string $key   Option to Update.
		 * @param mixed  $value Value to set.
		 * @return bool
		 * */
		public function update_option( $key, $value ) {
			return ( is_multisite() ) ? update_site_option( $key, $value ) : update_option( $key, $value );
		}

		/**
		 * Delete WP options.
		 *
		 * @param string $key Option to delete.
		 * @return mixed
		 * */
		public function delete_option( $key ) {
			return ( is_multisite() ) ? delete_site_option( $key ) : delete_option( $key );
		}
		/**
		 * Function to get license key and verify from database.
		 *
		 * @return bool
		 */
		public function is_clv() {
			$license_key = $this->get_option( 'mo_nft_lk' );
			$isverified  = $this->get_option( 'mo_nft_lv' );
			if ( $isverified ) {
				$isverified = $this->decrypt( $isverified );
			}

			if ( ! empty( $license_key ) && 'true' === $isverified ) {
				return 1;
			}
			return 0;
		}
		/**
		 * Deactivation hook.
		 */
		public function deactivate_plugin() {
			$this->delete_option( 'mo_nft_host_name' );
			$this->delete_option( 'mo_nft_new_registration' );
			$this->delete_option( 'mo_nft_admin_email' );
			$this->delete_option( 'mo_nft_admin_phone' );
			$this->delete_option( 'mo_nft_admin_fname' );
			$this->delete_option( 'mo_nft_admin_lname' );
			$this->delete_option( 'mo_nft_admin_company' );
			$this->delete_option( \MoNft\Constants::PANEL_MESSAGE_OPTION );
			$this->delete_option( 'mo_nft_admin_customer_key' );
			$this->delete_option( 'mo_nft_admin_api_key' );
			$this->delete_option( 'mo_nft_new_customer' );
			$this->delete_option( 'mo_nft_registration_status' );
			$this->delete_option( 'mo_nft_customer_token' );
			$this->delete_option( 'mo_nft_lk' );
			$this->delete_option( 'mo_nft_lv' );
			$this->delete_option( 'mo_nft_le' );
		}
	}
}

