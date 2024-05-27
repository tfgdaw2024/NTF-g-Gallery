<?php
/**
 * Accounts
 *
 * WEB3 Premium Accounts.
 *
 * @category   Core
 * @package    MoNft\Accounts
 * @author     miniOrange <info@xecurify.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoNft;

use MoNft\MoNftSupport;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MoNft\Accounts' ) ) {

	/**
	 * Class to save and render NFT Marketplace Accounts
	 *
	 * @category Core, Accounts
	 * @package  MoNft\Accounts\Accounts
	 * @author   miniOrange <info@xecurify.com>
	 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
	 * @link     https://miniorange.com
	 */
	class Accounts {
		/**
		 * Function to display licensing info.
		 *
		 * @return void
		 */
		public function mo_nft_lp() {
			$this->mo_nft_wp_enqueue();

			$code = '';
			if ( isset( $_POST['mo_nft_verify_license_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_nft_verify_license_nonce'] ) ), 'mo_nft_verify_license' ) ) {
				$code = isset( $_POST['mo_nft_license_key'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_nft_license_key'] ) ) : null;
			}

			?>
				<div class="mo_support_layout" style="width:96%">
					<h3>Verify your license [ <span style="font-size:13px;font-style:normal;"><a style="cursor:pointer;" href="https://login.xecurify.com/moas/login?redirectUrl=https://login.xecurify.com/moas/admin/customer/viewlicensekeys" target="_blank" >Click here to view your license key</a></span> ]</h3>
					<hr>
					<form name="f" method="post" action="">
						<input type="hidden" name="option" value="mo_nft_verify_license" />
						<?php wp_nonce_field( 'mo_nft_verify_license', 'mo_nft_verify_license_nonce' ); ?>
						<table class="mo_settings_table">
							<tr>
								<td><strong><p color="#FF0000">*</p>License Key:</strong></td>
								<td><input style="width:350px;" required type="text" name="mo_nft_license_key" placeholder="Enter your license key to activate the plugin" value="<?php echo esc_attr( $code ); ?>" /></td>
							</tr>
							<tr>
							<td colspan="2" style="font-size: 13px;">
							<br>1. License key you have entered here is associated with this site instance. In future, if you are re-installing the plugin or your site for any reason, you should deactivate the plugin from the current WordPress domain. It would free your License Key and allow you to activate this plugin on other domain/site.<br><br>
							2. This is not a developer's license. You may not modify the content or any part thereof, except as explicitly permitted under this plugin. Making any kind of change to the plugin's code may make the plugin unusable.
							<br><br>
							<p>&nbsp;<input style="margin-left:20px;" required type="checkbox" name="license_conditions"/><strong>I accept the above Terms and Conditions.</strong></p></td>
						</tr>
						</table>
						<br>
						<input style="margin-left:30%; font-size: 14px;" type="submit" name="submit" value="Activate License" class="mo-nft-btn btn" />
						<br><br>
					</form>
				</div>
			<?php
		}
		/**
		 * Function to render UI.
		 */
		public function render_account_ui() {
			global $mo_nft_util;
			if ( $mo_nft_util->is_customer_registered() ) {
				?>

			<h4>Thank you for registering with miniOrange</h4>
			<h6>Find your account details below</h6>
			<div id="web3-method" >
				<?php
				self::show_customer_info();
				?>
			</div>

				<?php

			} else {
				?>
			<div class="mo_nft_account_register">
				<h4>Register with miniOrange</h4>
				<div class="monft-head-desc">An account is required to purchase the Premium version of the plugin</div>
				<div id="web3-method" >
					<?php
						self::show_new_registration_page();
					?>
				</div>
			</div>
			<div class="mo_nft_account_login">
			<h4>Login with miniOrange</h4>
				<h6>Use your existing miniOrange account to log in to the Plugin</h6>
				<div id="web3-method" >
					<?php
					self::verify_password_ui();
					?>
				</div>
		</div>
				<?php
			}
		}
		/**
		 * Function to enqueue scripts
		 *
		 * @return void
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
		/**
		 * Function to register.
		 *
		 * @return void
		 */
		public function register() {
			global $mo_nft_util;

			if ( $mo_nft_util->is_customer_registered() ) {
				self::show_customer_info();
			} else {
				self::verify_password_ui();
			}
		}

		/**
		 * Function to render new registrations page.
		 */
		public function show_new_registration_page() {
			global $mo_nft_util;
			$this->mo_nft_wp_enqueue();
			$mo_nft_util->update_option( 'mo_nft_new_registration', 'true' );
			$current_user = wp_get_current_user();
			?>
		<!--Register with miniOrange-->
		<div class="mo-nft-content-card" style="background-color: #fafafa;">
		<form name="f" method="post" action="" class="mo-nft-registration-form">
			<input type="hidden" name="option" value="mo_nft_register_customer" />
			<?php wp_nonce_field( 'mo_nft_register_customer', 'mo_nft_register_customer_nonce' ); ?>
			<div>

			<h5>Email Address</h5>
				<label class="mo-nft-email-label">
					<input type="text" autofocus autocomplete="off" placeholder="test@example.org" name="email"
					value="<?php echo esc_attr( $mo_nft_util->get_option( 'mo_nft_admin_email' ) ); ?>"/>
				</label>
				<h5>Password</h5>
				<div class="row">
					<div class="col">
						<label>
							<input placeholder="********" type="password" name="password" class="monft_registration_input" />
						</label>
					</div>
					<div class="col">
						*Minimum length must be 8 characters
					</div>
				</div>
				<h5>Confirm Password</h5>
				<div class="row">
					<div class="col">
						<label>
							<input placeholder="********" type="password" name="confirmPassword" class="monft_registration_input" />
						</label>
					</div>
					<div class="col">
						Confirm the Password
					</div>
				</div>
				<!--Hidden-->
				<div class="hidden">
					<div class="col">
						<label>
							<input placeholder="First Name" type="text" name="fname" 
							value="<?php echo esc_attr( $current_user->user_firstname ); ?>"/>
						</label>
					</div>
				</div>
				<div class="hidden">
					<div class="col">
						<label>
							<input placeholder="Last Name" type="text" name="lname" 
							value="<?php echo esc_attr( $current_user->user_lastname ); ?>"/>
						</label>
					</div>
				</div>
				<div class="hidden">
					<div class="col">
						<label>
							<input placeholder="Company Name" type="text" name="company" 
							value="<?php echo esc_attr( isset( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '' ); ?>"/>
						</label>
					</div>
				</div>
				<div class="hidden">
					<div class="col">
						<label>
							<input placeholder="Company Name" type="text" pattern="[\+]?([0-9]{1,4})?\s?([0-9]{7,12})?" name="phone"/> 
						</label>
					</div>
				</div>
				<!--Hidden-->
				<div class="row">
					<div class="col-4">
						<button  type="submit" class="btn" id="monft_register">Register</button>
					</div>
					<div class="col-8">
						<div class="loader">
						</div>
					</div>
				</div>
				</br>
				<div class="row">					
				<h6 class="mo_nft_switch_login_register" >Already have an account? Login here</h6>
				</div>
			</form>
		</div>
		</div>
		<div name='monft_deploy_loader' id='monft_deploy_loader' class='monft_deploy_loader'> 
				<div class='monft_deploy_loader_content'>
				<img src= <?php echo esc_url( MONFT_URL . 'classes/resources/images/loader.gif' ); ?> alt="buying_nfts"/>
				<br><br><h1 style="text-align: center;">Please wait . . .</h1>
				</div>	
			</div>	
		<script>
			jQuery("#monft_register").click(async function(){
				document.getElementById('monft_deploy_loader').style.display = 'block';
			})
			jQuery("#phone").intlTelInput();
		</script>
			<?php
		}

		/**
		 * Function to render login UI.
		 */
		public function verify_password_ui() {
			$this->mo_nft_wp_enqueue();
			global $mo_nft_util;
			?>
			<div class="mo-nft-content-card" style="background-color: #fafafa;">
					<form name="f" method="post" action="">
						<input type="hidden" name="option" value="mo_nft_verify_customer" />
						<?php wp_nonce_field( 'mo_nft_verify_customer', 'mo_nft_verify_customer_nonce' ); ?>	
						<div>
							<h5>Email Address</h5>
								<label style="width: 100%">
									<input type="text" autofocus autocomplete="off" placeholder="test@example.org" name="email"
									value="<?php echo esc_attr( $mo_nft_util->get_option( 'mo_nft_admin_email' ) ); ?>"/>
								</label>
								<h5>Password</h5>
								<div class="row">
									<div class="col">
										<label>
											<input placeholder="********" type="password" name="password"/>
										</label>
									</div>
								</div>	
							</div>
							<div class="row">
								<div class="col-4">
									<button  type="input" class="btn">Login</button>
								</div>
								<div class="col-8">
									<div class="loader">
									</div>
								</div>
							</div>
					</form>
					<br>
					<div class="row">
						<small><a href="#mo_nft_forgot_password_link">Click here if you forgot your password</a></small>
					</div><br>
					<div class="row">
						<h6 class="mo_nft_switch_to_register" >Do not have an account? Register Here </h6>
					</div>
					<form id="mo_nft_change_email_form" method="post" action="">
						<input type="hidden" name="option" value="mo_nft_change_email" />
						<?php wp_nonce_field( 'mo_nft_change_email', 'mo_nft_change_email_nonce' ); ?>
					</form>
		</div>
		<script>
			jQuery("a[href=\"#mo_nft_forgot_password_link\"]").click(function(){
				window.open('https://login.xecurify.com/moas/idp/resetpassword');
			});

			jQuery('.mo_nft_switch_login_register').click((e)=>{
				e.preventDefault();
				jQuery('.mo_nft_account_register').toggle();
				jQuery('.mo_nft_account_login').toggle();
			})
			jQuery('.mo_nft_switch_to_register').click((e)=>{
				e.preventDefault();
				jQuery('.mo_nft_account_login').toggle();
				jQuery('.mo_nft_account_register').toggle();	
			})
		</script>

			<?php
		}



		/**
		 * Function to show customer info.
		 */
		public function show_customer_info() {
			global $mo_nft_util;
			$this->mo_nft_wp_enqueue();

			?>
		<div class="mo-nft-content-card" >
		<h5>Email registered with miniOrange</h5>
		<div class="row">
					<div class="col">
						<div class="col">
						<?php echo wp_kses( $mo_nft_util->get_option( 'mo_nft_admin_email' ), \mo_nft_get_valid_html() ); ?>
						</div>
					</div>
		</div>
				<h5>Customer Key</h5>
				<div class="row">
					<div class="col">
					<?php echo wp_kses( $mo_nft_util->get_option( 'mo_nft_admin_customer_key' ), \mo_nft_get_valid_html() ); ?>
					</div>
		</div>

		<table>
		<tr>
		<td>
		<form name="f1" method="post" action="" id="mo_nft_goto_login_form">
			<input type="hidden" value="mo_nft_change_miniorange" name="option"/>
			<?php wp_nonce_field( 'mo_nft_change_miniorange', 'mo_nft_change_miniorange_nonce' ); ?>
			<button class="btn" >Change Account</button>
		</form>
		</td>
		</tr>
		</table>
		<br />
		</div>
			<?php
		}
	}
}

?>
