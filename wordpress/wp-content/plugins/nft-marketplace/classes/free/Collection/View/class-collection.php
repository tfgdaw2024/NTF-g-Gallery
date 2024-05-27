<?php
/**
 * Core
 *
 * Create MoNft Collection view.
 *
 * @category   Common, Core
 * @package    MoNft\View
 * @author     miniOrange <info@xecurify.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoNft\view;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Create MoNft Method View Handler.
 *
 * @category Common, Core
 * @package  MoNft\View\Collection
 * @author   miniOrange <info@xecurify.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     https://miniorange.com
 */


if ( ! class_exists( 'MoNft\view\Collection' ) ) {
	/**
	 * Class to display NFT collection.
	 */
	class Collection {

		/**
		 * Constructor
		 */
		public function __construct() {
		}
		/**
		 * Function to enqueue scripts
		 *
		 * @return void
		 */
		public function mo_nft_wp_enqueue() {
			global $mo_nft_util;
			wp_enqueue_script( 'jquery' );
			if ( true === $mo_nft_util->is_developer_mode ) {
				wp_enqueue_script( 'mo_nft_web3Min', MONFT_URL . 'classes/resources/js/web3/dev/web3Min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'mo_nft_web3ModalDistIndex', MONFT_URL . 'classes/resources/js/web3/dev/web3ModalDistIndex.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_style( 'mo_nft_style', MONFT_URL . 'classes/resources/css/dev/bootstrap/bootstrap.min.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_style( 'mo_nft_custom_style', MONFT_URL . 'classes/resources/css/dev/styles.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
			} else {
				wp_enqueue_script( 'mo_nft_web3Min', MONFT_URL . 'classes/resources/js/web3/prod/web3Min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'mo_nft_web3ModalDistIndex', MONFT_URL . 'classes/resources/js/web3/prod/web3ModalDistIndex.min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_style( 'mo_nft_style', MONFT_URL . 'classes/resources/css/prod/bootstrap/bootstrap.min.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_style( 'mo_nft_custom_style', MONFT_URL . 'classes/resources/css/prod/styles.min.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
			}

		}
		/**
		 * Function to select the blockchain.
		 *
		 * @param saved_blockchain   $saved_blockchain Blockchain name which is saved.
		 * @param current_blockchain $current_blockchain Current blockchain.
		 * @return selected
		 */
		public function get_selected_blockchain( $saved_blockchain, $current_blockchain ) {
			if ( $saved_blockchain === $current_blockchain ) {
				return 'selected';
			}
			return '';
		}
		/**
		 * Function to render UI.
		 *
		 * @return void
		 */
		public function render_add_collection_ui() {
			?>
			<div id="web3-method" >
				<?php
				$this->add_collection_view();
				?>
			</div>
			<?php
		}
		/**
		 * Function to show option on how to add collection.
		 *
		 * @return void
		 */
		public function add_collection_view() {
			global $mo_nft_util, $mo_nft_plugin_admin_url,$mo_nft_plugin_dir_url;
			$currenttab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( ( $_GET['tab'] ) ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring nonce verification because we are fetching data from URL and not on form submission.
			$page_id    = $mo_nft_util->get_option( 'monft_marketplace_page_id' );
			?>
			<div class="container">
				<div class="row">
				<a href=<?php echo esc_url( get_post_permalink( $page_id ) ); ?> target="blank" class="mo-nft-btn-visit-marketplace btn">Visit Marketplace</a>
				<div class="row">
				<div class="col-md-6">
					<div class="card monft-add-collection-options">
					<div class="card-header monft-add-collection-header">
						<p><b>Already have NFT collection?</b></p>
					</div>
					<div class="card-body monft-add-collection-body">
						<p>Import existing NFT collection</p><br>
							<a href="<?php echo esc_url( $mo_nft_plugin_admin_url ) . 'admin.php?page=mo_nft_settings&tab=import_deploy'; ?>">
								<button class="mo-nft-btn btn">Import</button>
							</a>
					</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="card monft-add-collection-options">
					<div class="card-header monft-add-collection-header">
						<p><b>Do not have NFT collection?</b></p>
					</div>
					<div class="card-body  monft-add-collection-body">
						<p>Deploy a collection</p><br>
						<a href="<?php echo esc_url( $mo_nft_plugin_admin_url ) . 'admin.php?page=mo_nft_settings&tab=import_deploy&action=deploy'; ?>">
							<button class="mo-nft-btn btn">Deploy</button>
						</a>
					</div>
					</div>
				</div>
				</div>
				</div>
				<a href="<?php echo esc_url( $mo_nft_plugin_admin_url ) . 'admin.php?page=mo_nft_settings&tab=account'; ?>" class="mo-nft-add-collection-back-btn btn">
					Back
				</a>
			</div>
			<?php
		}
	}
}
?>
