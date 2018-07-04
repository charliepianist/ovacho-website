<?php 
/*
* Template Name: Login Page **BACKEND**
*/
?>
<!DOCTYPE html>
<html data-wf-page="5aac99ecaf97838325230d58" data-wf-site="5a7fa1f338edac00018725fb">
<?php get_header(); 
if(!is_user_logged_in()): ?>

  <div id="1_2" class="_1_2">
	<div class="_3_container w-container">
	    <div class="_1_login-page-wrapper">
	    	<img src="<?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO.png" srcset="<?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO-p-500.png 500w, <?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO-p-800.png 800w, <?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO-p-1080.png 1080w, <?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO.png 1193w" sizes="100vw" data-w-id="1b765782-d06a-2516-6677-4c27a4ed8756" class="_1_logo-rotate-log-in-2">
	      <h1 class="_1_login-page-wrapper-heading">User Login</h1>
	      <?php
			$o_page = basename($_SERVER['REQUEST_URI']);

			if (strpos($o_page, 'checkemail=registered') !== false) {
			echo '<p class="o-password-reset-email-msg">Account created successfully.</p>';
			}
			if (strpos($o_page, 'checkemail=confirm') !== false) {
			echo '<p class="o-password-reset-email-msg">A password reset email has been sent.</p>';
			}
			if (strpos($o_page, 'password=changed') !== false) {
			echo '<p class="o-password-reset-email-msg">Your password has successfully been updated.</p>';
			}
			if (strpos($o_page, 'failed') !== false) {
			echo '<p class="o-error-msg">Invalid username and/or password.</p>';
			}
			elseif (strpos($o_page, 'blank') !== false ) {
			echo '<p class="o-error-msg">Please input a username and password.</p>';
			}
			if (strpos($o_page, 'login=expiredkey') !== false || strpos($o_page, 'login=invalidkey') !== false) {
			echo '<p class="o-error-msg">Invalid password reset link.</p>';
			}


			?>
	        <?php get_template_part('parts/login/login-form'); ?>
	    </div>
	</div>
</div>
<?php else: ?>
	<?php get_template_part('parts/login/logged-in-error'); ?>
<?php endif; ?>
<?php get_footer(); ?>