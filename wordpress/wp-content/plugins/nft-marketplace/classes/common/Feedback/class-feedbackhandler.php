<?php
/**
 * App
 *
 * Feedback handler.
 *
 * @category   Core
 * @package    MoNft\Feedback
 * @author     miniOrange <info@xecurify.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoNft;

use MoNft\MintHandler;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MoNft\FeedbackHandler' ) ) {
	/**
	 * Class for Free NFT Settings
	 *
	 * @category Core
	 * @package  MoNft\Feedback\FeedbackHandler
	 * @author   miniOrange <info@xecurify.com>
	 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
	 * @link     https://miniorange.com
	 */
	class FeedbackHandler {

		/**
		 * WEB3 Common Settings
		 *
		 * @var \MoNft\Settings $common_settings
		 * */
		private $common_settings;

		/**
		 * Constructor
		 *
		 * @return void
		 **/
		public function __construct() {
			add_action( 'admin_init', array( $this, 'save_settings' ) );
			add_action( 'admin_footer', array( $this, 'feedback_request' ) );
		}

		/**
		 * Function to Save All Sorts of settings
		 *
		 * @return void
		 **/
		public function save_settings() {
			global $mo_nft_util;

			if ( isset( $_SERVER['REQUEST_METHOD'] ) && sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) === 'POST' && current_user_can( 'administrator' ) ) {
				if ( isset( $_POST['mo_nft_feedback_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_nft_feedback_nonce'] ) ), 'mo_nft_feedback' ) && isset( $_POST[ \MoNft\Constants::OPTION ] ) && 'mo_nft_feedback' === sanitize_text_field( wp_unslash( $_POST[ \MoNft\Constants::OPTION ] ) ) ) {

					$user                      = wp_get_current_user();
					$message                   = 'Plugin Deactivated:';
					$deactivate_reason         = isset( $_POST['mo_nft_deactivate_reason_radio'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_nft_deactivate_reason_radio'] ) ) : false;
					$deactivate_reason_message = isset( $_POST['mo_nft_query_feedback'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_nft_query_feedback'] ) ) : false;
					if ( ! $deactivate_reason ) {
						$mo_nft_util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'Please Select one of the reasons, if your reason is not mentioned please select Other Reasons' );
						$mo_nft_util->show_error_message();
					}
					$message .= $deactivate_reason;
					if ( isset( $deactivate_reason_message ) ) {
						$message .= ':' . $deactivate_reason_message;
					}
					$email = $mo_nft_util->get_option( 'mo_nft_admin_email' );
					if ( '' === $email ) {
						$email = $user->user_email;
					}
					$phone = $mo_nft_util->get_option( 'mo_nft_admin_phone' );
					// only reason.
					$feedback_reasons = new Customer();
					$submited         = json_decode( $feedback_reasons->send_email_alert( $email, $phone, $message ), true );
					deactivate_plugins( MONFT_DIR . DIRECTORY_SEPARATOR . 'miniorange-nft-marketplace-settings.php' );
					$mo_nft_util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'Thank you for the feedback.' );
					$mo_nft_util->show_success_message();
				}
				if ( isset( $_POST['mo_nft_skip_feedback_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_nft_skip_feedback_nonce'] ) ), 'mo_nft_skip_feedback' ) && isset( $_POST['option'] ) && 'mo_nft_skip_feedback' === sanitize_text_field( wp_unslash( $_POST['option'] ) ) ) {
					deactivate_plugins( MONFT_DIR . DIRECTORY_SEPARATOR . 'miniorange-nft-marketplace-settings.php' );
					$mo_nft_util->update_option( \MoNft\Constants::PANEL_MESSAGE_OPTION, 'Plugin Deactivated Successfuly. We will get back to you shortly.' );
					$mo_nft_util->show_success_message();
				}
			}

		}

		/**
		 * Feedback form
		 */
		public function feedback_request() {
			$feedback = new \MoNft\Feedback();
			$feedback->show_form();
		}
	}

}

