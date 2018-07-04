<?php
/*
* Template Name: Reset Password Page **BACKEND**
*/
?>

<!DOCTYPE html>
<html data-wf-page="5aac99ecaf97838325230d58" data-wf-site="5a7fa1f338edac00018725fb">
<?php get_header(); ?>
<?php if(!is_user_logged_in()): ?>
<div id="1_2" class="_1_2">
	<div class="_3_container w-container">
		<div data-w-id="5b0fafd8-0e5a-c647-b1cd-319a5d4edf61" class="_2_sign-up-wrapper"><img src="<?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO.png" srcset="<?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO-p-500.png 500w, <?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO-p-800.png 800w, <?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO-p-1080.png 1080w, <?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO.png 1193w" sizes="100vw" data-w-id="5b0fafd8-0e5a-c647-b1cd-319a5d4edf62" class="_1_logo-rotate-log-in">
      		<h1 class="_1_login-page-wrapper-heading">Reset Password</h1>
      		<?php 
      			$o_page = basename($_SERVER['REQUEST_URI']);
      			//CHECK IF FORM IS FOR SETTING PASSWORD OR SENDING EMAIL
				if (strpos($o_page, 'form=newpass') !== false): ?>
					<p class="o-error-msg">
		      			<?php
					$o_page = basename($_SERVER['REQUEST_URI']);
					if(strpos($o_page, 'password_reset_mismatch') !== false) {
						echo 'Error: Passwords do not match.<br>';
					}
					if(strpos($o_page, 'password_reset_empty') !== false) {
						echo 'Please enter your new password.<br>';
					}
					?>
		      		</p>

					<div class="w-form">
						<form name="resetpassform" id="resetpassform" action="<?php echo site_url('wp-login.php?action=resetpass'); ?>" method="post" novalidate="novalidate"  autocomplete="off">
							<input type="hidden" name="rp_login" id="rp_login" value="<?php echo esc_attr($_REQUEST['login']);?>" autocomplete="off">
							<input type="hidden" name="rp_key" id="rp_key" value="<?php echo esc_attr( $_REQUEST['key'] ); ?>" />
							<input type="password" name="pass1" id="pass1" class="text-field w-input" size="20" value="" autocomplete="off" placeholder="New Password"/>
							<input type="password" name="pass2" id="pass2" class="text-field w-input" size="20" value="" autocomplete="off" placeholder="Confirm Password"/>
							<input type="submit" value="Reset Password" data-wait="Please wait..." class="_1_login-in-wrapper w-button">
							</form>
					        <div class="w-form-done">
					          <div>Your password has been reset.</div>
					        </div>
					        <div class="w-form-fail">
					          <div>Oops! Something went wrong while submitting the form.</div>
					        </div>
					      </div>
				<?php else: ?>
      		<p class="o-error-msg">
      			<?php
			$o_page = basename($_SERVER['REQUEST_URI']);
			if(strpos($o_page, 'invalid_email') !== false) {
				echo 'Please enter a valid email address.<br>';
			}
			if(strpos($o_page, 'empty_username') !== false) {
				echo 'Please enter your username or email.<br>';
			}
			if(strpos($o_page, 'invalidcombo') !== false) {
				echo 'Sorry, we don\'t have an account registered to that username/email.<br>';
			}
			?>
      		</p>
      		<div class="w-form">
			<form name="lostpasswordform" id="lostpasswordform" action="<?php echo site_url('wp-login.php?action=lostpassword'); ?>" method="post" novalidate="novalidate" data-name="Email Form">
				<input type="email" class="text-field w-input" maxlength="256" name="user_login" data-name="Email 3" placeholder="Username or Email Address" id="user_login" value="">
				<input type="submit" value="Reset Password" data-wait="Please wait..." class="_1_login-in-wrapper w-button">
				</form>
		        <div class="w-form-done">
		          <div>An email to reset your password has been sent.</div>
		        </div>
		        <div class="w-form-fail">
		          <div>Oops! Something went wrong while submitting the form.</div>
		        </div>
		      </div>
		      <?php endif; ?>
  		</div>
	</div>
</div>

<?php else: ?>
	<?php get_template_part('parts/login/logged-in-error'); ?>
<?php endif; ?>
<?php get_footer(); ?>