<?php
/**
 * Core
 *
 * Create NFT Marketplace Page.
 *
 * @category   Common, Core
 * @package    MoNft\Navigation
 * @author     miniOrange <info@xecurify.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoNft\Navigation;

use MoNft\Utils;
use MoNft\Base\InstanceHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MoNft\Navigation\Marketplace' ) ) {
	/**
	 * Class to Create MoNft Method View Handler.
	 *
	 * @category Common, Core
	 * @package  MoNft\Navigation\Marketplace
	 * @author   miniOrange <info@xecurify.com>
	 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
	 * @link     https://miniorange.com
	 */
	class Marketplace {
		/**
		 * Constructor
		 */
		public function __construct() {
			add_shortcode( 'monft_marketplace', array( $this, 'view' ) );
			$this->utils = new Utils();
		}

		/**
		 * This function returns marketplace UI.
		 *
		 * @return string
		 */
		public function view() {

			$instance_helper = new InstanceHelper();
			$base_structure  = $instance_helper->get_base_structure_instance();
			global $mo_nft_util, $wpdb, $flag, $mo_nft_plugin_dir_url;
			wp_enqueue_script( 'jquery' );
			$content = '';
			$base_structure->enqueue_list_js();

			if ( true === $mo_nft_util->is_developer_mode ) {
				wp_enqueue_style( 'monft_marketplace_ui_styles', MONFT_URL . 'classes/resources/css/dev/marketplace_styles.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'monft_marketplace_ui_script', MONFT_URL . 'classes/free/Resources/js/web3/dev/marketplaceUI.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = true );
				wp_enqueue_script( 'monft_marketplace_ui_ethers', MONFT_URL . 'classes/resources/js/web3/dev/ethers-5.2.esm.min.js', array(), $ver = null, $in_footer = false );
				wp_enqueue_script( 'monft_marketplace_ui_web3', MONFT_URL . 'classes/resources/js/web3/dev/web3Min.js', array(), $ver = null, $in_footer = false );
				wp_enqueue_script( 'monft_display_category', MONFT_URL . 'classes/resources/js/web3/dev/displayCategories.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = true );
			} else {
				wp_enqueue_style( 'monft_marketplace_ui_styles', MONFT_URL . 'classes/resources/css/prod/marketplace_styles.min.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'monft_marketplace_ui_script', MONFT_URL . 'classes/free/Resources/js/web3/prod/marketplaceUI.min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = true );
				wp_enqueue_script( 'monft_marketplace_ui_ethers', MONFT_URL . 'classes/resources/js/web3/prod/ethers-5.2.esm.min.js', array(), $ver = null, $in_footer = false );
				wp_enqueue_script( 'monft_marketplace_ui_web3', MONFT_URL . 'classes/resources/js/web3/prod/web3Min.js', array(), $ver = null, $in_footer = false );
				wp_enqueue_script( 'monft_display_category', MONFT_URL . 'classes/resources/js/web3/prod/displayCategories.min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = true );
			}

			wp_enqueue_style( 'dashicons' );
			$data = array(
				'ajax_url'                            => admin_url( 'admin-ajax.php' ),
				'wp_nonce'                            => wp_create_nonce( 'mo_nft_wp_nonce' ),
				'mo_nft_collection'                   => $mo_nft_util->get_option( 'mo_nft_token_config_details_store' ),
				'monft_listed_nft_order_ids'          => $mo_nft_util->get_option( 'monft_listed_nft_order_ids' ),
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

			add_action( 'script_loader_tag', array( $this, 'add_type_to_script_settings' ), 10, 3 );

			$content .= '<div class="monft_categories col-md-2 "></div>';

			// Opening monft-marketplace-ui-wrapper.
			$content .= '<div class="monft-marketplace-ui-wrapper">';

			// Opening monft-navbar.
			$content .= '<div class="monft-navbar">';
			$content .= '<input class="monft-navbar-search" type="text" placeholder="Search for NFTs">';
			$content .= '<img role="img" src="' . $mo_nft_plugin_dir_url . 'classes/resources/images/octicon_search-24.png" />';
			$content .= '<button id="monft-mp-connect-wallet" class="monft-navbar-button" type="button">Connect Wallet</button>';

			// Closing navbar.
			$content .= '</div>';

			// Opening monft-collection-banner.
			$content .= '<div class="monft-collection-banner">';
			$content .= '<div class="monft-collection-cover">';

			$user_id = 1;

			// Get a single meta value.
			$banner_image           = get_user_meta( $user_id, 'mo_nft_collection_banner_image', true );
			$profile_image          = get_user_meta( $user_id, 'mo_nft_collection_profile_image', true );
			$collection_description = get_user_meta( $user_id, 'mo_nft_collection_description', true );

			if ( $banner_image ) {
				$content .= '<img src="' . $banner_image . '" alt="img"/>';
			} else {
				$content .= '<img src="' . $mo_nft_plugin_dir_url . 'classes/resources/images/bored-ape-fishing.jpg" alt="img"/>';
			}
			$content .= '</div>';
			$content .= '<div class="monft-collection-profile">';
			if ( $profile_image ) {
				$content .= '<img src="' . $profile_image . '" alt="img"/>';
			} else {
				$content .= '<img src="' . $mo_nft_plugin_dir_url . 'classes/resources/images/profile_image.jpeg" alt="img"/>';
			}
			$content .= '</div>';

			// Closing monft-collection-banner.
			$content          .= '</div>';
			$mo_nft_collection = $mo_nft_util->get_option( 'mo_nft_token_config_details_store' );
			$collection_name   = get_user_meta( $user_id, 'mo_nft_collection_name' );
			// Opening monft-collection-info-panel.
			$content .= '<div class="monft-collection-info-panel">';
			$content .= '<div class="monft-collection-nd-outer">';
			$content .= '<div class="monft-collection-name">';
			$content .= '<h3>' . $collection_name[0] . '</h3>';
			$content .= '</div>';
			$content .= '<div class="monft-collection-description">';
			$content .= '<p>';
			$content .= $collection_description;
			$content .= '</p>';
			$content .= '</div>';
			$content .= '</div>';
					// Opening monft-collection-details.
					$content .= '<div class="monft-collection-details">';
					$content .= '<table>';
					$content .= '<tbody>';
					$content .= '<tr>';
					$content .= '<td>Items</td>';
					$content .= '<td id="monft_total_NFTs"></td>';
					$content .= '</tr>';
					$content .= '<tr>';
					$content .= '<td>Chain</td>';
					$content .= '<td id="monft_chain"></td>';
					$content .= '</tr>';
					$content .= '<tr>';
					$content .= '<td>Listed</td>';
					$content .= '<td id="monft_listing_percent"></td>';
					$content .= '</tr>';
					$content .= '<tr>';
					$content .= '<td>Owners</td>';
					$content .= '<td id="monft_owner_counter"></td>';
					$content .= '</tr>';
					$content .= '</tbody>';
					$content .= '</table>';
					// Closing monft-collection-details.
					$content .= '</div>';

			// Closing monft-collection-info-panel.
			$content .= '</div>';

			// Opening the monft-nft-section panel.
			$content .= '<div class="monft-nft-section">';

			$categories     = array();
			$categorieshtml = '';

			$token_details = array();

			if ( ! empty( $mo_nft_collection ) ) {
							// Opening the monft-nft-filter sub-panel.
				$content .= '<div class="monft-nft-filter">';

				// Opening the heading inside filter.
				$content .= '<div class="monft-filter-sub-head">';
				$content .= '<div class="monft-filter-sub-left">';
				$content .= '<svg id="monft-filter-svg" width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">';
				$content .= '<path d="M20.2812 7.21875H1.71875C1.44525 7.21875 1.18294 7.1101 0.989546 6.9167C0.796149 6.72331 0.6875 6.461 0.6875 6.1875C0.6875 5.914 0.796149 5.65169 0.989546 5.4583C1.18294 5.2649 1.44525 5.15625 1.71875 5.15625H20.2812C20.5548 5.15625 20.8171 5.2649 21.0105 5.4583C21.2038 5.65169 21.3125 5.914 21.3125 6.1875C21.3125 6.461 21.2038 6.72331 21.0105 6.9167C20.8171 7.1101 20.5548 7.21875 20.2812 7.21875ZM16.8438 12.0312H5.15625C4.88275 12.0312 4.62044 11.9226 4.42705 11.7292C4.23365 11.5358 4.125 11.2735 4.125 11C4.125 10.7265 4.23365 10.4642 4.42705 10.2708C4.62044 10.0774 4.88275 9.96875 5.15625 9.96875H16.8438C17.1173 9.96875 17.3796 10.0774 17.573 10.2708C17.7663 10.4642 17.875 10.7265 17.875 11C17.875 11.2735 17.7663 11.5358 17.573 11.7292C17.3796 11.9226 17.1173 12.0312 16.8438 12.0312ZM12.7188 16.8438H9.28125C9.00775 16.8438 8.74544 16.7351 8.55205 16.5417C8.35865 16.3483 8.25 16.086 8.25 15.8125C8.25 15.539 8.35865 15.2767 8.55205 15.0833C8.74544 14.8899 9.00775 14.7812 9.28125 14.7812H12.7188C12.9923 14.7812 13.2546 14.8899 13.448 15.0833C13.6413 15.2767 13.75 15.539 13.75 15.8125C13.75 16.086 13.6413 16.3483 13.448 16.5417C13.2546 16.7351 12.9923 16.8438 12.7188 16.8438Z" fill="#303030"/>';
				$content .= '</svg>';
				$content .= '<h4><b>Filters</b></h4>';
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
				$content .= '<div class="monft-sub-left-group" style="margin-left: 54%;">';

				$content .= '<input class="monft-sub-navbar-search" type="text" placeholder="Search for NFTs"/>';
				$content .= '<img role="img" src="' . $mo_nft_plugin_dir_url . 'classes/resources/images/octicon_search-24.png" />';
				$content .= '</div>';

				// Closing sub display navbar.
				$content .= '</div>';

				// Opening NFT display grid.
				$content .= '<div class="monft-nft-grid">';
				foreach ( $mo_nft_collection as $key => $value ) {
					$headers = array(
						'Accept'    => 'application/json',
						'X-API-Key' => \MoNft\Constants::ALCHEMY_API_KEY,
					);
					$args    = array(
						'method'      => 'GET',
						'timeout'     => '30',
						'redirection' => '5',
						'httpversion' => '1.0',
						'blocking'    => true,
						'headers'     => $headers,
						'sslverify'   => true,
					);
					if ( 'sepolia' === $mo_nft_collection[ $key ]['blockchain'] ) {
						$chain = \MoNft\Constants::ALCHEMY_SEPOLIA;
					} elseif ( 'Ethereum-Mainnet' === $mo_nft_collection[ $key ]['blockchain'] ) {
						$chain = \MoNft\Constants::ALCHEMY_ETHEREUM;
					} elseif ( 'Polygon' === $mo_nft_collection[ $key ]['blockchain'] || 'mumbai' === $mo_nft_collection[ $key ]['blockchain'] ) {
						$chain = \MoNft\Constants::ALCHEMY_MUMBAI;
					} else {
						$chain = \MoNft\Constants::ALCHEMY_BINANCE;
					}
					$response = wp_remote_post( "https://{$chain}" . \MoNft\Constants::ALCHEMY_ENDPOINT . \MoNft\Constants::ALCHEMY_API_KEY . "/getNFTsForContract?contractAddress={$key}&withMetadata=true", $args );

					if ( is_wp_error( $response ) ) {
						$error_message = $response->get_error_message();
						wp_send_json_error( $error_message );
					}

					$res                 = wp_remote_retrieve_body( $response );
					$metadata_body       = json_decode( $res, true );
					$collection_address  = array();
					$token_ids           = array();
					$flag                = 0;
					$listed_nfts_counter = 0;

					// Start for loop to display metadata of minted NFTs.
					foreach ( $metadata_body as $metadata_key => $value ) {
						if ( 'nfts' === $metadata_key ) {
							$total_nfts = count( $metadata_body['nfts'] );
							foreach ( $value as $field_key => $fieldvalue ) {
								array_push( $token_ids, $metadata_body['nfts'][ $field_key ]['tokenId'] );
								$metadata             = '';
								$metadatastringformat = '';

								// NFT Grid Items.
								$content   .= '<div class="monft-nft-grid-item" id="mo-nft-product-' . $metadata_body['nfts'][ $field_key ]['tokenId'] . '">';
								$parsed_url = wp_parse_url( $metadata_body['nfts'][ $field_key ]['tokenUri'] );

								$path = $parsed_url['path'];

								$path_segments = explode( '/', $path );

								$cid = end( $path_segments );

								$ipfs_url              = \MoNft\Constants::IPFS_ENDPOINT . $cid;
								$response_metadata_obj = get_transient( 'monft_' . $ipfs_url );
								if ( ! $response_metadata_obj ) {

									$args         = array(
										'timeout' => '30',
									);
									$metadata_obj = wp_remote_get( $ipfs_url, $args );

									if ( '200' === (string) wp_remote_retrieve_response_code( $metadata_obj ) ) {

										$response_metadata_obj = wp_remote_retrieve_body( $metadata_obj );
										set_transient( 'monft_' . $ipfs_url, $response_metadata_obj );
									}
								}
								$metadata_body_array = json_decode( $response_metadata_obj, true );
								$image_cid           = substr( $metadata_body_array['image'], 7 );
								foreach ( $metadata_body_array['attributes'] as $key => $value ) {
									if ( isset( $metadata_body_array['attributes'][ $key ]['trait_type'] ) ) {
										$categories[ $metadata_body_array['attributes'][ $key ]['trait_type'] ][ $metadata_body_array['attributes'][ $key ]['value'] ] = 1;
										$metadata             .= '<p style="margin-left: 2px; font-size: 12px;">';
										$metadata             .= esc_attr( $metadata_body_array['attributes'][ $key ]['trait_type'] ) . ': ' . esc_attr( $metadata_body_array['attributes'][ $key ]['value'] ) . '</p>';
										$metadatastringformat .= $metadata_body_array['attributes'][ $key ]['trait_type'] . $metadata_body_array['attributes'][ $key ]['value'];
									}
								}
								$nft_description          = $metadata_body_array['description'];
								$display_nft_description  = '<p style="margin-left: 2px; font-size: 12px;">';
								$display_nft_description .= esc_attr( $nft_description ) . '</p>';
								// NFT grid item image wrapper.
								$content  .= '<div class="monft-image-wrapper-minted" id="' . esc_attr( $metadata_body['nfts'][ $field_key ]['tokenId'] ) . '">';
								$content  .= '<img src="' . \MoNft\Constants::IPFS_ENDPOINT . esc_attr( $image_cid ) . '" class="monft_image" name="monft_image" id="monft_image_' . esc_attr( $metadata_body['nfts'][ $field_key ]['tokenId'] ) . '"/>';
								$content  .= '</div>';
								$content  .= '<div class="monft-grid-info-panel" id="monft-grid-info-panel-' . esc_attr( $metadata_body['nfts'][ $field_key ]['tokenId'] ) . '">';
								$content  .= '<p  class="owner_of_nft" id="monft-owner-of-nft-' . esc_attr( $metadata_body['nfts'][ $field_key ]['tokenId'] ) . '"></p>';
								$content  .= '<h3 class="monft-item-name">' . esc_attr( $metadata_body_array['name'] ) . ' #' . esc_attr( $metadata_body['nfts'][ $field_key ]['tokenId'] ) . '</h3>';
								$content  .= '<div class="monft-attributes" hidden>';
								$content  .= '<input id="' . esc_attr( $metadata_body['nfts'][ $field_key ]['tokenId'] ) . 'collection_name" hidden>';
								$content  .= $metadata;
								$content  .= '</div>'; // For monft-attributes.
								$content  .= '<div class="monft-description" name="monft_description_' . esc_attr( $metadata_body['nfts'][ $field_key ]['tokenId'] ) . '" hidden>';
								$content  .= $display_nft_description;
								$content  .= '</div>'; // For monft-description.
								$is_listed = get_post_meta( $metadata_body['nfts'][ $field_key ]['tokenId'], '_monft_nft_listed' );
								if ( isset( $is_listed['0'] ) && 'yes' === $is_listed[0] ) {
									$listed_nfts_counter ++;
									$content .= '<p id="monft-is-listed-message-' . esc_attr( $metadata_body['nfts'][ $field_key ]['tokenId'] ) . '" class="monft-is-listed-message"></p>';
								} else {
									$content .= '<p id="monft-not-listed-message-' . esc_attr( $metadata_body['nfts'][ $field_key ]['tokenId'] ) . '" class="monft-not-listed-message">#' . esc_attr( $metadata_body['nfts'][ $field_key ]['tokenId'] ) . ' Is not listed</p>';
								}
								$content .= '<p id="monft-bought-message-' . esc_attr( $metadata_body['nfts'][ $field_key ]['tokenId'] ) . '" hidden>#' . esc_attr( $metadata_body['nfts'][ $field_key ]['tokenId'] ) . ' Bought!</p>';
								$content .= '</div>'; // For monft-grid-info-panel.
								if ( isset( $is_listed['0'] ) && 'yes' === $is_listed[0] ) {
									$content .= '<div id ="owner-address-' . esc_attr( $metadata_body['nfts'][ $field_key ]['tokenId'] ) . '" hidden></div>';

									$content .= '<button name="buy-button" id="' . esc_attr( $metadata_body['nfts'][ $field_key ]['tokenId'] ) . '" class="monft-transact-button" listed="yes" value="' . esc_attr( $metadata_body['nfts'][ $field_key ]['contract']['address'] ) . '">';
									$content .= '<svg name="monft-cart-svg-' . esc_attr( $metadata_body['nfts'][ $field_key ]['tokenId'] ) . '"width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">';
									$content .= '<path d="M17 18C17.5304 18 18.0391 18.2107 18.4142 18.5858C18.7893 18.9609 19 19.4696 19 20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22C16.4696 22 15.9609 21.7893 15.5858 21.4142C15.2107 21.0391 15 20.5304 15 20C15 18.89 15.89 18 17 18ZM1 2H4.27L5.21 4H20C20.2652 4 20.5196 4.10536 20.7071 4.29289C20.8946 4.48043 21 4.73478 21 5C21 5.17 20.95 5.34 20.88 5.5L17.3 11.97C16.96 12.58 16.3 13 15.55 13H8.1L7.2 14.63L7.17 14.75C7.17 14.8163 7.19634 14.8799 7.24322 14.9268C7.29011 14.9737 7.3537 15 7.42 15H19V17H7C6.46957 17 5.96086 16.7893 5.58579 16.4142C5.21071 16.0391 5 15.5304 5 15C5 14.65 5.09 14.32 5.24 14.04L6.6 11.59L3 4H1V2ZM7 18C7.53043 18 8.03914 18.2107 8.41421 18.5858C8.78929 18.9609 9 19.4696 9 20C9 20.5304 8.78929 21.0391 8.41421 21.4142C8.03914 21.7893 7.53043 22 7 22C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20C5 18.89 5.89 18 7 18ZM16 11L18.78 6H6.14L8.5 11H16Z" fill="white"/>';
									$content .= '</svg>';
									$content .= '<p name="monft-buy-text-' . esc_attr( $metadata_body['nfts'][ $field_key ]['tokenId'] ) . '">Buy</p>';
									$content .= '<img src="' . esc_url( MONFT_URL . 'classes/resources/images/loading-3-marketplace.gif' ) . '" alt="buying_nfts" name="monft-buying-loader-' . esc_attr( $metadata_body['nfts'][ $field_key ]['tokenId'] ) . '" class="monft-loader-buy" hidden>';
									$content .= '</button>';
								} else {
									$content .= '<button name="list-nft-button" id="' . esc_attr( $metadata_body['nfts'][ $field_key ]['tokenId'] ) . '" class="monft-transact-button" listed="no" hidden>';
									$content .= '<p name="monft-buy-text-' . esc_attr( $metadata_body['nfts'][ $field_key ]['tokenId'] ) . '">List NFT</p>';
									$content .= '<img src="' . esc_url( MONFT_URL . 'classes/resources/images/loading-3-marketplace.gif' ) . '" alt="buying_nfts" name="monft-buying-loader-' . esc_attr( $metadata_body['nfts'][ $field_key ]['tokenId'] ) . '" class="monft-loader-buy" hidden>';
									$content .= '</button>';
								}
								$content .= '<div class="monft_purchase_message" name="purchase_message_' . esc_attr( $metadata_body['nfts'][ $field_key ]['tokenId'] ) . '" hidden>Sold</div>';
								$content .= '<div id="monft_metadata_' . esc_attr( $metadata_body['nfts'][ $field_key ]['tokenId'] ) . '" class="monft_metadata" hidden>' . esc_attr( $metadatastringformat ) . '</div>';
								$content .= '</div>';
								// Handle case for listed/not listed.
							}
						}
					}
					$token_details[ $metadata_body['nfts'][ $field_key ]['contract']['address'] ] = $token_ids;
				}

				if ( ! empty( $token_details[ $metadata_body['nfts'][ $field_key ]['contract']['address'] ] ) ) {
					$flag = 1;
				}
				$data['token_details']       = $token_details;
				$data['total_nfts']          = $total_nfts;
				$data['listed_nfts_counter'] = $listed_nfts_counter;
				// Localize this after Minted NFTs are fetched using Moralis.
				wp_localize_script( 'monft_list_NFT_marketplace', 'monft_utility_object', $data );

				// Get non-minted NFTs from database.
				$query_args      = array(
					'post_type'      => 'monft_product',
					'posts_per_page' => -1,
				);
				$get_post_result = get_posts( $query_args );

				foreach ( $get_post_result as $product_id => $product_item ) {

					$metadata                  = '';
					$metadatastringformat      = '';
					$deployed_contract_address = '';
					$bulk_metadata             = get_post_meta( $get_post_result[ $product_id ]->ID, '_product_attributes' );
					$image_attributes          = get_post_meta( $get_post_result[ $product_id ]->ID, '_product_attributes' );
					$attached_contract_address = get_post_meta( $get_post_result[ $product_id ]->ID, '_monft_nft_contract_address' );

					// Get description.
					$post_content         = $get_post_result[ $product_id ]->post_content;
					$display_post_content = '<p style="margin-left: 2px; font-size: 12px;">' . esc_attr( $post_content ) . '</p>';

					foreach ( $mo_nft_collection as $key => $value ) {
						$deployed_contract_address = $key;
					}
					if ( ! empty( $attached_contract_address ) && ( $attached_contract_address[0] === $deployed_contract_address ) ) {
						$flag = 1;
						foreach ( $image_attributes as $key => $value ) {
							if ( isset( $image_attributes[ $key ]['name'] ) ) {
								foreach ( $image_attributes[ $key ]['name'] as $index => $value ) {
									$categories[ $image_attributes[ $key ]['name'][ $index ] ][ $image_attributes[ $key ]['value'][ $index ] ] = 1;
									$metadata             .= '<p style="margin-left: 2px; font-size: 12px;">';
									$metadata             .= esc_attr( $image_attributes[ $key ]['name'][ $index ] ) . ': ' . esc_attr( $image_attributes[ $key ]['value'][ $index ] ) . '</p>';
									$metadatastringformat .= $image_attributes[ $key ]['name'][ $index ] . $image_attributes[ $key ]['value'][ $index ];
								}
							}
						}

						$content       .= '<div class="monft-nft-grid-item" id="mo-nft-product-' . esc_attr( $get_post_result[ $product_id ]->ID ) . '">';
						$content       .= '<div class="monft-image-wrapper-not-minted" id="' . esc_attr( $get_post_result[ $product_id ]->ID ) . '">';
						$attachment_id  = get_post_meta( $get_post_result[ $product_id ]->ID, '_thumbnail_id', true );
						$attachment_url = wp_get_attachment_url( $attachment_id );
						$content       .= '<img src="' . esc_attr( $attachment_url ) . '" class="monft_image" name="monft_image" id="monft_image_' . esc_attr( $get_post_result[ $product_id ]->ID ) . '"/>';
						$content       .= '</div>';
						$content       .= '<div class="monft-grid-info-panel" id="monft-grid-info-panel-' . esc_attr( $get_post_result[ $product_id ]->ID ) . '">';
						$content       .= '<h3 class="monft-item-name">' . esc_attr( $get_post_result[ $product_id ]->post_title ) . '</h3>';
						$content       .= '<div class="monft-attributes" hidden>';
						$content       .= '<input id="' . esc_attr( $get_post_result[ $product_id ]->ID ) . 'collection_name" hidden>';
						$content       .= $metadata;
						$content       .= '</div>'; // For monft-attributes.
						$content       .= '<div class="monft-description" name="monft_description_' . esc_attr( $get_post_result[ $product_id ]->ID ) . '" hidden>';
						$content       .= $display_post_content;
						$content       .= '</div>'; // For monft-description.
						$content       .= '<p id="monft-minted-message-' . esc_attr( $get_post_result[ $product_id ]->ID ) . '" hidden>#' . esc_attr( $get_post_result[ $product_id ]->ID ) . ' Minted!</p>';
						$content       .= '</div>'; // For monft-grid-info-panel.
						$content       .= '<button name="mint-button" id="' . esc_attr( $get_post_result[ $product_id ]->ID ) . '" class="monft-transact-button">';
						$content       .= '<svg name="monft-cart-svg-' . esc_attr( $get_post_result[ $product_id ]->ID ) . '"width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">';
						$content       .= '<path d="M17 18C17.5304 18 18.0391 18.2107 18.4142 18.5858C18.7893 18.9609 19 19.4696 19 20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22C16.4696 22 15.9609 21.7893 15.5858 21.4142C15.2107 21.0391 15 20.5304 15 20C15 18.89 15.89 18 17 18ZM1 2H4.27L5.21 4H20C20.2652 4 20.5196 4.10536 20.7071 4.29289C20.8946 4.48043 21 4.73478 21 5C21 5.17 20.95 5.34 20.88 5.5L17.3 11.97C16.96 12.58 16.3 13 15.55 13H8.1L7.2 14.63L7.17 14.75C7.17 14.8163 7.19634 14.8799 7.24322 14.9268C7.29011 14.9737 7.3537 15 7.42 15H19V17H7C6.46957 17 5.96086 16.7893 5.58579 16.4142C5.21071 16.0391 5 15.5304 5 15C5 14.65 5.09 14.32 5.24 14.04L6.6 11.59L3 4H1V2ZM7 18C7.53043 18 8.03914 18.2107 8.41421 18.5858C8.78929 18.9609 9 19.4696 9 20C9 20.5304 8.78929 21.0391 8.41421 21.4142C8.03914 21.7893 7.53043 22 7 22C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20C5 18.89 5.89 18 7 18ZM16 11L18.78 6H6.14L8.5 11H16Z" fill="white"/>';
						$content       .= '</svg>';
						$content       .= '<p name="monft-mint-text-' . esc_attr( $get_post_result[ $product_id ]->ID ) . '">Mint</p>';
						$content       .= '<img src="' . esc_url( MONFT_URL . 'classes/resources/images/loading-3-marketplace.gif' ) . '" alt="buying_nfts" name="monft-buying-loader-' . esc_attr( $get_post_result[ $product_id ]->ID ) . '" class="monft-loader-buy" hidden>';
						$content       .= '</button>';
						$content       .= '<div id="monft_metadata_' . esc_attr( $get_post_result[ $product_id ]->ID ) . '" class="monft_metadata" hidden>' . esc_attr( $metadatastringformat ) . '</div>';
						$content       .= '</div>';
					}
				}
			}

				// Closing NFT display grid.
				$content .= '</div>';

			if ( ! $flag ) {
				$content .= '<div class="monft_categories col-md-2 " style="display:none;"></div>';
				$content .= '<h3 class="monft_empty_message" style="text-align: center;margin-left: 13%;color: #a19d9d;font-family: sans-serif;">No NFTs to display!</h3>';
			}

			$data = array(
				'categories' => $categories,
			);
			wp_localize_script( 'monft_display_category', 'monft_utility_object', $data );

			// Closing the monft-nft-display sub-panel.
			$content .= '</div>';

			// Closing the monft-nft-section panel.
			$content .= '</div>';

			// Closing monft-marketplace-ui-wrapper.
			$content .= '</div>';

			return $content;
		}

		/**
		 * Add module type to html tag.
		 *
		 * @param string $tag The <script> tag for the enqueued script.
		 * @param string $handle The script's registered handle.
		 * @param string $source The script's source URL.
		 * @return string
		 */
		public function add_type_to_script_settings( $tag, $handle, $source ) {
			if ( 'monft_marketplace_ui_ethers' === $handle ) {
				$tag = '<script type="module" src="' . $source . '" ></script>';//phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -- This js needs type = module. This is the only way wordpress provides to enque a js of type module.
			}
			return $tag;
		}
	}
}
