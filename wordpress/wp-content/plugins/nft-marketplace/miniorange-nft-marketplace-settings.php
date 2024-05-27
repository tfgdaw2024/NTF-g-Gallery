<?php
/**
 * Plugin Name: NFT Marketplace
 * Plugin URI: http://miniorange.com
 * Description: NFT marketplace converts your WooCommerce store into a NFT store.
 * Version: 3.2.0
 * Author: miniOrange
 * License: MIT/Expat
 * License URI: https://docs.miniorange.com/mit-license
 */

require '-autoload.php';

use MoNft\Base\InstanceHelper;
use \MoNft\Navigation\Marketplace;



global $mo_nft_util;
global $mo_nft_plugin_admin_url;
global $mo_nft_plugin_dir_url, $mo_nft_plugin_dir_path;



$mo_nft_plugin_admin_url = get_admin_url();
$mo_nft_plugin_dir_url   = plugin_dir_url( __FILE__ );
$mo_nft_plugin_dir_path  = plugin_dir_path( __FILE__ );

$instance_helper   = new InstanceHelper();
$monft_marketplace = new Marketplace();
$base_structure    = $instance_helper->get_base_structure_instance();
$import_handler    = $instance_helper->get_import_handler_instance();
$mo_nft_util       = $instance_helper->get_utils_instance();
$feedback_handler  = $instance_helper->get_feedback_handler_instance();
$accounts_handler  = $instance_helper->get_accounts_handler_instance();
$mint_handler      = $instance_helper->get_mint_handler_instance();
$transfer_handler  = $instance_helper->get_transfer_handler_instance();
$upload_handler    = $instance_helper->get_upload_handler_instance();
$branding_handler  = $instance_helper->get_branding_handler_instance();

/**
 * Function to deactivate plugin
 *
 * @return void
 */
function mo_nft_deactivate() {
	global $mo_nft_util;
	do_action( 'mo_nft_clear_plug_cache' );
	$mo_nft_util->deactivate_plugin();
}
register_deactivation_hook( __FILE__, 'mo_nft_deactivate' );

/**
 * Function to create pages on activating plugin
 *
 * @return void
 */
function mo_nft_create_marketplace_pages() {
	global $mo_nft_util;
	if ( empty( $mo_nft_util->get_option( 'monft_marketplace_page_id' ) ) ) {
		$page_title = 'NFT Marketplace';
		$shortcode  = '<!-- wp:shortcode -->
	[monft_marketplace]
	<!-- /wp:shortcode -->';
		// Create page arguments.
		$page_args = array(
			'post_title'   => $page_title,
			'post_content' => $shortcode,
			'post_status'  => 'publish',
			'post_type'    => 'page',
		);

		// Insert the page and get its ID.
		$page_id = wp_insert_post( $page_args );
			$mo_nft_util->update_option( 'monft_marketplace_page_id', $page_id );
	}
	if ( empty( $mo_nft_util->get_option( 'monft_marketplace_nft_info_page_id' ) ) ) {
		$parent_page_id    = $mo_nft_util->get_option( 'monft_marketplace_page_id' );
		$subpage_title     = 'NFT Details';
		$subpage_shortcode = '<!-- wp:shortcode -->
		[monft_marketplace_nft_info]
		<!-- /wp:shortcode -->';
		$subpage_args      = array(
			'post_title'   => $subpage_title,
			'post_content' => $subpage_shortcode,
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_parent'  => $parent_page_id,
		);

		// Insert the subpage and get its ID.
		$subpage_id = wp_insert_post( $subpage_args );
		$mo_nft_util->update_option( 'monft_marketplace_nft_info_page_id', $subpage_id );
	}
	if ( empty( $mo_nft_util->get_option( 'monft_profile_page_id' ) ) ) {
		$page_title = 'My NFT Profile';
		$shortcode  = '<!-- wp:shortcode -->
	[monft_profile]
	<!-- /wp:shortcode -->';
		// Create page arguments.
		$page_args = array(
			'post_title'   => $page_title,
			'post_content' => $shortcode,
			'post_status'  => 'publish',
			'post_type'    => 'page',
		);

		// Insert the page and get its ID.
		$page_id = wp_insert_post( $page_args );
			$mo_nft_util->update_option( 'monft_profile_page_id', $page_id );
	}

}
register_activation_hook( __FILE__, 'mo_nft_create_marketplace_pages' );

add_filter( 'pre_set_site_transient_update_plugins', 'mo_nft_update_nft_details_page' );

/**
 * Function to add nft details page on upgradation
 *
 * @param transient $transient transient.
 * @return void
 */
function mo_nft_update_nft_details_page( $transient ) {

	global $mo_nft_util;

	if ( empty( $mo_nft_util->get_option( 'monft_marketplace_nft_info_page_id' ) ) ) {
		// Page doesn't exist, create it.
		$parent_page_id    = $mo_nft_util->get_option( 'monft_marketplace_page_id' );
		$subpage_title     = 'NFT Details';
		$subpage_shortcode = '<!-- wp:shortcode -->
                [monft_marketplace_nft_info]
                <!-- /wp:shortcode -->';
		$subpage_args      = array(
			'post_title'   => $subpage_title,
			'post_content' => $subpage_shortcode,
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_parent'  => $parent_page_id,
		);

		// Insert the subpage and get its ID.
		$subpage_id = wp_insert_post( $subpage_args );
		$mo_nft_util->update_option( 'monft_marketplace_nft_info_page_id', $subpage_id );
	}
}


