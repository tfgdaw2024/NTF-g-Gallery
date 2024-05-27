<?php
/**
 * Core
 *
 * Create MoNft Branding view.
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

if ( ! class_exists( 'MoNft\view\Branding' ) ) {
	/**
	 * Class to Create MoNft Method View Handler.
	 *
	 * @category Common, Core
	 * @package  MoNft\View\Branding
	 * @author   miniOrange <info@xecurify.com>
	 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
	 * @link     https://miniorange.com
	 */
	class Branding {

		/**
		 * Constructor
		 */
		public function __construct() {
			global $mo_nft_util;
			if ( true === $mo_nft_util->is_developer_mode ) {
				wp_enqueue_style( 'mo_nft_style_bu', MONFT_URL . 'classes/resources/css/dev/bootstrap/bootstrap.min.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_style( 'mo_nft_custom_style_bu', MONFT_URL . 'classes/resources/css/dev/styles.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
			} else {
				wp_enqueue_style( 'mo_nft_style_bu', MONFT_URL . 'classes/resources/css/prod/bootstrap/bootstrap.min.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
				wp_enqueue_style( 'mo_nft_custom_style_bu', MONFT_URL . 'classes/resources/css/prod/styles.min.css', array(), $ver = \MoNft\Constants::MONFT_VER_CURR, $in_footer = false );
			}
		}
		/**
		 * Function to render upload NFT metadata ui
		 *
		 * @return void
		 */
		public function render_branding_ui() {
			global $mo_nft_util;
			$page_id                = $mo_nft_util->get_option( 'monft_marketplace_page_id' );
			$user_id                = get_current_user_id();
			$banner_image           = get_user_meta( $user_id, 'mo_nft_collection_banner_image' );
			$profile_image          = get_user_meta( $user_id, 'mo_nft_collection_profile_image' );
			$collection_description = get_user_meta( $user_id, 'mo_nft_collection_description' );
			$collection_name        = get_user_meta( $user_id, 'mo_nft_collection_name' );
			?>
			<!DOCTYPE html>
			<html lang="en">

			<head>
				<meta charset="utf-8">
				<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
				<title>NFT Marketplace Branding</title>
			</head>

			<body>
				<div class="mo_support_layout_upload_metadata_container container">
					<div class="container-xl">
						<div class="mo_nft_single_metadata_header">
							<h2 class="text-black">Branding</h2>
							<a href=<?php echo esc_url( get_post_permalink( $page_id ) ); ?> target="_blank"
								class="mo-nft-btn-visit-marketplace btn">Visit Marketplace</a>
						</div>

						<div class="row justify-content-center">
							<div class="col-md-12" style="height: 450px;">
								<div class="mo-nft-content-card card" style="background-color: #fafafa;border:none;">
									<form enctype="multipart/form-data" method="post" action="" class="mo_nft_upload_metadata_form" style="overflow-y: auto;">
										<?php wp_nonce_field( 'branding_upload', 'mo_branding_upload_nonce' ); ?>
										<div class="form-row">
											<div class="mo-nft-form-group-zip col-md-12">
												<label>
													<h5>Upload a Collection Banner image in png/jpg</h5>
												</label>
												<a href="<?php echo esc_url( MONFT_URL . 'classes/resources/images/collection-banner-image.jpg' ); ?>"
													download><span class="dashicons dashicons-download"></span></a>Download a sample
												image
											</div>
											<div>
												<br />
												<input
													id="banner_image"
													type="file"
													accept=".png, .jpg, .jpeg"
													name="banner_image"
													onchange="previewImage(this)"
													hidden	
												/>
												<div style="display: flex; flex-direction: row; gap: 70px; alignItems: center;"
												>
												<label
													id="logo-btn"
													for="banner_image"
													style="height: 200px; width: 40%; border-radius: 1rem"
												>
												<?php
												if ( ! empty( $banner_image[0] ) ) {
													?>
													<img
													id = "banner"
														alt="image"
														width="300"
														height="180"
														src = "<?php echo esc_url( $banner_image[0] ); ?>"
														style="objectFit: contain; width:92%;"
													/>
													<?php
												} else {
													?>
													<img
														id = "banner"
														width="300"
														height="180"
														src = "<?php echo esc_url( MONFT_URL . 'classes/resources/images/empty-image.png' ); ?>"
														alt="image"
														style="objectFit: contain; width:92%;"
													/>
											<?php } ?>	
												</label>
											</div>
											<!-- </div> -->
											<div class="mo-nft-form-group-csv col-md-12">
												<label>
													<h5>Upload a Collection Profile image in png/jpg</h5>
												</label>
												<!-- <input type="file" name="profile_image" id="profile_image"
													accept=".png, .jpg, .jpeg" /> -->
												<a href="<?php echo esc_url( MONFT_URL . 'classes/resources/images/collection-profile-image.jpg' ); ?>"
													download><span class="dashicons dashicons-download"></span></a>Download a sample
												image
											</div>
											<div >
												<br>
												<input
													id="profile_image"
													type="file"
													accept=".png, .jpg, .jpeg"
													name="profile_image"
													onchange="previewImage(this)"
													hidden
												/>
												<div style="display: flex; flex-direction: row; gap: 70px; alignItems: center;"
												>
												<label
													id="logo-btn"
													for="profile_image"
													style="height: 200px; width: 40%; border-radius: 1rem"
												>
												<?php
												if ( ! empty( $profile_image[0] ) ) {
													?>
													<img
													id = "profile"
														alt="image"
														width="300"
														height="180"
														src = "<?php echo esc_url( $profile_image[0] ); ?>"
														style="objectFit: contain; width:92%;"
													/>
													<?php
												} else {
													?>
													<img
														id = "profile"
														width="300"
														height="180"
														src = "<?php echo esc_url( MONFT_URL . 'classes/resources/images/empty-image.png' ); ?>"
														alt="image"
														style="objectFit: contain; width:92%;"
													/>
												<?php } ?>
												</label>
											</div>
											<div class="mo-nft-form-group col-md-12">
												<label>
													<h5>Enter Collection Name</h5>
												</label>
												<textarea type="text" name="collection_name" id="collection_name" maxlength = "1000"><?php echo esc_attr( $collection_name[0] ); ?></textarea>
											</div>
											<div class="mo-nft-form-group col-md-12">
												<label>
													<h5>Enter Collection description</h5>
												</label>
												<textarea type="text" name="collection_description" id="collection_description" maxlength = "1000"><?php echo esc_attr( $collection_description[0] ); ?></textarea>
											</div>
											<div class="mo-nft-form-group col-md-12">
												<label>&nbsp;</label>
												<input type="submit" name="submit" value="Upload"
													class="btn mo-nft-btn btn-primary form-control" >
											</div>
										</div>
									</form>
									<form method="post" id="mo_nft_branding_image_size_error_form">
										<input type="hidden" name="option" value="mo_nft_branding_image_size_error" />
										<?php wp_nonce_field( 'mo_nft_branding_image_size_error', 'mo_nft_branding_image_size_error_nonce' ); ?>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
				</div>
			</body>
			</html>

			<script>
				function previewImage(input) {
					let id;
					if(input.name =='banner_image'){
						id = "banner"
					}else{
						id = "profile"
					}
					const file = input.files[0];
					const previewImage = document.getElementById(id);
					if (file) {
						const reader = new FileReader();
						reader.onload = function(e) {
							const img = new Image();
							img.onload = function(){

								const aspectRatio = img.width / img.height;
								if(id == 'banner'){
									if (aspectRatio < 1.4 || aspectRatio > 2.1) {
										jQuery("#mo_nft_branding_image_size_error_form").submit();
										return;
									}
								}
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
						previewImage.src = '';
						// If no file is selected, display an empty image
						// return
					}
				}
			</script>

			<?php
		}

	}
}
?>
