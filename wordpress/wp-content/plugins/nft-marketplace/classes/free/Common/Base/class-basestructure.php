<?php
/**
 * Plugin UI Base Structure
 *
 * NFT Marketplace Config guides.
 *
 * @category   Core
 * @package    MoNft\Base
 * @author     miniOrange <info@xecurify.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoNft\Base;

use MoNft\Support;
use MoNft\Base\Loader;
use MoNft\view\Collection;
use MoNft\view\ImportCollection;
use MoNft\view\BulkUpload;
use MoNft\Accounts;
use MoNft\view\Help;
use MoNft\View\Branding;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MoNft\Base\BaseStructure' ) ) {
	/**
	 * Class to render Basic Structure of plugin UI.
	 *
	 * @category Core
	 * @package  MoNft\Base\BaseStructure
	 * @author   miniOrange <info@xecurify.com>
	 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
	 * @link     https://miniorange.com
	 */
	class BaseStructure {

		/**
		 * Private variable loader.
		 *
		 * @var loader
		 */
		private $loader;
		/**
		 * Private variable instance_helper.
		 *
		 * @var instance_helper
		 */
		private $instance_helper;
		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );

			$this->loader          = new Loader();
			$this->instance_helper = new InstanceHelper();
		}

		/**
		 * Functino to add Plugin to menu list.
		 */
		public function admin_menu() {

			$page = add_menu_page( 'NFT Marketplace ' . __( 'Configure Settings', 'mo_nft_settings' ), 'miniOrange NFT Marketplace', 'administrator', 'mo_nft_settings', array( $this, 'menu_options' ), MONFT_URL . 'classes/resources/images/miniorange.png' );
		}

		/**
		 * Render Skeleton.
		 */
		public function menu_options() {
			global $mo_nft_util;
			$currenttab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( ( $_GET['tab'] ) ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring nonce verification because we are fetching data from URL and not on form submission.
			?>
		<div>
			<div class='monft-overlay dashboard'></div>
			<div class="miniorange_container">
				<?php

					$this->content_navbar( $currenttab );
				?>
			</div>

		</div>
			<?php
		}

		/**
		 * Function to render tabs.
		 *
		 * @param string $currenttab Current active tab.
		 */
		public function content_navbar( $currenttab ) {
			global $mo_nft_util, $mo_nft_plugin_admin_url,$mo_nft_plugin_dir_url;
			$admin_api_key          = $mo_nft_util->get_option( 'mo_nft_admin_api_key' );
			$mo_nft_miniorange_logo = $mo_nft_plugin_dir_url . 'classes/resources/images/miniorange-logo.png';
			$redirect_ic            = $mo_nft_plugin_dir_url . 'classes/resources/images/redirect_ic.png';

			?>

<div class="wrap">
<div class="mo-nft-header-wrap">		
	<nav class="navbar navbar-expand-md mo-nft-navbar">
	<div class="container-fluid">
		<div class="monft-navbar-title">
			<img src="<?php echo esc_url( $mo_nft_miniorange_logo ); ?>" alt="miniOrange" height="30px" width="30px">
			<a class="navbar-brand" href="#">miniOrange NFT Marketplace</a>
		</div>
		<div>
		<a href="<?php echo esc_url( \MoNft\Constants::SET_UP_GUIDE_LINK ); ?>" target="_blank" class=" mo-nft-btn btn">Set Up Guide<img class="mo-nft-redirect_ic" src="<?php echo( esc_url( $redirect_ic ) ); ?>" height="24px"/></a>
		<a href="<?php echo esc_url( \MoNft\Constants::WEB3_SUITE_LINK ); ?>" target="_blank" class=" mo-nft-btn btn">Other Web3 Solutions<img class="mo-nft-redirect_ic" src="<?php echo( esc_url( $redirect_ic ) ); ?>" height="24px"/></a>
		</div>
	</div>
	</nav>				
</div>

<div class="mo-nft-support-div">
		<div id="mo-slide-support" class="mo-nft-slide-support-btn dashicons dashicons-arrow-right-alt2"></div>
		<div class="mo-nft-support-content">
						<?php Support::support_page(); ?>
		</div>
		<div class="mo-support-footer">
		</div>
	</div>
<script>
	jQuery('.mo-nft-support-div').addClass('mo-nft-support-closed');
		jQuery('#mo-slide-support').removeClass('dashicons-arrow-right-alt2');
		jQuery('#mo-slide-support').addClass('mo-nft-support-icon');

	$('#mo-slide-support').click(function(){
			$('.mo-nft-support-div').toggleClass('mo-nft-support-closed');
			$('#mo-slide-support').toggleClass('dashicons-arrow-right-alt2');
			$('#mo-slide-support').toggleClass('mo-nft-support-icon');
			$('#mo-nft-support-section').toggleClass('mo-nft-support-section');
	});
	</script>
			<div class="container mo_nft_container">
				<div class="row">
					<div class="col-2">
						<div class="mo-nft-tab">
							<a href="<?php echo esc_url( $mo_nft_plugin_admin_url ) . 'admin.php?page=mo_nft_settings&tab=account'; ?>">
								<button class="mo-nft-tab-button <?php echo ( 'account' === esc_attr( $currenttab ) || '' === esc_attr( $currenttab ) ) ? 'active' : ''; ?>">
									<div class="mo-nft-nav-svg">
											<div class="nft-svg-account"></div>
									</div>
									<div class="mo-nft-nav-msg">Account Setup</div>
								</button>
							</a>
							<a href="<?php echo esc_url( $mo_nft_plugin_admin_url ) . 'admin.php?page=mo_nft_settings&tab=addcollection'; ?>">
							<button  class="mo-nft-tab-button 
							<?php
							echo ( 'addcollection' === esc_attr( $currenttab ) ) ? 'active' : '';
							echo ( 'import_deploy' === esc_attr( $currenttab ) ) ? 'active' : '';
							?>
							">
								<div class="mo-nft-nav-svg">
									<div class="nft-svg-import-nft"></div>
								</div>
								<div class="mo-nft-nav-msg">Add Collection</div>
							</button>
							</a>						
							<a href="<?php echo esc_url( $mo_nft_plugin_admin_url ) . 'admin.php?page=mo_nft_settings&tab=upload_nft_metadata'; ?>">
							<button  class="mo-nft-tab-button <?php echo ( 'upload_nft_metadata' === esc_attr( $currenttab ) ) ? 'active' : ''; ?>">
								<div class="mo-nft-nav-svg">
								<div class="nft-svg-upload-nft"></div>
								</div>
								<div class="mo-nft-nav-msg">Upload NFT Metadata</div>
							</button>
							</a>
							<a href="<?php echo esc_url( $mo_nft_plugin_admin_url ) . 'admin.php?page=mo_nft_settings&tab=branding'; ?>">
							<button  class="mo-nft-tab-button <?php echo ( 'branding' === esc_attr( $currenttab ) ) ? 'active' : ''; ?>">
								<div class="mo-nft-nav-svg">
									<div class="nft-svg-brand-nft"></div>
								</div>
								<div class="mo-nft-nav-msg">Branding</div>
							</button>
							</a>
							<a href="<?php echo esc_url( $mo_nft_plugin_admin_url ) . 'admin.php?page=mo_nft_settings&tab=help'; ?>">
								<button class="mo-nft-tab-button <?php echo ( 'help' === esc_attr( $currenttab ) ) ? 'active' : ''; ?>">
									<div class="mo-nft-nav-svg">
										<div class="nft-svg-help"></div>
									</div>
									<div class="mo-nft-nav-msg">FAQs</div>
								</button>
							</a>					
						</div>
					</div>
					<div class="col-9 mo-nft-tab-content">
						<?php

						$collection                 = new Collection();
						$nft_import_deploy_settings = new ImportCollection();
						$bulk_upload                = new BulkUpload();
						$account                    = new Accounts();
						$help                       = new Help();
						$branding                   = new Branding();

						switch ( esc_attr( $currenttab ) ) {
							case 'account':
								$account->render_account_ui();
								break;

							case 'addcollection':
								if ( empty( $admin_api_key ) ) {
									$account->render_account_ui();
									break;
								} else {
									$collection->render_add_collection_ui();
									break;
								}

							case 'import_deploy':
								if ( empty( $admin_api_key ) ) {
									$account->render_account_ui();
									break;
								} else {
									$nft_import_deploy_settings->render_import_ui();
									break;
								}

							case 'upload_nft_metadata':
								if ( empty( $admin_api_key ) ) {
									$account->render_account_ui();
									break;
								} else {
									$bulk_upload->render_nft_metadata_ui();
									break;
								}

							case 'branding':
								if ( empty( $admin_api_key ) ) {
									$account->render_account_ui();
									break;
								} else {
									$branding->render_branding_ui();
									break;
								}

							case 'help':
								$help->render_faq_ui();
								break;

							default:
								$account->render_account_ui();
						}
						?>
					</div>
				</div>
			</div>
					</div>
						<?php

		}
		/**
		 * Function to enqueue js
		 *
		 * @return void
		 */
		public function enqueue_list_js() {
			global $mo_nft_util;
			if ( true === $mo_nft_util->is_developer_mode ) {
				wp_enqueue_script( 'monft_list_NFT_marketplace', MONFT_URL . 'classes/free/Resources/js/web3/dev/listNFT.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = true );
			} else {
				wp_enqueue_script( 'monft_list_NFT_marketplace', MONFT_URL . 'classes/free/Resources/js/web3/prod/listNFT.min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = true );
			}
		}
	}
}
?>
