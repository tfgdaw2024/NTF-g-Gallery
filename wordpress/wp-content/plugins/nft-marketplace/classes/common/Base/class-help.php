<?php
/**
 * Core
 *
 * Create MoNft Method help.
 *
 * @category   Common, Core
 * @package    MoNft\Base
 * @author     miniOrange <info@xecurify.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoNft\view;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MoNft\view\Help' ) ) {
	/**
	 * Class to display FAQs
	 */
	class Help {
		/**
		 * Constructor
		 */
		public function __construct() {

		}
		/**
		 * Function to render UI for FAQs
		 *
		 * @return void
		 */
		public function render_faq_ui() {
			?>
			<h4 class="mo-nft-faq-heading">FAQs</h4>
			<div id="web3-method" >
				<?php
					$this->help_view();
				?>
			</div>
			<br>
			<script>
				jQuery(document).ready(function($) {
					jQuery('.monft-faq-box').click(function() {
						var $faqBox = jQuery(this);
						var $ans = $faqBox.find('.monft-faq-a');

						$faqBox.find('.monft-exppand-ans').toggleClass('dashicons-arrow-up-alt2');
						$ans.slideToggle();
					});
				});
			</script>
			<?php
		}
		/**
		 * Function to display FAQs
		 *
		 * @return void
		 */
		public function help_view() {
					$monft_faq_array = \MoNft\Constants::FAQ;
					$monft_count     = 0;
			?>
					<div class="mo-nft-content-card">
				<?php
				foreach ( $monft_faq_array as $que => $ans ) {
					?>
						<div class="monft-faq-box">
							<div class="monft-faq-q">
								<div class="monft-question">
								<?php echo esc_html( $que ); ?>
								</div>
								<span class="monft-exppand-ans dashicons dashicons-arrow-down-alt2" id="<?php echo esc_attr( 'monft-' . $monft_count ); ?>"></span>
							</div>
							<div class="monft-faq-a" id="<?php echo esc_attr( 'monft-' . ( $monft_count++ ) . '-ans' ); ?>">
							<?php echo esc_html( $ans ); ?>
							</div>
						</div>
					<?php
				}
				?>
				</div>
			<?php
		}
	}
}
?>
