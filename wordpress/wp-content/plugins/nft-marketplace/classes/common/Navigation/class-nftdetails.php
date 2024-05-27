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
if ( ! class_exists( 'MoNft\Navigation\NftDetails' ) ) {
	/**
	 * Class to Create MoNft Method View Handler.
	 *
	 * @category Common, Core
	 * @package  MoNft\Navigation\NftDetails
	 * @author   miniOrange <info@xecurify.com>
	 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
	 * @link     https://miniorange.com
	 */
	class NftDetails {
		/**
		 * Constructor
		 */
		public function __construct() {
			add_shortcode( 'monft_marketplace_nft_info', array( $this, 'view' ) );
		}
		/**
		 * This function returns NFT Profile page UI
		 *
		 * @return string
		 */
		public function view() {
			$content         = '';
			$instance_helper = new InstanceHelper();
			global $mo_nft_plugin_dir_url;
			$base_structure = $instance_helper->get_base_structure_instance();
			global $mo_nft_util;
			$mo_nft_collection = $mo_nft_util->get_option( 'mo_nft_token_config_details_store' );
			// $token_id
			if ( ! empty( $mo_nft_collection ) ) {
				foreach ( $mo_nft_collection as $key => $value ) {
					$contract_address = $mo_nft_collection[ $key ]['contractAddress'];
					$chain            = $mo_nft_collection[ $key ]['blockchain'];
					$block_chain      = strtoupper( $mo_nft_collection[ $key ]['blockchain'] );
					$token_standard   = strtoupper( $mo_nft_collection[ $key ]['standard'] );
				}
			}
			$query_params = $_GET; //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring nonce verification because we are fetching data from URL and not on form submission.
			if ( ! empty( $query_params['tokenId'] ) ) {
				$token_id      = $query_params['tokenId'];
				$is_nft_listed = isset( get_post_meta( $token_id, '_monft_nft_listed' )[0] ) ? get_post_meta( $token_id, '_monft_nft_listed' )[0] : false;
			} else {
				$token_id      = '';
				$is_nft_listed = '';
			}
			if ( ! empty( $query_params['productId'] ) ) {
				$product_id = $query_params['productId'];
			} else {
				$product_id = '';
			}

			if ( 'yes' === $is_nft_listed ) {
				$current_nft_details = get_post_meta( $token_id, '_monft_listed_signed_voucher' )[0]['message'];
				$current_price       = explode( ',', $current_nft_details )[0];
				$price_value         = trim( substr( $current_price, strlen( 'Price:' ) ) );
				$wei_per_ether       = '1000000000000000000'; // 1 Ether = 10^18 Wei
				// Perform the conversion.
				$quotient  = intdiv( $price_value, $wei_per_ether );
				$remainder = $price_value % $wei_per_ether;
				// Format the result as a string.
				$list_price_ether                     = $quotient . '.' . sprintf( '%018d', $remainder );
				list($whole_number, $fractional_part) = explode( '.', $list_price_ether, 2 );
				// Remove trailing zeros only from the fractional part.
				$trimmed_fractional_part = rtrim( $fractional_part, '0' );
				// If there's anything left in the fractional part, concatenate it back.
				$final_price_value = $trimmed_fractional_part ? "$whole_number.$trimmed_fractional_part" : $whole_number;
			} else {
				$final_price_value = '-';
			}
			// monft-mint-button-from-profile.
			$button_name_set['name']  = ! empty( $token_id ) ? 'monft-list-button-from-profile' : 'monft-mint-button-from-profile';
			$button_name_set['value'] = ! empty( $token_id ) ? 'LIST NFT' : 'MINT NFT';
			$button_name['name']      = 'yes' === $is_nft_listed ? 'monft-buy-button-from-profile' : $button_name_set['name'];
			$button_name['value']     = 'yes' === $is_nft_listed ? 'BUY NFT' : $button_name_set['value'];
			$chain_currency           = ( 'mumbai' === $chain ? 'Matic' : 'Eth' );
			$nft_owner                = 'No Owner';
			if ( ! empty( $token_id ) ) {
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
					if ( 'sepolia' === $chain ) {
						$chain = \MoNft\Constants::ALCHEMY_SEPOLIA;
					} elseif ( 'Ethereum-Mainnet' === $chain ) {
						$chain = \MoNft\Constants::ALCHEMY_ETHEREUM;
					} elseif ( 'Polygon' === $chain || 'mumbai' === $chain ) {
						$chain = \MoNft\Constants::ALCHEMY_MUMBAI;
					} else {
						$chain = \MoNft\Constants::ALCHEMY_BINANCE;
					}
					$response      = wp_remote_post( "https://{$chain}" . \MoNft\Constants::ALCHEMY_ENDPOINT . \MoNft\Constants::ALCHEMY_API_KEY . "/getNFTMetadata?contractAddress={$contract_address}&tokenId={$token_id}&refreshCache=false", $args );
					$res           = wp_remote_retrieve_body( $response );
					$metadata_body = json_decode( $res, true );
					$nft_owner     = ! empty( $metadata_body['owner_of'] ) ? $metadata_body['owner_of'] : 'No Owner';
					$parsed_url    = wp_parse_url( $metadata_body['tokenUri'] );

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
						}
					}
					$metadata_body_array = json_decode( $response_metadata_obj, true );
					$image_cid           = substr( $metadata_body_array['image'], 7 );
					$nft_name            = $metadata_body_array['name'];
					$nft_description     = $metadata_body_array['description'];
					$nft_attribute       = $metadata_body_array['attributes'];
					foreach ( $nft_attribute as $key => $value ) {
						$nft_trait       = $nft_attribute[ $key ]['trait_type'];
						$nft_trait_value = $nft_attribute[ $key ]['value'];
					}
			} else {
				$args                     = array(
					'post_type'      => 'monft_product',
					'post__in'       => array( $product_id ),
					'posts_per_page' => -1,
				);
				$result                   = get_posts( $args );
				$nft_attribute_not_minted = get_post_meta( $product_id, '_product_attributes' )[0];
				$nft_attribute            = array();
				$trait_array              = $nft_attribute_not_minted['name'];
				$value_array              = $nft_attribute_not_minted['value'];
				$nft_index                = 0;
				foreach ( $trait_array as $key => $value ) {
					$nft_attribute[ $nft_index ]['trait_type'] = $value;
					++$nft_index;
				}
				$nft_index = 0;
				foreach ( $value_array as $key => $value ) {
					$nft_attribute[ $nft_index ]['value'] = $value;
					++$nft_index;
				}
				$nft_description = $result[0]->post_content;
				$nft_name        = $result[0]->post_title;
				$attachment_id   = get_post_meta( $product_id, '_thumbnail_id', true );
				$attachment_url  = wp_get_attachment_url( $attachment_id );

			}
			$content                 .= '
            <div class="nft-details-ui-wrapper">
            ';
			$content                 .= '
            <div class="container" style="margin-top:6%;">
               <div id="loaderwholescreen">
                  <div class="loaderwholescreen"></div>
               </div>
               <!-- Modal HTML -->
               <div id="myModal">
                  <div class="modal-header">
                     <img src="' . $mo_nft_plugin_dir_url . 'classes/resources/images/checkmark.jpg" alt="Check Logo" class="check-logo">
                     <p style = "color:white;"></p>
                  </div>
                  <div class="modal-footer">
                     <button id="modalOKBtn" class="modal-btn">OK</button>
                  </div>
               </div>
               <button id="monft-mp-connect-wallet" class="connect-wallet-button" style="margin-right:29%;">Connect Wallet</button>
               <div class="flex-container">
                  <div class="left-section">
                     <!-- Left Section -->
                     <!-- Add your content here -->
                     <!-- Example content -->
                     <div>';
						$content     .= '<img class="nft-image" src="' . ( ! empty( $token_id ) ? \MoNft\Constants::IPFS_ENDPOINT . esc_attr( $image_cid ) : esc_attr( $attachment_url ) ) . '" /> 
                     </div>
                  </div>
                  <div class="right-section">
                     <!-- Right Section -->
                     <!-- Top part of the right section -->
                     <div class="right-section-top" >
                        <div class="right-section-content">
                           <h3>' . esc_attr( strtoupper( $nft_name ) ) . ' #' . $token_id . '</h3>';
							$content .= '
                           		<div class="logo-text-container">
								<img src="' . $mo_nft_plugin_dir_url . 'classes/resources/images/owner_image.jpg" alt="Logo 2" class="small-logo">
								<h4 class="owner_of_nft" id="monft-owner-of-nft-' . $token_id . '" style="
									font-size: 14px;
									"></h4>
                           </div>
                        </div>
                     </div>
                     <!-- Bottom part of the right section -->
                     <div class="right-section-bottom">
                        <div class="right-section-tabs">
                           <div class="tab" id="details-tab">Details</div>
                           <div class="tab" id="description-tab">Description</div>
                           <div class="tab" id="attributes-tab">Attributes</div>
                        </div>
                        <div class="tab-content" id="details-content">
                           <div class="key-value-pair">
                              <div class="key">Contract Address</div>
                              <div class="value">' . esc_attr( $contract_address ) . '</div>
                           </div>
                           <div class="key-value-pair">
                              <div class="key">Chain</div>
                              <div class="value">' . esc_attr( $block_chain ) . '</div>
                           </div>
                           <div class="key-value-pair">
                              <div class="key">Token Standard</div>
                              <div class="value">' . esc_attr( $token_standard ) . '</div>
                           </div>
                           <div class="key-value-pair">
                              <div class="key">Token Id</div>
                              <div class="value">' . esc_attr( $token_id ) . '</div>
                           </div>
                        </div>
                        <div class="tab-content" id="description-content">
                           <!-- Description content -->
                           <p style ="font-family:sans-serif;">' . esc_attr( $nft_description ) . '</p>
                        </div>
                        <div class="tab-content" id="attributes-content">
                           ';
			foreach ( $nft_attribute as $key => $value ) {
				$nft_trait       = $nft_attribute[ $key ]['trait_type'];
				$nft_trait_value = $nft_attribute[ $key ]['value'];
				$content        .= '
                           <div class="key-value-pair-attributes">
                              <div class="key-attributes">' . esc_attr( $nft_trait ) . '</div>
                              <div class="value-attributes">' . esc_attr( $nft_trait_value ) . '</div>
                           </div>
                           ';
			}
				$content .= '<!-- Add more key-value pairs as needed -->
                        </div>
                     </div>
                     <div class="price-container">
                        <div class="price-box" style="
                           font-size: 18px;
                           font-weight: 500;
                           ">';
			if ( '-' === esc_attr( $final_price_value ) ) {
				$content .= '<span style ="margin-top:27px;">Current Price : NA</span>
							</div>';
			} else {
				$content .= '<span style ="margin-top:27px;">Current Price : ' . esc_attr( $final_price_value ) . ' ' . esc_attr( $chain_currency ) . '</span>
							</div>';
			}

			if ( 'LIST NFT' === $button_name['value'] ) {
				$content .= '<div style="text-align: right;" id="list-button-text">
							<input min="0" id="' . esc_attr( $product_id ? $product_id : $token_id ) . '" type="number" class="listing-price-input" name="listing_price" placeholder="Enter Price in ' . esc_attr( $chain_currency ) . '"/>
						</div>';
			}
				$content .= '<div>
						<div id="token-owner" name = "' . esc_attr( $nft_owner ) . '"  hidden></div>
						<div id="buy-button-text"></div>
                        <button data-chain="' . $chain . '" name = "' . esc_attr( $button_name['name'] ) . '" class="buy-now-button" value = "' . $contract_address . '" id="' . esc_attr( $product_id ? $product_id : $token_id ) . '">
                           ' . esc_attr( $button_name['value'] ) . '
                           <div class="loader" id="loader"></div>
                        </button>
						</div>
                     </div>
                  </div>
               </div>
            </div>
            ';

			wp_enqueue_script( 'jquery' );
			if ( true === $mo_nft_util->is_developer_mode ) {
				wp_enqueue_style( 'monft_custom_style_listpage', MONFT_URL . 'classes/resources/css/dev/styles.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_style( 'mo_nft_style', MONFT_URL . 'classes/resources/css/dev/bootstrap/bootstrap.min.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_style( 'monft_custom_style_nft_details', MONFT_URL . 'classes/resources/css/dev/nft_details.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_style( 'monft_style_settings_listpage', MONFT_URL . 'classes/resources/css/dev/style_settings.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'monft_ethersmin_main', MONFT_URL . 'classes/resources/js/web3/dev/ethers-5.2.esm.min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'monft_web3min_main', MONFT_URL . 'classes/resources/js/web3/dev/web3Min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'monft_display_category', MONFT_URL . 'classes/resources/js/web3/dev/displayCategories.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = true );
			} else {
				wp_enqueue_style( 'monft_custom_style_nft_details', MONFT_URL . 'classes/resources/css/prod/nft_details.min.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_style( 'mo_nft_style', MONFT_URL . 'classes/resources/css/prod/bootstrap/bootstrap.min.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_style( 'monft_custom_style_listpage', MONFT_URL . 'classes/resources/css/prod/styles.min.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_style( 'monft_style_settings_listpage', MONFT_URL . 'classes/resources/css/prod/style_settings.min.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'monft_ethersmin_main', MONFT_URL . 'classes/resources/js/web3/prod/ethers-5.2.esm.min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'monft_web3min_main', MONFT_URL . 'classes/resources/js/web3/prod/web3Min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'monft_display_category', MONFT_URL . 'classes/resources/js/web3/prod/displayCategories.min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = true );
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
				'token_id'                            => $token_id,
				'base_url'                            => MONFT_URL,
			);
			// Localize the script.
			wp_localize_script( 'monft_list_NFT_marketplace', 'monft_utility_object', $data );

			$token_id_data = array(
				'categories' => '',
			);

			wp_localize_script( 'monft_display_category', 'monft_utility_object', $token_id_data );

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
	new NftDetails();
}
