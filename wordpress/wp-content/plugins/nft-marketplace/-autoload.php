<?php
/**
 * This file loads all files containing different classes.
 *
 * @package nft-marketplace
 * @author     miniOrange <info@xecurify.com>
 * @link       https://miniorange.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MONFT_DIR', plugin_dir_path( __FILE__ ) );
define( 'MONFT_URL', plugin_dir_url( __FILE__ ) );
define( 'MONFT_VERSION', 'mo_nft_login_free' );

mo_nft_include_file( MONFT_DIR . DIRECTORY_SEPARATOR . 'classes' );

/**
 * Traverse all sub-directories for files.
 *
 * Get all files in a directory.
 *
 * @param string $folder Folder to Traverse.
 * @param Array  $results Array of files to append to.
 * @return Array $results Array of files found.
 **/
function mo_nft_get_dir_contents( $folder, &$results = array() ) {
	foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $folder, RecursiveDirectoryIterator::KEY_AS_PATHNAME ), RecursiveIteratorIterator::CHILD_FIRST ) as $file => $info ) {
		if ( $info->isFile() && $info->isReadable() ) {
			$results[ $file ] = realpath( $info->getPathname() );
		}
	}
	return $results;
}

/**
 * Order all php files.
 *
 * Get all php files to require() in perfect order.
 *
 * @param string $folder Folder to Traverse.
 * @return Array Array of php files to require.
 **/
function mo_nft_get_sorted_files( $folder ) {

	$filepaths  = mo_nft_get_dir_contents( $folder );
	$interfaces = array();
	$classes    = array();

	foreach ( $filepaths as $file => $filepath ) {
		if ( strpos( $filepath, '.php' ) !== false ) {
			if ( strpos( $filepath, 'Interface' ) !== false ) {
				$interfaces[ $file ] = $filepath;
			} else {
				$classes[ $file ] = $filepath;
			}
		}
	}

	return array(
		'interfaces' => $interfaces,
		'classes'    => $classes,
	);
}

/**
 * Wrapper for require_all().
 *
 * Wrapper to call require_all() in perfect order.
 *
 * @param string $folder Folder to Traverse.
 * @return void
 **/
function mo_nft_include_file( $folder ) {

	if ( ! is_dir( $folder ) ) {
		return;
	}

	$folder   = mo_nft_sane_dir_path( $folder );
	$realpath = realpath( $folder );
	if ( false !== $realpath && ! is_dir( $folder ) ) {
		return;
	}
	$sorted_elements = mo_nft_get_sorted_files( $folder );
	mo_nft_require_all( $sorted_elements['interfaces'] );
	mo_nft_require_all( $sorted_elements['classes'] );
}

/**
 * All files given as input are passed to require_once().
 *
 * Wrapper to call require_all() in perfect order.
 *
 * @param Array $filepaths array of files to require.
 * @return void
 **/
function mo_nft_require_all( $filepaths ) {

	foreach ( $filepaths as $file => $filepath ) {
		require_once $filepath;

	}

}

/**
 * Validate file paths
 *
 * File names passed are validated to be as required
 *
 * @param string $filename filepath to validate.
 * @return bool validity of file.
 **/
function mo_nft_is_valid_file( $filename ) {
	return '' !== $filename && '.' !== $filename && '..' !== $filename;
}

/**
 * Valid html
 *
 * Helper function for escaping.
 *
 * @param array $args HTML to add to valid args.
 *
 * @return array valid html.
 **/
function mo_nft_get_valid_html( $args = array() ) {
	$retval = array(
		'strong' => array(),
		'em'     => array(),
		'b'      => array(),
		'i'      => array(),
		'a'      => array(
			'href'   => array(),
			'target' => array(),
		),
	);
	if ( ! empty( $args ) ) {
		return array_merge( $args, $retval );
	}
	return $retval;
}

/**
 * Get Version number
 */
function mo_nft_get_version_number() {
	$file_data      = get_file_data( MONFT_DIR . DIRECTORY_SEPARATOR . 'miniorange-nft-marketplace-settings.php', array( 'Version' ), 'plugin' );
	$plugin_version = isset( $file_data[0] ) ? $file_data[0] : '';
	return $plugin_version;
}

/**
 * Function to sanitize dir paths.
 *
 * @param string $folder Dir Path to sanitize.
 *
 * @return string sane path.
 */
function mo_nft_sane_dir_path( $folder ) {
	return str_replace( '/', DIRECTORY_SEPARATOR, $folder );
}

