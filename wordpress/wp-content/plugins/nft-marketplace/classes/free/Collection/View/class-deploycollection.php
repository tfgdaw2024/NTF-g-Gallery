<?php //phpcs:ignore - system related error ignored.
/**
 * Core
 *
 * Create MoNft Deploy collection view.
 *
 * @category   Common, Core
 * @package    MoNft\View
 * @author     miniOrange <info@xecurify.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link       https://miniorange.com
 */
namespace MoNft\view;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'MoNft\view\DeployCollection' ) ) {
	/**
	 * Class to Create MoNft Method View Handler.
	 *
	 * @category Common, Core
	 * @package  MoNft\View\DeployCollection
	 * @author   miniOrange <info@xecurify.com>
	 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
	 * @link     https://miniorange.com
	 */
	class DeployCollection {

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
		 * Function to display deploy ui
		 *
		 * @return void
		 */
		public static function show_deploy_ui() {
			global $mo_nft_plugin_admin_url, $mo_nft_util;
			$page_id = $mo_nft_util->get_option( 'monft_marketplace_page_id' );
			?>
			<div class="monft_deploy_header">
				<h2 class="monft-text-black">Deploy NFT Collection</h2>
				<a href=<?php echo esc_url( get_post_permalink( $page_id ) ); ?> target="blank"
					class="mo-nft-btn-visit-marketplace btn">Visit Marketplace</a>
			</div>
			<div class="mo-nft-content-card-deploy card">
				<form id="monft-deploy-contract-form" class="monft-import-deploy-form">
					<div class="monft-input-fields-deploy">
						<label for="dropdown1">
							<h5><b>Blockchain</b><span style="color:red">*</span></h5>
						</label>
						<select id="monft-select-blockchain" name="dropdown1" required>
							<option value="sepolia">Sepolia</option>
							<option value="mumbai">Mumbai</option>
						</select>
					</div>
					<br>
					<div class="monft-input-fields-deploy">
						<label for="dropdown2">
							<h5><b>NFT Standard</b><span style="color:red">*</span></h5>
						</label>
						<select id="monft-select-standard" name="dropdown2" required>
							<option value="erc721">ERC-721</option>
						</select>

					</div>
					<br>
					<div class="monft-input-fields-deploy">
						<label for="collectionName">
							<h5><b>Collection Name</b><span style="color:red">*</span></h5>
						</label>
						<input name="collectionName" type="text" id="monft-select-collectionName" maxlength = "100" required value="">
					</div>
					<br>
					<div class="monft-input-fields-deploy">
						<label for="collectionSymbol">
							<h5><b>Collection Symbol</b><span style="color:red">*</span></h5>
						</label>
						<input name="collectionSymbol" type="text" id="monft-select-collectionSymbol" maxlength = '15' required value="">
					</div>
					<br>
					<button type="submit" class=" mo-nft-deploy-btn btn" id="monft-deploy-contract">Deploy</button>
					<a href="<?php echo esc_url( $mo_nft_plugin_admin_url ) . 'admin.php?page=mo_nft_settings&tab=addcollection'; ?>"
						class="mo-nft-back-btn btn">
						Back
					</a>
				</form>
				<form method="post" id="mo_nft_account_error_form">
					<input type="hidden" name="option" value="mo_nft_account_error" />
					<?php wp_nonce_field( 'mo_nft_account_error', 'mo_nft_account_error_nonce' ); ?>
				</form>
				<form method="post" id="mo_nft_deploy_success_form"
					action="<?php echo esc_url( $mo_nft_plugin_admin_url ) . 'admin.php?page=mo_nft_settings&tab=upload_nft_metadata'; ?>">
					<input type="hidden" name="option" value="mo_nft_deploy_success" />
					<?php wp_nonce_field( 'mo_nft_deploy_success', 'mo_nft_deploy_success_nonce' ); ?>
				</form>
			</div>
			<div name='monft_deploy_loader' id='monft_deploy_loader' class='monft_deploy_loader'>
				<div class='monft_deploy_loader_content'>
					<img src=<?php echo esc_url( MONFT_URL . 'classes/resources/images/loader.gif' ); ?> alt="buying_nfts" />
					<br><br>
					<h1 style="text-align: center;">Deploying your contract . . .</h1>
				</div>
			</div>
			<div name='monft_re-deploy' id='monft_re-deploy' class='monft_re-deploy'>
				<div class='monft_re-deploy_content'>
					<h1 style="text-align: center;"> You have already deployed one collection. If you want to deploy a new
						collection click on confirm, it will override the existing NFT collection</h1>
					<br><br>
					<div class="monft-re-deploy-actions"><input type="button" class=" mo-nft-re-deploy-cancel-btn btn"
							id="monft-re-deploy-cancel-contract" value="Cancel" /><input type="button"
							class=" mo-nft-re-deploy-btn btn" id="monft-re-deploy-contract" value="Confirm" /></div>
				</div>
			</div>
			<script>
				jQuery('#monft-deploy-contract-form').on("submit", async function (event) {
					event.preventDefault();
					const blockchain = jQuery('#monft-select-blockchain').val();
					const standard = jQuery('#monft-select-standard').val();
					const collectionName = jQuery('#monft-select-collectionName').val();
					const collectionSymbol = jQuery('#monft-select-collectionSymbol').val();
					let walletAddress;

					if (blockchain && standard && collectionName && collectionSymbol) {
						jQuery('#monft_deploy_loader').show();

						if ('sepolia' === blockchain) {
							chainId = '0xaa36a7';
							rpcUrls = "https://sepolia.infura.io/v3/";
							chainName = "Sepolia test network";
							name = "SepoliaETH";
							symbol = "SepoliaETH";
							blockExplorerUrls = "https://sepolia.etherscan.io";
						}
						else {
							chainId = '0x13881';
							rpcUrls = "https://rpc-mumbai.maticvigil.com/";
							chainName = "Mumbai";
							name = "MATIC",
								symbol = "MATIC",
								blockExplorerUrls = "https://mumbai.polygonscan.com/";
						}
						try {
							let accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
							walletAddress = accounts[0];

							try {
								await ethereum.request({
									method: 'wallet_switchEthereumChain',
									params: [{ chainId: chainId }],
								});
							} catch (switchError) {
								// This error code indicates that the chain has not been added to MetaMask.
								if (switchError.code === 4902) {

									try {
										const result = await window.ethereum.request({
											method: "wallet_addEthereumChain",
											params: [{
												chainId: chainId,
												rpcUrls: [rpcUrls],
												chainName: chainName,
												nativeCurrency: {
													name: name,
													symbol: symbol,
													decimals: 18
												},
												blockExplorerUrls: [blockExplorerUrls]
											}]
										})
									} catch (addNetworkError) {
										console.log(addNetworkError)
										jQuery('#monft_deploy_loader').hide();
										return;
									}
								} else {
									console.log(switchError)
									jQuery('#monft_deploy_loader').hide();
									return;
								}
							}
						} catch (connectError) {
							console.log(connectError)
							jQuery('#monft_deploy_loader').hide();
							return;
						}

						const connectedChainId = await window.ethereum.request({
							"method": "eth_chainId",
						});

						if (chainId === connectedChainId) {

							let deployContract = {
								'action': 'monft_deploy_contract',
								'request': 'deployContract',
								'blockchain': blockchain,
								'standard': standard,
								'collectionName': collectionName,
								'collectionSymbol': collectionSymbol,
								'walletAddress': walletAddress,
								'mo_nft_verify_nonce': '<?php echo esc_attr( wp_create_nonce( 'mo_nft_wp_nonce' ) ); ?>'
							};

							await jQuery.post(ajaxurl, deployContract, function (response) {
								get_response = response.status;
								if (false === response.success) {
									jQuery('#monft_deploy_loader').hide();
									jQuery("#mo_nft_account_error_form").submit();
								}
								else {
									contractAddress = response.contractAddress.toLowerCase();
									contractAbi = response.contractAbi;
								}
							});

							if (get_response) {
								contractAbi = JSON.stringify(contractAbi);
								tokenDetails = {
									'contractAddress': contractAddress,
									'blockchain': blockchain,
									'standard': standard,
									'contractABI': contractAbi,
									'collectionName':collectionName,
									'collectionSymbol': collectionSymbol,
								}
								let addTokenDetails = {
									'action': 'monft_free_settings',
									'request': 'addTokenDetails',
									'tokenDetails': tokenDetails,
									'mo_nft_verify_nonce': '<?php echo esc_attr( wp_create_nonce( 'mo_nft_wp_nonce' ) ); ?>'
								};
								await jQuery.post(ajaxurl, addTokenDetails, function (response) {
									if ("RECORD_EXIST" == response.data) {
										jQuery('#monft_deploy_loader').hide();
										jQuery('#monft_re-deploy').show();
									} else if ("SUCCESS" == response) {
										jQuery("#mo_nft_deploy_success_form").submit();
									}
									else {
										alert('Error occur!!. Please try again');
									}
								});
							}
						} else {
							jQuery('#monft_deploy_loader').hide();
							return;
						}
					}
				});

				jQuery("#monft-re-deploy-contract").click(async function () {
					let addTokenDetails = {
						'action': 'monft_import_collection',
						'request': 'addTokenDetails',
						'tokenDetails': tokenDetails,
						'mo_nft_verify_nonce': '<?php echo esc_attr( wp_create_nonce( 'mo_nft_wp_nonce' ) ); ?>'
					};
					await jQuery.post(ajaxurl, addTokenDetails, function (response) {
						if ('RECORD_UPDATED' == response.data) {
							jQuery("#mo_nft_deploy_success_form").submit();
						}
					});
				});
				jQuery("#monft-re-deploy-cancel-contract").click(async function () {
					jQuery('#monft_re-deploy').hide();
				});


				async function addBlockchainNetwork() {
					// Get the selected value of the select element
					var selectedBlockchain = document.getElementById("monft-select-blockchain").value;
					let ChainDetails = [];
					ChainDetails = getChainDetails(selectedBlockchain, '');
						try {
							await ethereum.request({
								method: "wallet_switchEthereumChain",
								params: [{ chainId: ChainDetails["chainId"] }],
							});
						} catch (switchError) {
						// This error code indicates that the chain has not been added to MetaMask.
						if (switchError.code === 4902) {
							// Do something
							try {
							const result = await window.ethereum.request({
								method: "wallet_addEthereumChain",
								params: [
								{
									chainId: ChainDetails["chainId"],
									rpcUrls: [ChainDetails["url"]],
									chainName: ChainDetails["name"],
									nativeCurrency: {
									name: ChainDetails["currName"],
									symbol: ChainDetails["currName"],
									decimals: 18,
									},
									blockExplorerUrls: [ChainDetails["blockUrl"]],
								},
								],
							});
							} catch (error) {
							console.log(error);
							}
						}
						}
				}

				// Add event listener to the select element
				document.getElementById("monft-select-blockchain").addEventListener("change", addBlockchainNetwork);

				function getChainDetails(chain, chainSymbol) {
					let ChainDetails = [];
					ChainDetails["chainSymbol"] = chainSymbol;
					switch (chain) {
						case "Polygon":
						if (!chainSymbol) {
							ChainDetails["chainSymbol"] = "MATIC";
						}
						ChainDetails["name"] = "Mumbai Testnet";
						ChainDetails["chainId"] = "0x13881";
						ChainDetails["url"] =
							"https://polygon-mumbai.g.alchemy.com/v2/cp_ROOTPfVxPFkH4qJmKZMPfWnuUZWg7";
						ChainDetails["currName"] = "Matic";
						ChainDetails["blockUrl"] = "https://mumbai.polygonscan.com/";
						break;
						case "mumbai":
						if (!chainSymbol) {
							ChainDetails["chainSymbol"] = "MATIC";
						}
						ChainDetails["name"] = "Mumbai Testnet";
						ChainDetails["chainId"] = "0x13881";
						ChainDetails["url"] =
							"https://polygon-mumbai.g.alchemy.com/v2/cp_ROOTPfVxPFkH4qJmKZMPfWnuUZWg7";
						ChainDetails["currName"] = "Matic";
						ChainDetails["blockUrl"] = "https://mumbai.polygonscan.com/";
						break;
						case "Goerli":
						if (!chainSymbol) {
							ChainDetails["chainSymbol"] = "ETH";
						}
						ChainDetails["name"] = "Goerli Testnet";
						ChainDetails["chainId"] = _ethers.utils.hexValue(5);
						ChainDetails["url"] =
							"https://eth-goerli.g.alchemy.com/v2/O9TvFMdLZ2_fJTBbChS9mz0RB5We_EJ_";
						ChainDetails["currName"] = "Ether";
						ChainDetails["blockUrl"] = "https://goerli.etherscan.io/";
						break;
						case "sepolia":
						if (!chainSymbol) {
							ChainDetails["chainSymbol"] = "ETH";
						}
						ChainDetails["name"] = "Sepolia test network";
						ChainDetails["chainId"] = "0xAA36A7";
						ChainDetails["url"] =
							"https://eth-sepolia.g.alchemy.com/v2/g5TxwTxkPhEZu4jMAB4OFYnhGH4oi2hC";
						ChainDetails["currName"] = "Ether";
						ChainDetails["blockUrl"] = "https://sepolia.etherscan.io";
						break;
						case "Ethereum-Mainnet":
						if (!chainSymbol) {
							ChainDetails["chainSymbol"] = "ETH";
						}
						ChainDetails["name"] = "Ethereum Mainnet";
						ChainDetails["chainId"] = _ethers.utils.hexValue(1);
						ChainDetails["url"] = "https://eth.llamarpc.com";
						ChainDetails["currName"] = "ETH";
						ChainDetails["blockUrl"] = "https://etherscan.io/";
						break;
						case "Binance":
						if (!chainSymbol) {
							ChainDetails["chainSymbol"] = "BNB";
						}
						ChainDetails["name"] = "Binance Testnet";
						ChainDetails["chainId"] = _ethers.utils.hexValue(97);
						ChainDetails["url"] = "https://data-seed-prebsc-1-s2.binance.org:8545";
						ChainDetails["currName"] = "BNB";
						ChainDetails["blockUrl"] = "https://testnet.bscscan.com/";
						break;
				}
				return ChainDetails;
		}
			</script>
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
