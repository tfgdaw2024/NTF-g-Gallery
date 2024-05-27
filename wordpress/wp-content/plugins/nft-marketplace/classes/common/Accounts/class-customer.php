<?php
/**
 * MiniOrange enables user to log in through OAuth to apps such as Google, EVE Online etc.
 *  Copyright (C) 2015  miniOrange
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 * @package     MoNft\Customer
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

/**
 * This library is miniOrange Authentication Service.
 * Contains Request Calls to Customer service.
 **/

namespace MoNft;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MoNft\Customer' ) ) {

	/**
	 * Accounts
	 *
	 * WEB3 Account Settings.
	 *
	 * @category   Core
	 * @package    MoNft\Customer\Customer
	 * @author     miniOrange <info@xecurify.com>
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
	 * @link       https://miniorange.com
	 */
	class Customer {

		/**
		 * Customer Email
		 *
		 * @var string
		 */
		public $email;

		/**
		 * Customer Phone
		 *
		 * @var string
		 */
		public $phone;

		/**
		 * Default customer key
		 *
		 * @var string
		 */
		private $default_customer_key = '16555';

		/**
		 * Default API key
		 *
		 * @var string
		 */
		private $default_api_key = 'fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq';

		/**
		 * Host Name
		 *
		 * @var string
		 */
		private $host_name = '';

		/**
		 * Host key
		 *
		 * @var string
		 */
		private $host_key = '';

		/**
		 * Constructor
		 */
		public function __construct() {
			global $mo_nft_util;
			$this->host_name = $mo_nft_util->get_option( 'mo_nft_host_name' ) ? $mo_nft_util->get_option( 'mo_nft_host_name' ) : 'https://login.xecurify.com';
			$this->email     = $mo_nft_util->get_option( 'mo_nft_admin_email' );
			$this->phone     = $mo_nft_util->get_option( 'mo_nft_admin_phone' );
			$this->host_key  = $mo_nft_util->get_option( 'mo_nft_password' );
		}

		/**
		 * Function to register customer.
		 */
		public function create_customer() {
			global $mo_nft_util;
			$url          = $this->host_name . '/moas/rest/customer/add';
			$password     = $this->host_key;
			$first_name   = $mo_nft_util->get_option( 'mo_nft_admin_fname' );
			$last_name    = $mo_nft_util->get_option( 'mo_nft_admin_lname' );
			$company      = $mo_nft_util->get_option( 'mo_nft_admin_company' );
			$fields       = array(
				'companyName'           => $company,
				'areaOfInterest'        => 'WP NFT Marketplace',
				'firstname'             => $first_name,
				'lastname'              => $last_name,
				\MoNft\Constants::EMAIL => $this->email,
				'phone'                 => $this->phone,
				'password'              => $password,
			);
			$field_string = wp_json_encode( $fields );

			return $this->send_request(
				array(),
				false,
				$field_string,
				array(),
				false,
				$url
			);
		}

		/**
		 * Function to retrieve customer key from API.
		 */
		public function get_customer_key() {
			global $mo_nft_util;
			$url          = $this->host_name . '/moas/rest/customer/key';
			$email        = $this->email;
			$password     = $this->host_key;
			$fields       = array(
				\MoNft\Constants::EMAIL => $email,
				'password'              => $password,
			);
			$field_string = wp_json_encode( $fields );
			return $this->send_request(
				array(),
				false,
				$field_string,
				array(),
				false,
				$url
			);
		}

		/**
		 * Function to add eveonline app.
		 *
		 * @param string $name     Appname.
		 * @param string $app_name Appname.
		 */
		public function add_nft_application( $name, $app_name ) {
			global $mo_nft_util;
			$url           = $this->host_name . '/moas/rest/application/addoauth';
			$customer_key  = $mo_nft_util->get_option( 'mo_nft_admin_customer_key' );
			$scope         = $mo_nft_util->get_option( 'mo_nft_' . $name . '_scope' );
			$client_id     = $mo_nft_util->get_option( 'mo_nft_' . $name . '_client_id' );
			$client_secret = $mo_nft_util->get_option( 'mo_nft_' . $name . '_client_secret' );
			if ( false !== $scope ) {
				$fields = array(
					'applicationName' => $app_name,
					'scope'           => $scope,
					'customerId'      => $customer_key,
					'clientId'        => $client_id,
					'clientSecret'    => $client_secret,
				);
			} else {
				$fields = array(
					'applicationName' => $app_name,
					'customerId'      => $customer_key,
					'clientId'        => $client_id,
					'clientSecret'    => $client_secret,
				);
			}
			$field_string = wp_json_encode( $fields );
			return $this->send_request(
				array(),
				false,
				$field_string,
				array(),
				false,
				$url
			);
		}


		/**
		 * Self-Explanatory.
		 */
		public function check_internet_connection() {
			return (bool) @fsockopen( 'login.xecurify.com', 443, $errno, $errstr, 5 ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fsockopen, WordPress.PHP.NoSilencedErrors.Discouraged -- Using default PHP function to check socket connection.
		}

		/**
		 * Function to send Feedback.
		 *
		 * @param string $email   Self Explanatory.
		 * @param string $phone   Self Explanatory.
		 * @param string $message Self Explanatory.
		 */
		public function send_email_alert( $email, $phone, $message ) {
			global $mo_nft_util;
			if ( ! $this->check_internet_connection() ) {
				return;
			}
			$url = $this->host_name . '/moas/api/notify/send';
			global $user;
			$customer_key = $this->default_customer_key;
			$api_key      = $this->default_api_key;

			$current_time_in_millis = self::get_timestamp();
			$string_to_hash         = $customer_key . $current_time_in_millis . $api_key;
			$hash_value             = hash( 'sha512', $string_to_hash );
			$from_email             = $email;
			$subject                = 'WordPress NFT Marketplace - ' . $email;
			$site_url               = site_url();
			$user                   = wp_get_current_user();
			$version                = ( \ucwords( \strtolower( $mo_nft_util->get_versi_str() ) ) !== 'Free' ) ? ( \ucwords( \strtolower( $mo_nft_util->get_versi_str() ) ) . ' - ' . \mo_nft_get_version_number() ) : ( ' - ' . \mo_nft_get_version_number() );

			$query   = '[ WP NFT Marketplace' . $version . ' ] : ' . $message;
			$server  = isset( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '';
			$content = '<div >Hello, <br><br>First Name :' . $user->user_firstname . '<br><br>Last  Name :' . $user->user_lastname . '   <br><br>Company :<a href="' . $server . '" target="_blank" >' . $server . '</a><br><br>Phone Number :' . $phone . '<br><br>Email :<a href="mailto:' . $from_email . '" target="_blank">' . $from_email . '</a><br><br>Query :' . $query . '</div>';

			$fields                   = array(
				'customerKey'           => $customer_key,
				'sendEmail'             => true,
				\MoNft\Constants::EMAIL => array(
					'customerKey' => $customer_key,
					'fromEmail'   => $from_email,
					'bccEmail'    => 'web3@xecurify.com',
					'fromName'    => 'miniOrange',
					'toEmail'     => 'web3@xecurify.com',
					'toName'      => 'web3@xecurify.com',
					'subject'     => $subject,
					'content'     => $content,
				),
			);
			$field_string             = wp_json_encode( $fields );
			$headers                  = array( 'Content-Type' => 'application/json' );
			$headers['Customer-Key']  = $customer_key;
			$headers['Timestamp']     = $current_time_in_millis;
			$headers['Authorization'] = $hash_value;
			return $this->send_request(
				$headers,
				true,
				$field_string,
				array(),
				false,
				$url
			);
		}

		/**
		 * Function to submit contactus form.
		 *
		 * @param string $email Email of the admin.
		 * @param string $phone Phone of the admin.
		 * @param string $query Query of the admin.
		 * @param string $send_config bool.
		 */
		public function submit_contact_us( $email, $phone, $query, $send_config = true ) {
			global $current_user;
			global $mo_nft_util;
			wp_get_current_user();
			$customer_key           = $this->default_customer_key;
			$api_key                = $this->default_api_key;
			$current_time_in_millis = time();
			$url                    = $this->host_name . '/moas/api/notify/send';
			$string_to_hash         = $customer_key . $current_time_in_millis . $api_key;
			$hash_value             = hash( 'sha512', $string_to_hash );
			$from_email             = $email;
			$version                = ( \ucwords( \strtolower( $mo_nft_util->get_versi_str() ) ) !== 'Free' ) ? ( \ucwords( \strtolower( $mo_nft_util->get_versi_str() ) ) . ' - ' . \mo_nft_get_version_number() ) : ( ' - ' . \mo_nft_get_version_number() );
			$subject                = 'Query: WordPress NFT Marketplace ' . $version . ' Plugin';
			$query                  = '[WordPress NFT Marketplace ' . $version . '] ' . $query;

			$server                   = isset( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '';
			$content                  = '<div >Hello, <br><br>First Name :' . $current_user->user_firstname . '<br><br>Last  Name :' . $current_user->user_lastname . '   <br><br>Company :<a href="' . $server . '" target="_blank" >' . $server . '</a><br><br>Phone Number :' . $phone . '<br><br>Email :<a href="mailto:' . $from_email . '" target="_blank">' . $from_email . '</a><br><br>Query :' . $query . '</div>';
			$fields                   = array(
				'customerKey'           => $customer_key,
				'sendEmail'             => true,
				\MoNft\Constants::EMAIL => array(
					'customerKey' => $customer_key,
					'fromEmail'   => $from_email,
					'bccEmail'    => 'web3@xecurify.com',
					'fromName'    => 'miniOrange',
					'toEmail'     => 'web3@xecurify.com',
					'toName'      => 'web3@xecurify.com',
					'subject'     => $subject,
					'content'     => $content,
				),
			);
			$field_string             = wp_json_encode( $fields, JSON_UNESCAPED_SLASHES );
			$headers                  = array( 'Content-Type' => 'application/json' );
			$headers['Customer-Key']  = $customer_key;
			$headers['Timestamp']     = $current_time_in_millis;
			$headers['Authorization'] = $hash_value;
			return $this->send_request(
				$headers,
				true,
				$field_string,
				array(),
				false,
				$url
			);
		}


		/**
		 * Function to get timestamp from API.
		 */
		public function get_timestamp() {
			global $mo_nft_util;
			$url = $this->host_name . '/moas/rest/mobile/get-timestamp';
			return $this->send_request(
				array(),
				false,
				'',
				array(),
				false,
				$url
			);
		}



		/**
		 * Function to check if customer registering already exists.
		 */
		public function check_customer() {
			global $mo_nft_util;
			$url          = $this->host_name . '/moas/rest/customer/check-if-exists';
			$email        = $this->email;
			$fields       = array(
				\MoNft\Constants::EMAIL => $email,
			);
			$field_string = wp_json_encode( $fields );
			return $this->send_request(
				array(),
				false,
				$field_string,
				array(),
				false,
				$url
			);
		}



		/**
		 * Function to actually send requests
		 *
		 * @param array  $additional_headers Additional headers to send with default headers.
		 * @param bool   $override_headers   self explanatory.
		 * @param string $field_string       Field String.
		 * @param array  $additional_args    Additional args to send with default headers.
		 * @param bool   $override_args      self explanatory.
		 * @param string $url                URL to send request to.
		 */
		private function send_request( $additional_headers = false, $override_headers = false, $field_string = '', $additional_args = false, $override_args = false, $url = '' ) {
			$headers  = array(
				'Content-Type'  => 'application/json',
				'charset'       => 'UTF - 8',
				'Authorization' => 'Basic',
			);
			$headers  = ( $override_headers && $additional_headers ) ? $additional_headers : array_unique( array_merge( $headers, $additional_headers ) );
			$args     = array(
				'method'      => 'POST',
				'body'        => $field_string,
				'timeout'     => '30',
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $headers,
				'sslverify'   => true,
			);
			$args     = ( $override_args ) ? $additional_args : array_unique( array_merge( $args, $additional_args ), SORT_REGULAR );
			$response = wp_remote_post( $url, $args );

			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				echo wp_kses( "Something went wrong: $error_message", \mo_nft_get_valid_html() );
				exit();
			}
			return wp_remote_retrieve_body( $response );
		}

		/**
		 * Deactivation Hook
		 */
		public function manage_deactivate_cache() {
			global $mo_nft_util;
			$lk = $mo_nft_util->get_option( 'mo_api_authentication_lk' );
			if ( ! $mo_nft_util->is_customer_registered() || false === $lk || empty( $lk ) ) {
				return;
			}
			$url          = $this->host_name . '/moas/api/backupcode/updatestatus';
			$customer_key = $mo_nft_util->get_option( 'mo_nft_admin_customer_key' );
			$api_key      = $mo_nft_util->get_option( 'mo_nft_admin_api_key' );
			$code         = $mo_nft_util->decrypt( $lk );

			$current_time_in_millis = round( microtime( true ) * 1000 );
			$current_time_in_millis = number_format( $current_time_in_millis, 0, '', '' );

			/* Creating the Hash using SHA-512 algorithm */
			$string_to_hash           = $customer_key . $current_time_in_millis . $api_key;
			$hash_value               = hash( 'sha512', $string_to_hash );
			$customer_key_header      = 'Customer-Key: ' . $customer_key;
			$timestamp_header         = 'Timestamp: ' . $current_time_in_millis;
			$authorization_header     = 'Authorization: ' . $hash_value;
			$fields                   = '';
			$fields                   = array(
				'code'             => $code,
				'customerKey'      => $customer_key,
				'additionalFields' => array(
					'field1' => site_url(),
				),
			);
			$field_string             = wp_json_encode( $fields );
			$headers                  = array( 'Content-Type' => 'application/json' );
			$headers['Customer-Key']  = $customer_key;
			$headers['Timestamp']     = $current_time_in_millis;
			$headers['Authorization'] = $hash_value;
			$args                     = array(
				'method'      => 'POST',
				'body'        => $field_string,
				'timeout'     => '30',
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $headers,

			);
			$response = wp_remote_post( $url, $args );

			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				echo 'Something went wrong:' . esc_attr( $error_message );
				exit();
			}

			return wp_remote_retrieve_body( $response );
		}
		/**
		 * Function to verify the license
		 *
		 * @param code $code Code.
		 * @return response
		 */
		public function mo_nft_xfs_zkodsfh_h_j( $code ) {

			global $mo_nft_util;
			$url = $this->host_name . '/moas/api/backupcode/verify';

			$customer_key = $mo_nft_util->get_option( 'mo_nft_admin_customer_key' );
			$api_key      = $mo_nft_util->get_option( 'mo_nft_admin_api_key' );
			$username     = $mo_nft_util->get_option( 'mo_nft_admin_email' );

			/* Current time in milliseconds since midnight, January 1, 1970 UTC. */
			$current_time_in_millis = round( microtime( true ) * 1000 );
			$current_time_in_millis = number_format( $current_time_in_millis, 0, '', '' );

			/* Creating the Hash using SHA-512 algorithm */
			$string_to_hash = $customer_key . $current_time_in_millis . $api_key;
			$hash_value     = hash( 'sha512', $string_to_hash );

			$customer_key_header  = 'Customer-Key: ' . $customer_key;
			$timestamp_header     = 'Timestamp: ' . $current_time_in_millis;
			$authorization_header = 'Authorization: ' . $hash_value;

			$fields = array(
				'code'             => $code,
				'customerKey'      => $customer_key,
				'additionalFields' => array(
					'field1' => site_url(),

				),
			);
			$field_string = wp_json_encode( $fields );

			$headers = array(
				'Content-Type'  => 'application/json',
				'Customer-Key'  => $customer_key,
				'Timestamp'     => $current_time_in_millis,
				'Authorization' => $hash_value,
			);
			$args    = array(
				'method'      => 'POST',
				'body'        => $field_string,
				'timeout'     => '30',
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $headers,

			);

			$response = wp_remote_post( $url, $args );
			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				echo 'Something went wrong: ' . esc_attr( $error_message );
				exit();
			}

			return wp_remote_retrieve_body( $response );
		}
		/**
		 * Function to check customer license.
		 *
		 * @return response
		 */
		public function mo_nft_check_customer_ln() {
			global $mo_nft_util;
			$url          = $this->host_name . '/moas/rest/customer/license';
			$customer_key = $mo_nft_util->get_option( 'mo_nft_admin_customer_key' );

			$api_key                = $mo_nft_util->get_option( 'mo_nft_admin_api_key' );
			$current_time_in_millis = round( microtime( true ) * 1000 );
			$string_to_hash         = $customer_key . number_format( $current_time_in_millis, 0, '', '' ) . $api_key;
			$hash_value             = hash( 'sha512', $string_to_hash );
			$customer_key_header    = 'Customer-Key: ' . $customer_key;
			$timestamp_header       = 'Timestamp: ' . $current_time_in_millis;
			$authorization_header   = 'Authorization: ' . $hash_value;
			$fields                 = '';
			$fields                 = array(
				'customerId'      => $customer_key,
				'applicationName' => 'wp_oauth_web3_authentication_all_inclusive_plan',
			);
			$field_string           = wp_json_encode( $fields );
			$headers                = array(
				'Content-Type'  => 'application/json',
				'Customer-Key'  => $customer_key,
				'Timestamp'     => $current_time_in_millis,
				'Authorization' => $hash_value,
			);
			$args                   = array(
				'method'      => 'POST',
				'body'        => $field_string,
				'timeout'     => '30',
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $headers,
			);
			$response               = wp_remote_post( $url, $args );
			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				echo 'Something went wrong:' . esc_attr( $error_message );
				exit();
			}

			return wp_remote_retrieve_body( $response );
		}

	}
}
