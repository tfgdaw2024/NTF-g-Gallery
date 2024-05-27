<?php
/**
 * Support
 *
 * MoNft Plugin Support.
 *
 * @category   Common
 * @package    MoNft\Support
 * @author     miniOrange <info@xecurify.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoNft;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MoNft\Support' ) ) {
	/**
	 * Class to Handle and render support form.
	 *
	 * @category Core
	 * @package  MoNft\Support\Support
	 * @author   miniOrange <info@xecurify.com>
	 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
	 * @link     https://miniorange.com
	 */
	class Support {

		/**
		 * Public function.
		 */
		public static function support() {
			self::support_page();
		}

		/**
		 * Private function to render support form.
		 */
		public static function support_page() {
			global $mo_nft_util;
			$mo_nft_admin_email = '';
			$mo_nft_admin_email = $mo_nft_util->get_option( 'mo_nft_admin_email' ) ? $mo_nft_util->get_option( 'mo_nft_admin_email' ) : '';

			?>
		<div id="mo-nft-support-layout" class="mo-nft-support-layout">
				<div class="mo-nft-support-header">
				<h4>Contact Us</h4>
				</div>
				<?php if ( isset( $_SERVER['REQUEST_URI'] ) ) { ?>
				<p>Need any help? Couldn't find an answer in <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'help' ), sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) ); ?>">FAQ</a>?<br>Just send us a query so we can help you.</p>
					<?php
				}
				?>
				<form method="post" action="" class="mo-nft-support-form">
					<input type="hidden" name="option" value="mo_nft_contact_us_query_option" />
					<?php wp_nonce_field( 'mo_nft_contact_us_query_option', 'mo_nft_contact_us_query_option_nonce' ); ?>
					<input type="email" class="mo-nft-support-textbox" required name="mo_nft_contact_us_email" placeholder="Enter email here" value="<?php echo esc_attr( $mo_nft_admin_email ); ?>">
					<input type="tel" id="contact_us_phone" placeholder="Enter phone here" class="mo-nft-support-textbox" name="mo_nft_contact_us_phone" value="<?php esc_attr( $mo_nft_util->get_option( 'mo_nft_admin_phone' ) ); ?>">
					<textarea class="mo-nft-support-textbox" onkeypress="mo_nft_valid_query(this)" placeholder="Enter your query here" onkeyup="mo_nft_valid_query(this)" onblur="mo_nft_valid_query(this)" required name="mo_nft_contact_us_query" rows="4" style="resize: vertical;"></textarea>
					<input type="submit" name="submit" class="button button-primary button-large" />
					<p>If you want custom features in the plugin, just drop an email at <a href="mailto:web3@xecurify.com">web3@xecurify.com</a>.</p>
				</form>
		</div>
		<script>
			jQuery("#contact_us_phone").intlTelInput();
			function mo_nft_valid_query(f) {
				!(/^[a-zA-Z?,.\(\)\/@ 0-9]*$/).test(f.value) ? f.value = f.value.replace(
						/[^a-zA-Z?,.\(\)\/@ 0-9]/, '') : null;
			}
		</script>
			<?php
		}
	}
}
?>
