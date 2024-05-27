<?php
/**
 * Core
 *
 * NFT Marketplace Instance Helper.
 *
 * @category   Common, Core
 * @package    MoNft\Base
 * @author     miniOrange <info@xecurify.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoNft\Base;

use MoNft\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MoNft\Base\InstanceHelper' ) ) {
	/**
	 * Class to Select Instance of NFT Marketplace.
	 *
	 * @category Common, Core
	 * @package  MoNft\Base\InstanceHelper
	 * @author   miniOrange <info@xecurify.com>
	 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
	 * @link     https://miniorange.com
	 */
	class InstanceHelper {

		/**
		 * NFT Marketplace Current Version
		 *
		 * @var string $current_version
		 * */
		private $current_version = 'FREE';

		/**
		 * NFT Marketplace common utils
		 *
		 * @var MoNft\Utils $utils
		 * */
		private $utils;

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->utils           = new Utils();
			$this->current_version = $this->utils->get_versi_str();
		}


		/**
		 * Function to get Account Instance
		 *
		 * @return mixed
		 */
		public function get_accounts_instance() {
			return new \MoNft\Accounts();
		}
		/**
		 * Function to get proper feedback handler instance.
		 *
		 * @return mixed
		 */
		public function get_feedback_handler_instance() {

			if ( class_exists( 'MoNft\FeedbackHandler' ) ) {
				return new \MoNft\FeedbackHandler();
			} else {
				wp_die( 'Please Change The version back to what it really was' );
				exit();
			}
		}



		/**
		 * Function to get proper Utils instance.
		 *
		 * @return mixed
		 */
		public function get_utils_instance() {
			return $this->utils;
		}

		/**
		 * Function to get base structure instance.
		 *
		 * @return mixed
		 */
		public function get_base_structure_instance() {

			if ( class_exists( 'MoNft\Base\PremiumBaseStructure' ) ) {
				return new \MoNft\Base\PremiumBaseStructure();
			} elseif ( class_exists( 'MoNft\Base\BaseStructure' ) ) {
				return new \MoNft\Base\BaseStructure();
			} else {
				wp_die( 'Please Change The version back to what it really was' );
				exit();
			}
		}
		/**
		 * Function to get nft-marketplace instance.
		 *
		 * @return mixed
		 */
		public function get_import_handler_instance() {

			if ( class_exists( 'MoNft\controller\PremiumImportHandler' ) ) {
				return new \MoNft\controller\PremiumImportHandler();
			} elseif ( class_exists( 'MoNft\controller\ImportHandler' ) ) {
				return new \MoNft\controller\ImportHandler();
			} else {
				wp_die( 'Please Change The version back to what it really was' );
				exit();
			}
		}
		/**
		 * Function to get proper Settings instance.
		 *
		 * @return mixed
		 */
		public function get_accounts_handler_instance() {

			if ( class_exists( 'MoNft\PremiumSettings' ) ) {
				return new \MoNft\PremiumSettings();
			} elseif ( class_exists( 'MoNft\AccountsHandler' ) ) {
				return new \MoNft\AccountsHandler();
			} else {
				wp_die( 'Please Change The version back to what it really was' );
				exit();
			}
		}
		/**
		 * Function to get proper Mint handler instance.
		 *
		 * @return mixed
		 */
		public function get_mint_handler_instance() {

			if ( class_exists( 'MoNft\controller\PremiumMintHandler' ) ) {
				return new \MoNft\controller\PremiumMintHandler();
			} elseif ( class_exists( 'MoNft\controller\MintHandler' ) ) {
				return new \MoNft\controller\MintHandler();
			} else {
				wp_die( 'Please Change The version back to what it really was' );
				exit();
			}
		}
		/**
		 * Function to get proper Transfer handler instance.
		 *
		 * @return mixed
		 */
		public function get_transfer_handler_instance() {

			if ( class_exists( 'MoNft\controller\PremiumTransferHandler' ) ) {
				return new \MoNft\controller\PremiumTransferHandler();
			} elseif ( class_exists( 'MoNft\controller\TransferHandler' ) ) {
				return new \MoNft\controller\TransferHandler();
			} else {
				wp_die( 'Please Change The version back to what it really was' );
				exit();
			}
		}
		/**
		 * Function to get upload handler instance
		 *
		 * @return mixed
		 */
		public function get_upload_handler_instance() {
			if ( class_exists( 'MoNft\controller\PremiumWoocommerceUploadHandler' ) ) {
				return new \MoNft\controller\PremiumWoocommerceUploadHandler();
			} elseif ( class_exists( 'MoNft\controller\BulkUploadHandler' ) ) {
				return new \MoNft\controller\BulkUploadHandler();
			} else {
				wp_die( 'Please Change The version back to what it really was' );
				exit();
			}
		}

		/**
		 * Function to get upload handler instance
		 *
		 * @return mixed
		 */
		public function get_branding_handler_instance() {
			if ( class_exists( 'MoNft\controller\PremiumBrandingHandler' ) ) {
				return new \MoNft\controller\PremiumBrandingHandler();
			} elseif ( class_exists( 'MoNft\controller\BrandingHandler' ) ) {
				return new \MoNft\controller\BrandingHandler();
			} else {
				wp_die( 'Please Change The version back to what it really was' );
				exit();
			}
		}
	}
}

