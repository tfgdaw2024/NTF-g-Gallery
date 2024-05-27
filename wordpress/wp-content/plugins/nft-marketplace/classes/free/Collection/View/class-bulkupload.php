<?php
/**
 * Core
 *
 * Create MoNft bulkupload view.
 *
 * @category   Common, Core
 * @package    MoNft\View
 * @author     miniOrange <info@xecurify.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoNft\view;

use MoNft\Base\InstanceHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MoNft\view\BulkUpload' ) ) {
	/**
	 * Class to Create MoNft Method View Handler.
	 *
	 * @category Common, Core
	 * @package  MoNft\View\BulkUpload
	 * @author   miniOrange <info@xecurify.com>
	 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
	 * @link     https://miniorange.com
	 */
	class BulkUpload {

		/**
		 * Constructor
		 */
		public function __construct() {
			global $mo_nft_util;
			if ( true === $mo_nft_util->is_developer_mode ) {
				wp_enqueue_style( 'mo_nft_style_bu', MONFT_URL . 'classes/resources/css/dev/bootstrap/bootstrap.min.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_style( 'mo_nft_custom_style_bu', MONFT_URL . 'classes/resources/css/dev/styles.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'mo_nft_web3min_bu', MONFT_URL . 'classes/resources/js/web3/dev/web3Min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
			} else {
				wp_enqueue_style( 'mo_nft_style_bu', MONFT_URL . 'classes/resources/css/prod/bootstrap/bootstrap.min.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_style( 'mo_nft_custom_style_bu', MONFT_URL . 'classes/resources/css/prod/styles.min.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_script( 'mo_nft_web3min_bu', MONFT_URL . 'classes/resources/js/web3/prod/web3Min.js', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
			}
		}
		/**
		 * Function to render upload NFT metadata ui
		 *
		 * @return void
		 */
		public function render_nft_metadata_ui() {
			$currentaction = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( ( $_GET['action'] ) ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring nonce verification because we are fetching data from URL and not on form submission.
			?>
			<div id="web3-method" >
			<?php
			if ( 'bulk_upload' === esc_attr( $currentaction ) ) {
				$this->show_bulk_upload_ui();
			} else {
				$this->upload_single_nft_metadata_view();
			}
			?>

			</div>
		<br>
			<?php
		}
		/**
		 * Function to display bulk nft metadata view
		 *
		 * @return void
		 */
		public function show_bulk_upload_ui() {
			global  $mo_nft_util,$mo_nft_plugin_admin_url;
			$config_object               = $mo_nft_util->get_option( 'mo_nft_token_config_details_store' );
			$collection_names            = array();
			$mo_nft_token_config_details = $mo_nft_util->get_option( 'mo_nft_token_config_details_store' );
			if ( $mo_nft_token_config_details ) {
				foreach ( $mo_nft_token_config_details as $key => $token_details_row ) {
					$collection_names[ $key ] = $mo_nft_token_config_details[ $key ]['contractAddressName'];
				}
			}
			$page_id = $mo_nft_util->get_option( 'monft_marketplace_page_id' );
			?>

		<!DOCTYPE html>
		<html lang="en">
			<head>
				<meta charset="utf-8">
				<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
				<title>Bulk Upload NFT Metadata</title>
			</head>
			<body>
				<div class="mo_support_layout_upload_metadata_container container">
					<div class="container-xl">
								<div class="mo_nft_bulk_metadata_header">
									<h2 class="text-black">Bulk Upload NFT Metadata</h2>
									<a href=<?php echo esc_url( get_post_permalink( $page_id ) ); ?> target="blank" class="mo-nft-btn-visit-marketplace btn">Visit Marketplace</a>
								</div>
						<div class="row justify-content-center">
							<div class="col-md-12">
								<div class="mo-nft-content-card card" style="background-color: #fafafa;border:none;">
									<form enctype="multipart/form-data" method="post" action="" class="mo_nft_upload_metadata_form">
									<?php wp_nonce_field( 'upload_zip_file', 'mo_nft_upload_zip_file_nonce' ); ?>
										<div class="form-row">
											<a href="<?php echo esc_url( $mo_nft_plugin_admin_url ) . 'admin.php?page=mo_nft_settings&tab=upload_nft_metadata&action=upload_nft_metadata'; ?>" class="btn monft-single-upload-nft-btn" >
															Single Upload NFT
											</a>
										<?php
											$content = '';
											$content = apply_filters(
												'mo_nft_create_woocommerce_product_view',
												$content,
												$collection_names
											);
										?>
											<div class="mo-nft-form-group-zip col-md-12">
												<div>
													<label><h5>Upload a zip of NFT collection</h5></label>
													<input required type="file" name="zip_file" id="zip_file"/>
													<a href="<?php echo esc_url( MONFT_URL . 'classes/resources/images/Images.zip' ); ?>" download><span class="dashicons dashicons-download"></span></a>Download a sample zip		
												</div>
											</div>
											<div class="mo-nft-form-group-csv col-md-12">
												<label><h5>Upload a NFT Metadata CSV File</h5></label>
												<input type="file" name="csv_file" id="csv_file" accept=".csv" required/>
												<a href="<?php echo esc_url( MONFT_URL . 'classes/resources/images/NFT-Metadata.csv' ); ?>" download><span class="dashicons dashicons-download"></span></a>Download a sample csv
											</div>
											<div class="mo-nft-form-group col-md-12">
												<label>&nbsp;</label>
												<input type="submit" name="submit" value="Upload" class="btn mo-nft-btn btn-primary form-control">
											</div>
										</div>
										<a href="<?php echo esc_url( $mo_nft_plugin_admin_url ) . 'admin.php?page=mo_nft_settings&tab=upload_nft_metadata'; ?>" class="mo-nft-back-btn btn">
											Back
										</a>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</body>
		</html>

			<?php
		}
		/**
		 * Function to display single nft metadata view
		 *
		 * @return void
		 */
		public function upload_single_nft_metadata_view() {
			global $mo_nft_plugin_admin_url, $mo_nft_util, $mo_nft_plugin_dir_url;
			$redirect_ic                 = $mo_nft_plugin_dir_url . 'classes/resources/images/redirect_ic.png';
			$collection_names            = array();
			$mo_nft_token_config_details = $mo_nft_util->get_option( 'mo_nft_token_config_details_store' );
			if ( $mo_nft_token_config_details ) {
				foreach ( $mo_nft_token_config_details as $key => $token_details_row ) {
					$collection_names[ $key ] = $mo_nft_token_config_details[ $key ]['contractAddress'];
					$blockchain               = $mo_nft_token_config_details[ $key ]['blockchain'];
					$contract_address         = $mo_nft_token_config_details[ $key ]['contractAddress'];
				}
			}
			$page_id = $mo_nft_util->get_option( 'monft_marketplace_page_id' );
			?>
			<!DOCTYPE html>
		<html lang="en">
			<head>
				<meta charset="utf-8">
				<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
				<title>Upload NFT metadata</title>
			</head>
			<body>
				<div class="mo_support_layout_upload_metadata_container container">
					<div class="container-xl">
								<div class="mo_nft_single_metadata_header">
									<h2 class="text-black">Upload NFT Metadata</h2>
									<a href=<?php echo esc_url( get_post_permalink( $page_id ) ); ?> target="blank" class="mo-nft-btn-visit-marketplace btn">Visit Marketplace</a>	
								</div>
						<div class="row justify-content-center">
							<div class="col-md-12" style="height: 500px;">
								<div class="mo-nft-content-card card" style="background-color: #fafafa;border:none;">
									<form enctype="multipart/form-data" method="post" class= "mo_nft_upload_metadata_form" action="" style="overflow-y: auto;">
										<?php wp_nonce_field( 'upload_single_file', 'mo_nft_upload_single_file_nonce' ); ?>
																<div class="form-row">
										<?php
										$content = '';
										$content = apply_filters(
											'mo_nft_create_woocommerce_product_view',
											$content,
											$collection_names
										);
										?>
											<div class="mo-nft-form-group-image-file">
											<?php
											if ( 'sepolia' === $blockchain ) {
												?>
										<a href="<?php echo esc_attr( \MoNft\Constants::SEPOLIASCAN_TESTNET_URL . $contract_address . '#code' ); ?>"  target="_blank" class="monft_contract_redirect">View Contract <img class="mo-nft-redirect_ic_contract" src="<?php echo( esc_url( $redirect_ic ) ); ?>" height="24px"/></a>
												<?php
											} elseif ( 'mumbai' === $blockchain ) {
												?>
										<a href="<?php echo esc_attr( \MoNft\Constants::POLYSCAN_TESTNET_URL . $contract_address . '#code' ); ?>"  target="_blank" class="monft_contract_redirect">View Contract <img class="mo-nft-redirect_ic_contract" src="<?php echo( esc_url( $redirect_ic ) ); ?>" height="24px"/></a>
												<?php
											} else {
												?>
										<a href="" target="_blank" class=" mo-nft-btn btn" hidden>View Contract<img class="mo-nft-redirect_ic_contract" src="<?php echo( esc_url( $redirect_ic ) ); ?>" height="24px"/></a>
												<?php
											}
											?>
												<label><h5>Upload a jpeg/png file that contains image of your NFT </h5><p>or</p><h5 class="mo-nft-bulk-upload-option">Bulk Upload NFTs</h5></label><br>
											</div>
											<div style="display:flex;">
												<div class="mo-nft-single-upload-fields" style="width: 29%;">
												<input required type="file" name="image_file" id="image_file" accept="image/png, image/jpeg" onchange="previewImage(this)" style="width: 90%;"/>
													<h5>Enter NFT Metadata</h5><br>
													<label for="nft_name">Name</label>
													<input required type="text" placeholder="NFT Name" name="nft_name" id="nft_name" class="monft-attributes-input"/><br>
													<label for="nft_description">Description</label>
														<textarea type="text" placeholder="Please enter a description for your NFT...." rows="3" name="monft_single_nft_description" id="monft_single_nft_description" class="monft-attributes-input" ></textarea>
													<br>
												</div>
												<div id="image-preview-container" style="width:43%;">
													<img id="preview-image" src="<?php echo esc_url( MONFT_URL . 'classes/resources/images/empty-image.png' ); ?>" alt="Image Preview" style=" border: solid 2px grey;margin-left:15%;width: 60%; height: 71%; object-fit: cover;">
												</div>
												<div style="border-left: 1px solid;width=32%;">
													<a href="<?php echo esc_url( $mo_nft_plugin_admin_url ) . 'admin.php?page=mo_nft_settings&tab=upload_nft_metadata&action=bulk_upload'; ?>" class="btn monft-bulk-upload-nft-btn" >
													Bulk Upload NFT
													</a>
												</div>
										</div>
											<div class="form-group col-md-6">
											<label for="nft_attributes">Attributes</label>
													<div class="mo-nft-attribute-fields">
														<div id="input-container">
															<div class="mo-nft-attributes-input-group input-group">
																<input required type="text" placeholder="Attribute Name" name="nft_attributes_name[]" id="nft_attributes_name" class="monft-attributes-input" />
																<input required type="text" placeholder="Attribute Value" name="nft_attributes_value[]" id="nft_attributes_value" class="monft-attributes-value-input"/>
															</div>
														</div>
														<span class="dashicons dashicons-plus-alt" id="add-button"></span>
													</div>
												<label>&nbsp;</label>
												<input type="submit" name="submit" value="Upload" class="btn mo-nft-btn btn-primary form-control">
											</div>
										</div>
										<a href="<?php echo esc_url( $mo_nft_plugin_admin_url ) . 'admin.php?page=mo_nft_settings&tab=import_deploy'; ?>" class="mo-nft-back-btn btn">
											Back
										</a>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</body>
		</html>
		<script>
			jQuery(document).ready(function() {
				jQuery("#add-button").click(function() {
				var newRow = jQuery(".input-group:first").clone(); // Clone the first input row
				newRow.find("input").val(""); // Clear the values of input fields in the new row
				jQuery(newRow).append('<span class="dashicons dashicons-remove"></span>');
				jQuery("#input-container").append(newRow); // Append the new row to the container
				jQuery(".dashicons-remove").click(function() {
					jQuery(this).closest(".input-group").remove();
					var deleteIcon = jQuery(this).find(".dashicons-remove");
					deleteIcon.hide();
				});
			});
			});



			function previewImage(input) {
					const previewImage = document.getElementById('preview-image');
					const file = input.files[0];

					if (file) {
						const reader = new FileReader();
						reader.onload = function(e) {
							const img = new Image();
							img.onload = function() {

								const canvas = document.createElement('canvas');
								const ctx = canvas.getContext('2d');

								// Set the desired width and height
								const maxWidth = 200; // Adjust as needed
								const maxHeight = 200; // Adjust as needed

								let width = img.width;
								let height = img.height;

								// Calculate the aspect ratio to maintain proportions
								if (width > height) {
									if (width > maxWidth) {
										height *= maxWidth / width;
										width = maxWidth;
									}
								} else {
									if (height > maxHeight) {
										width *= maxHeight / height;
										height = maxHeight;
									}
								}

								// Resize the canvas and draw the image
								canvas.width = width;
								canvas.height = height;
								ctx.drawImage(img, 0, 0, width, height);

								// Update the src attribute of the preview image
								previewImage.src = canvas.toDataURL('image/jpeg');
							};
							img.src = e.target.result;
						};
						reader.readAsDataURL(file);
					} else {
						// If no file is selected, display an empty image
						previewImage.src = '';
					}
				}
			</script>
			<?php
		}
	}
}
?>
