<?php
/**
 * Core
 *
 * WEB3 Loader.
 *
 * @category   Common, Core, UI
 * @package    MoNft\Base
 * @author     miniOrange <info@xecurify.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoNft\Base;

use MoNft\Base\InstanceHelper;
use MoNft\Utils;
use MoNft\View\Collection;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MoNft\Base\Loader' ) ) {
	/**
	 * Class to save Load and Render REST API UI
	 *
	 * @category Common, Core
	 * @package  MoNft\Base\Loader
	 * @author   miniOrange <info@xecurify.com>
	 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
	 * @link     https://miniorange.com
	 */
	class Loader {

		/**
		 * Instance Helper
		 *
		 * @var \MoNft\Base\MintHandler $instance_helper
		 * */
		private $instance_helper;
		/**
		 * Private variable util
		 *
		 * @var mo_nft_util
		 */
		private $mo_nft_util;
		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'plugin_settings_style' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'plugin_settings_script' ) );
			$this->mo_nft_util     = new Utils();
			$this->instance_helper = new InstanceHelper();
		}

		/**
		 * Function to enqueue CSS
		 */
		public function plugin_settings_style() {
			global $mo_nft_util;
			if ( true === $mo_nft_util->is_developer_mode ) {
				wp_enqueue_style( 'mo_nft_admin_settings_style', MONFT_URL . 'classes/resources/css/dev/style_settings.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_style( 'mo_nft_admin_settings_phone_style', MONFT_URL . 'classes/resources/css/dev/phone.css', array(), $ver    = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
			} else {
				wp_enqueue_style( 'mo_nft_admin_settings_style', MONFT_URL . 'classes/resources/css/prod/style_settings.min.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_style( 'mo_nft_admin_settings_phone_style', MONFT_URL . 'classes/resources/css/prod/phone.min.css', array(), $ver    = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
			}
		}

		/**
		 * Function to enqueue JS
		 */
		public function plugin_settings_script() {
			global $mo_nft_util;
			wp_enqueue_script( 'mo_nft_admin_settings_phone_script', MONFT_URL . 'classes/resources/js/phone.min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
			if ( true === $mo_nft_util->is_developer_mode ) {
				wp_enqueue_script( 'mo_nft_admin_settings_script', MONFT_URL . 'classes/resources/js/dev/style-settings.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
			} else {
				wp_enqueue_script( 'mo_nft_admin_settings_script', MONFT_URL . 'classes/resources/js/prod/style-settings.min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
			}

		}
	}
}


