<?php
/**
 * Core
 *
 * Create My Profile Page.
 *
 * @category   Common, Core
 * @package    MoNft\Navigation
 * @author     miniOrange <info@xecurify.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoNft\Navigation;

use MoNft\Base\InstanceHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'MoNft\Navigation\Profile' ) ) {
	/**
	 * Class to Create MoNft Method View Handler.
	 *
	 * @category Common, Core
	 * @package  MoNft\Navigation\Profile
	 * @author   miniOrange <info@xecurify.com>
	 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
	 * @link     https://miniorange.com
	 */
	class Profile {
		/**
		 * Constructor
		 */
		public function __construct() {
			add_shortcode( 'monft_profile', array( $this, 'view' ) );
		}
		/**
		 * This function returns marketplace page UI
		 *
		 * @return string
		 */
		public function view() {
			$instance_helper = new InstanceHelper();
			$base_structure  = $instance_helper->get_base_structure_instance();
			global $mo_nft_util, $mo_nft_plugin_dir_url;
			$content = '';

			// Opening monft-marketplace-ui-wrapper.
			$content .= '<div class="monft-marketplace-ui-wrapper">';

			// Opening monft-navbar.
			$content .= '<div class="monft-navbar">';

			$content .= '<button id="monft-mp-connect-wallet" class="monft-navbar-button" type="button">Connect Wallet</button>';

			// Closing navbar.
			$content .= '</div>';
			// Opening the monft-nft-section panel.
			$content .= '<div class="monft-nft-section">';

			// Opening the monft-nft-filter sub-panel.
			$content .= '<div class="monft-nft-filter">';

			// Opening the heading inside filter.
			$content .= '<div class="monft-filter-sub-head">';
			$content .= '<div class="monft-filter-sub-left">';
			$content .= '<svg id="monft-filter-svg" width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">';
			$content .= '<path d="M20.2812 7.21875H1.71875C1.44525 7.21875 1.18294 7.1101 0.989546 6.9167C0.796149 6.72331 0.6875 6.461 0.6875 6.1875C0.6875 5.914 0.796149 5.65169 0.989546 5.4583C1.18294 5.2649 1.44525 5.15625 1.71875 5.15625H20.2812C20.5548 5.15625 20.8171 5.2649 21.0105 5.4583C21.2038 5.65169 21.3125 5.914 21.3125 6.1875C21.3125 6.461 21.2038 6.72331 21.0105 6.9167C20.8171 7.1101 20.5548 7.21875 20.2812 7.21875ZM16.8438 12.0312H5.15625C4.88275 12.0312 4.62044 11.9226 4.42705 11.7292C4.23365 11.5358 4.125 11.2735 4.125 11C4.125 10.7265 4.23365 10.4642 4.42705 10.2708C4.62044 10.0774 4.88275 9.96875 5.15625 9.96875H16.8438C17.1173 9.96875 17.3796 10.0774 17.573 10.2708C17.7663 10.4642 17.875 10.7265 17.875 11C17.875 11.2735 17.7663 11.5358 17.573 11.7292C17.3796 11.9226 17.1173 12.0312 16.8438 12.0312ZM12.7188 16.8438H9.28125C9.00775 16.8438 8.74544 16.7351 8.55205 16.5417C8.35865 16.3483 8.25 16.086 8.25 15.8125C8.25 15.539 8.35865 15.2767 8.55205 15.0833C8.74544 14.8899 9.00775 14.7812 9.28125 14.7812H12.7188C12.9923 14.7812 13.2546 14.8899 13.448 15.0833C13.6413 15.2767 13.75 15.539 13.75 15.8125C13.75 16.086 13.6413 16.3483 13.448 16.5417C13.2546 16.7351 12.9923 16.8438 12.7188 16.8438Z" fill="#303030"/>';
			$content .= '</svg>';
			$content .= '<h4>Filters</h4>';
			$content .= '</div>';
			$content .= '<div class="monft-filter-sub-right">';
			$content .= '<svg name="monft_filter_attributes" id="monft_filter_attributes" width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">';
			$content .= '<path d="M11.8312 13.0064L16.7939 17.9437L15.3835 19.3613L9.00318 13.0137L15.3508 6.63337L16.7684 8.04473L11.8312 13.0074L11.8312 13.0064Z" fill="#303030"/>';
			$content .= '</svg>';
			$content .= '</div>';

			// Closing the heading inside filter.
			$content .= '</div>';

			// Opening the filters list inside the filter panel.
			$content .= '<div class="monft-filter-list">';

			// Closing the filters list inside the filter panel.
			$content .= '</div>';

			// Closing the monft-nft-filter sub-panel.
			$content .= '</div>';

			// Opening the monft-nft-display sub-panel.
			$content .= '<div class="monft-nft-display">';

			// Opening sub display navbar.
			$content .= '<div class="monft-sub-navbar">';
			$content .= '<div class="monft-sub-left-group-profile">';
			$content .= '<input class="monft-sub-navbar-search-profile" type="text" placeholder="Search for NFTs"/>';
			$content .= '<img class="monft-sub-navbar-search-icon-profile" role="img" src="' . $mo_nft_plugin_dir_url . 'classes/resources/images/octicon_search-24.png" />';

			$content .= '</div>';

			// Closing sub display navbar.
			$content .= '</div>';

			$content .= '<p class="monft_empty_profile_message " hidden>You have not minted any NFTs yet! Please visit the shop to mint</p>';

			// Opening NFT display grid.
			$content .= '<div class="monft-nft-grid">';

			// Closing NFT display grid.
			$content .= '</div>';

			// Closing the monft-nft-display sub-panel.
			$content .= '</div>';

			// Closing the monft-nft-section panel.
			$content .= '</div>';

			// Closing monft-marketplace-ui-wrapper.
			$content .= '</div>';

			// $content .= '<div class="mo-nft-loading" id="mo-nft-loading" hidden><img src="' . esc_url( MONFT_URL . 'classes/resources/images/profile_loader.gif' ) . '" alt="loading_nfts"><br><div class="monft_loading_nft">Loading your NFTs...</div></div>';

			$content .= '<div id="loaderwholescreenprofile">';
			$content .= '<div class="loaderwholescreenprofile"></div>';
			$content .= '</div>';
			wp_enqueue_script( 'jquery' );
			if ( true === $mo_nft_util->is_developer_mode ) {
				wp_enqueue_style( 'monft_marketplace_ui_styles', MONFT_URL . 'classes/resources/css/dev/marketplace_styles.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_style( 'monft_custom_style_listpage', MONFT_URL . 'classes/resources/css/dev/styles.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_style( 'monft_style_settings_listpage', MONFT_URL . 'classes/resources/css/dev/style_settings.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'monft_ethersmin_main', MONFT_URL . 'classes/resources/js/web3/dev/ethers-5.2.esm.min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'monft_web3min_main', MONFT_URL . 'classes/resources/js/web3/dev/web3Min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'monft_web3_display_my_profile_nfts', MONFT_URL . 'classes/resources/js/web3/dev/displayMyProfileNfts.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
			} else {
				wp_enqueue_style( 'monft_marketplace_ui_styles', MONFT_URL . 'classes/resources/css/prod/marketplace_styles.min.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_style( 'monft_custom_style_listpage', MONFT_URL . 'classes/resources/css/prod/styles.min.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_style( 'monft_style_settings_listpage', MONFT_URL . 'classes/resources/css/prod/style_settings.min.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'monft_ethersmin_main', MONFT_URL . 'classes/resources/js/web3/prod/ethers-5.2.esm.min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'monft_web3min_main', MONFT_URL . 'classes/resources/js/web3/prod/web3Min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'monft_web3_display_my_profile_nfts', MONFT_URL . 'classes/resources/js/web3/prod/displayMyProfileNfts.min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
			}

			$base_structure->enqueue_list_js();
			wp_enqueue_style( 'dashicons' );
			$data = array(
				'ajax_url'                            => admin_url( 'admin-ajax.php' ),
				'wp_nonce'                            => wp_create_nonce( 'mo_nft_wp_nonce' ),
				'monft_listed_nft_order_ids'          => $mo_nft_util->get_option( 'monft_listed_nft_order_ids' ),
				'mo_nft_collection'                   => $mo_nft_util->get_option( 'mo_nft_token_config_details_store' ),
				'marketplace_address'                 => \MoNft\Constants::MARKETPLACE_ADDRESS,
				'marketplace_abi'                     => \MoNft\Constants::MARKETPLACE_ABI,
				'marketplace_address_testnet'         => \MoNft\Constants::MARKETPLACE_ADDRESS_TESTNET,
				'marketplace_abi_testnet'             => \MoNft\Constants::MARKETPLACE_ABI_TESTNET,
				'marketplace_address_testnet_polygon' => \MoNft\Constants::MARKETPLACE_ADDRESS_TESTNET_POLYGON,
				'marketplace_abi_testnet_polygon'     => \MoNft\Constants::MARKETPLACE_ABI_TESTNET_POLYGON,
				'marketplace_address_testnet_sepolia' => \MoNft\Constants::MARKETPLACE_ADDRESS_TESTNET_SEPOLIA,
				'marketplace_abi_testnet_sepolia'     => \MoNft\Constants::MARKETPLACE_ABI_TESTNET_SEPOLIA,
				'base_url'                            => MONFT_URL,
			);
			// Localize the script.
			wp_localize_script( 'monft_list_NFT_marketplace', 'monft_utility_object', $data );

			add_action( 'script_loader_tag', array( $this, 'add_type_to_script_settings' ), 10, 3 );

			return $content;

		}
		/**
		 * Undocumented function
		 *
		 * @param [type] $tag The <script> tag for the enqueued script.
		 * @param [type] $handle The script's registered handle.
		 * @param [type] $source The script's source URL.
		 * @return string
		 */
		public function add_type_to_script_settings( $tag, $handle, $source ) {
			if ( 'monft_ethersmin_main' === $handle ) {
				$tag = '<script type="module" src="' . esc_url( $source ) . '" ></script>';//phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -- This js needs type = module. This is the only way wordpress provides to enque a js of type module.
			}
			return $tag;
		}

	}
	new Profile();
}
