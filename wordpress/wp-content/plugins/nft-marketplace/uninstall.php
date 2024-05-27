<?php
/**
 * The uninstall.php file for deleting options table fields.
 *
 * @package  miniOrange-nft-marketplace
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

delete_option( 'mo_nft_host_name' );
delete_option( 'mo_nft_admin_email' );
delete_option( 'mo_nft_admin_phone' );
delete_option( 'mo_nft_verify_customer' );
delete_option( 'mo_nft_admin_customer_key' );
delete_option( 'mo_nft_admin_api_key' );
delete_option( 'mo_nft_customer_token' );
delete_option( 'mo_nft_new_customer' );
delete_option( 'mo_nft_message' );
delete_option( 'mo_nft_new_registration' );
delete_option( 'mo_nft_registration_status' );
delete_option( 'mo_nft_collection' );
delete_option( 'mo_nft_blockchain' );
delete_option( 'monft_tokenid_to_owner' );

