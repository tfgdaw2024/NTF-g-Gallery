<?php
/**
 * Core
 *
 * Create MoNft Import collection view.
 *
 * @category   Common, Core
 * @package    MoNft\View
 * @author     miniOrange <info@xecurify.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoNft\view;

use MoNft\view\DeployCollection;
use MoNft\view\PremiumImportCollection;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'MoNft\view\ImportCollection' ) ) {
	/**
	 * Class to Create MoNft Method View Handler.
	 *
	 * @category Common, Core
	 * @package  MoNft\View\ImportCollection
	 * @author   miniOrange <info@xecurify.com>
	 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
	 * @link     https://miniorange.com
	 */
	class ImportCollection {
		/**
		 * Constructor
		 */
		public function __construct() {
			global $wpdb, $mo_nft_util;
			if ( true === $mo_nft_util->is_developer_mode ) {
				wp_enqueue_style( 'mo_nft_style', MONFT_URL . 'classes/resources/css/dev/bootstrap/bootstrap.min.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_style( 'mo_nft_custom_style', MONFT_URL . 'classes/resources/css/dev/styles.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'mo_nft_ethersmin', MONFT_URL . 'classes/resources/js/web3/dev/ethers-5.2.esm.min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'mo_nft_web3min', MONFT_URL . 'classes/resources/js/web3/dev/web3Min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'mo_nft_mintnft_settings', MONFT_URL . 'classes/free/Resources/js/web3/dev/mintNFT.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = true );
			} else {
				wp_enqueue_style( 'mo_nft_style', MONFT_URL . 'classes/resources/css/prod/bootstrap/bootstrap.min.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_style( 'mo_nft_custom_style', MONFT_URL . 'classes/resources/css/prod/styles.min.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'mo_nft_ethersmin', MONFT_URL . 'classes/resources/js/web3/prod/ethers-5.2.esm.min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'mo_nft_web3min', MONFT_URL . 'classes/resources/js/web3/prod/web3Min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'mo_nft_mintnft_settings', MONFT_URL . 'classes/free/Resources/js/web3/prod/mintNFT.min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = true );
			}

			$result = $mo_nft_util->get_option( 'mo_nft_collection' );

			if ( isset( $result[0] ) && $result[0] ) {
				wp_localize_script(
					'mo_nft_mint_function',
					'mo_nft_wallet_object',
					array(
						'collection_address' => $result[0]->option_value,
					)
				);
			}

		}

		/**
		 * Function to render Import/Deploy ui.
		 *
		 * @return void
		 */
		public function render_import_ui() {
			$currentaction = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( ( $_GET['action'] ) ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring nonce verification because we are fetching data from URL and not on form submission.
			?>
			<div id="web3-method" >
			<?php
			if ( 'deploy' === esc_attr( $currentaction ) ) {
				DeployCollection::show_deploy_ui();
			} else {
				if ( class_exists( 'MoNft\Base\PremiumBaseStructure' ) ) {
					PremiumImportCollection::render_ui();
				} else {
					$this->show_import_ui();
				}
			}
			?>

			</div>
		<br>
			<?php
		}
		/**
		 * Function to display import ui.
		 *
		 * @return void
		 */
		public function show_import_ui() {
			global $mo_nft_plugin_admin_url, $mo_nft_util;
			$mo_nft_token_config_details = $mo_nft_util->get_option( 'mo_nft_token_config_details_store' );
			$page_id                     = $mo_nft_util->get_option( 'monft_marketplace_page_id' );
			?>
			<div class="monft_import_header">
				<h2 class="text-black">Import NFT Collection</h2>	
				<a href=<?php echo esc_url( get_post_permalink( $page_id ) ); ?> target="blank" class="mo-nft-btn-visit-marketplace btn">Visit Marketplace</a>
			</div>
			<div class="mo-nft-content-card-import card">
			<div id='mo_error_message' class='monft-notice monft-error'><div class='monft-notice-close dashicons dashicons-no-alt'></div></div>
				<form class = "monft-import-deploy-form">
				<div style="width=32%;">
						<a href="<?php echo esc_url( $mo_nft_plugin_admin_url ) . 'admin.php?page=mo_nft_settings&tab=import_deploy&action=deploy'; ?>" class="btn monft-deploy-collection-btn" >
						Deploy Contract
						</a>
					</div>
					<div class="monft-input-fields-import">
						<label for="dropdown1"><h5><b>Blockchain</b><span style="color:red">*</span></h5></label>
						<select class="form-select" placeholder="Blockchain" id="monftBlockchain" name="blockchain[]" required>
							<option  value="sepolia">Sepolia</option>
							<option  value ="mumbai">Mumbai</option>
						</select>
					</div>
					<br>
					<div class="monft-input-fields-import">
						<label><h5><b>NFT Contract Address</b><span style="color:red">*</span></h5></label>
						<input id="monftContractAddress" type="text" class="form-control" placeholder="0xE72385fB3A2d88893EA3DeA6B8a59ACa89Dea4E9" required>
					</div>
					<br>
					<div class="monft-input-fields-import">
						<label for="dropdown2"><h5><b>NFT Standard</b><span style="color:red">*</span></h5></label>
						<select id="monftStandard" class="form-select" name="dropdown2" required>
						<option value="ERC-721">ERC-721</option>
						</select>
					</div>
					<br>
					<div class="monft-input-fields-import">
						<label><h5><b>Collection contract ABI</b><span style="color:red">*</span></h5></label>
						<input id="monftABI" type="text" class="form-control" required><a class="monft-get-contract-abi-link" onClick="get_abi();" target="_blank" class="monft-get-contract-ABI">Click here to get the contract ABI</a>
					</div>
					<br>
						<input type="button" id="monftAddTokenDetails" class="mo-nft-AddTokenDetails btn" value="Import">
						<a href="<?php echo esc_url( $mo_nft_plugin_admin_url ) . 'admin.php?page=mo_nft_settings&tab=addcollection'; ?>" class="mo-nft-back-btn btn">
							Back
						</a>
				</form>
				<form method="post" id="mo_nft_account_error_form">				
				<input type="hidden" name="option" value="mo_nft_account_error" />
				<?php wp_nonce_field( 'mo_nft_account_error', 'mo_nft_account_error_nonce' ); ?>
				</form>
				<form method="post" id="mo_nft_import_success_form" action="<?php echo esc_url( $mo_nft_plugin_admin_url ) . 'admin.php?page=mo_nft_settings&tab=upload_nft_metadata'; ?>">				
				<input type="hidden" name="option" value="mo_nft_import_success" />
				<?php wp_nonce_field( 'mo_nft_import_success', 'mo_nft_import_success_nonce' ); ?>
				</form>
			</div>
			<div name='monft_deploy_loader' id='monft_import_loader' class='monft_deploy_loader'>
				<div class='monft_deploy_loader_content'>
					<img src=<?php echo esc_url( MONFT_URL . 'classes/resources/images/loader.gif' ); ?> alt="buying_nfts" />
					<br><br>
					<h1 style="text-align: center;">Importing your contract . . .</h1>
				</div>
			</div>
			<div name='monft_re-import' id='monft_re-import' class='monft_re-import'> 
				<div class='monft_re-import_content'>
				<h1 style="text-align: center;"> You have already imported one collection. If you want to import a new collection click on confirm, it will override the existing NFT collection</h1>
				<br><br><div class="monft-re-import-actions"><input type="button" class=" mo-nft-re-import-cancel-btn btn" id="monft-re-import-cancel-contract" value="Cancel"/><input type="button" class=" mo-nft-re-import-btn btn" id="monft-re-import-contract" value="Confirm"/></div>
				</div>	
			</div>
			<script>
				function get_abi(){
					contractAddress = jQuery('#monftContractAddress').val();
					blockchain = jQuery('#monftBlockchain').val();

					if('Goerli' === blockchain){
						url = "<?php echo esc_url( \MoNft\Constants::GOERLISCAN_TESTNET_URL ); ?>" + contractAddress + "#code";
					}else if('sepolia' === blockchain){
						url = "<?php echo esc_url( \MoNft\Constants::SEPOLIASCAN_TESTNET_URL ); ?>" + contractAddress + "#code";
					}else if('Ethereum-Mainnet' === blockchain){
						url = "<?php echo esc_url( \MoNft\Constants::ETHERSCAN_URL ); ?>" + contractAddress + "#code";
					}else if('mumbai' === blockchain){
						url = "<?php echo esc_url( \MoNft\Constants::POLYSCAN_TESTNET_URL ); ?>" + contractAddress + "#code";
					}else{
						url = "<?php echo esc_url( \MoNft\Constants::BSCSCAN_TESTNET_URL ); ?>" + contractAddress + "#code";
					}
					window.open(url, '_blank');
				}
				jQuery("#monftAddTokenDetails").click(async function(){
					jQuery('#mo_error_message').slideUp();
						<?php if ( ! $mo_nft_token_config_details ) { ?>
					jQuery('#monftNoTokenDetails').remove();
							<?php
						}
						$admin_api_key = $mo_nft_util->get_option( 'mo_nft_admin_api_key' );
						if ( empty( $admin_api_key ) ) {
							?>
						jQuery("#mo_nft_account_error_form").submit();
							<?php
						} else {
							?>
					const contractAddress = jQuery('#monftContractAddress').val();
					const blockchain = jQuery('#monftBlockchain').val();
					const standard = jQuery('#monftStandard').val();
					const contractABI = jQuery('#monftABI').val();			

					if('' === contractAddress || '' === contractABI ){
						jQuery('#mo_error_message').text("Please fill the required fields");
						jQuery('#mo_error_message').slideToggle();
						// Wait for 4 seconds (3000 milliseconds) and then slide up the element
						setTimeout(function() {
							jQuery('#mo_error_message').slideUp();
						}, 3000);
					}else{
						jQuery('#monft_import_loader').show()
						tokenDetails = {
						'contractAddress':contractAddress.toLowerCase(),
						'blockchain':blockchain,
						'standard':standard,
						'contractABI':contractABI,
					}	

					let collectionDetails = {
						'contractAddress':contractAddress.toLowerCase(),
						'blockchain':blockchain,
					}
					let getCollectionDetails = {
						'action':'monft_import_collection_details', 
						'request':'getCollectionName',
						'collectionDetails':collectionDetails,
						'mo_nft_verify_nonce':'<?php echo esc_attr( wp_create_nonce( 'mo_nft_wp_nonce' ) ); ?>'
					};

					let errorOccured = false
					await jQuery.post(ajaxurl,getCollectionDetails,function(response) {
						if("SUCCESS" === response.data.status){
							if(!response.data.token_address ){
								jQuery('#mo_error_message').text("Please provide valid Contract Address from the selected Network");
								jQuery('#mo_error_message').slideToggle();
								jQuery('#monft_import_loader').hide();

							}else if("ERC721" !== response.data.contract_type){
								jQuery('#mo_error_message').text("Please provide valid Token Standard for selected Contract Address");
								jQuery('#mo_error_message').slideToggle();
								jQuery('#monft_import_loader').hide();

							}else{
								tokenDetails['collectionName']=response.data.name;
								tokenDetails['collectionSymbol']=response.data.symbol;
								errorOccured = true
							}
					}else{
						jQuery('#mo_error_message').text("Error occur!!. Please try again");
						jQuery('#mo_error_message').slideToggle();
						jQuery('#monft_import_loader').hide();
					}

					});
					if(!errorOccured){
						return
					}

					let addTokenDetails = {
						'action':'monft_free_settings', 
						'request':'addTokenDetails',
						'tokenDetails':tokenDetails,
						'mo_nft_verify_nonce':'<?php echo esc_attr( wp_create_nonce( 'mo_nft_wp_nonce' ) ); ?>'
					};

					await jQuery.post(ajaxurl,addTokenDetails,function(response) {
						if("SUCCESS" == response){
							jQuery("#mo_nft_import_success_form").submit();
						}else if("DUPLICATE_ENTRY" == response){
							jQuery('#monft_import_loader').hide();
							alert('Duplicate Entry: Contract address name should be unique');
						}
						else if("RECORD_EXIST" == response.data){
							jQuery('#monft_import_loader').hide();
							jQuery('#monft_re-import').show();
						}
						else{
							jQuery('#monft_import_loader').hide();
							alert('Error occur!!. Please try again');
						}
					});
					}
							<?php
						}
						?>
				});
				jQuery("#monft-re-import-contract").click(async function(){
					jQuery('#monft_re-import').hide();
					let addTokenDetails = {
						'action':'monft_import_collection', 
						'request':'addTokenDetails',
						'tokenDetails':tokenDetails,
						'mo_nft_verify_nonce':'<?php echo esc_attr( wp_create_nonce( 'mo_nft_wp_nonce' ) ); ?>'
					};
					await jQuery.post(ajaxurl,addTokenDetails,function(response){
						if('RECORD_UPDATED' == response.data){
							jQuery("#mo_nft_import_success_form").submit();
						}
					});
				});
				jQuery("#monft-re-import-cancel-contract").click(async function(){
					jQuery('#monft_re-import').hide();
				});
</script>

</html>

			<?php
		}
		/**
		 * Enqueue web3 related scripts
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
	}
}
?>
