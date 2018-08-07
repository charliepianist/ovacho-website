<?php 
/*
* Template Name: Register Page **BACKEND**
*/
?>
<!DOCTYPE html>
<html data-wf-page="5aac99ecaf97838325230d58" data-wf-site="5a7fa1f338edac00018725fb">
<?php get_header(); ?>
<?php 
//get referrer id
$ref_id = '';
if(isset($_GET) && $_GET['ref']) $ref_id = reverse_referral_id($_GET['ref']);
?>

<div id="1_2" class="_1_2">
	<div class="_3_container w-container">
		<div data-w-id="5b0fafd8-0e5a-c647-b1cd-319a5d4edf61" class="_2_sign-up-page-wrapper"><img src="<?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO.png" srcset="<?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO-p-500.png 500w, <?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO-p-800.png 800w, <?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO-p-1080.png 1080w, <?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO.png 1193w" sizes="100vw" data-w-id="5b0fafd8-0e5a-c647-b1cd-319a5d4edf62" class="_1_logo-rotate-log-in">
      		<h1 class="_1_login-page-wrapper-heading" style="margin-bottom: 0.1em;">Create Account</h1>
      		<p class="o-error-msg" style="margin-bottom:0em;">
      			<?php
			$o_page = basename($_SERVER['REQUEST_URI']);
			$o_has_error = false;
			if (strpos($o_page, 'empty_username=true') !== false || strpos($o_page, 'empty_email=true') !== false || strpos($o_page, 'name_error=true') !== false || strpos($o_page, 'pwd_error=true') !== false) {
					echo 'Please fill out all fields.<br>';
					$o_has_error = true;
			}
			if(strpos($o_page, 'equal_pwds_error=true') !== false) {
					echo 'Error: Passwords do not match.<br>';
					$o_has_error = true;
			}
			if(strpos($o_page, 'username_exists=true') !== false) {
				echo 'Sorry, this username has already been taken.<br>';
					$o_has_error = true;
			}
			if(strpos($o_page, 'email_exists=true') !== false) {
				echo 'Sorry, there is already an account registered to this email.<br>';
					$o_has_error = true;
			}
			if(strpos($o_page, 'invalid_username=true') !== false) {
				echo 'Invalid username.<br>';
					$o_has_error = true;
			}
			if(strpos($o_page, 'invalid_email=true') !== false) {
				echo 'Invalid email address.<br>';
					$o_has_error = true;
			}
			?>
      		</p>
      		<!-- REFERRER AND REGISTER FORM -->
      		<?php if($ref_id) {
      				echo '<p style="color:black; margin-top:0em;">Referred By: ' . get_username($ref_id) . '</p>';
      			}else echo '<p></p>';

      			//REGISTER FORM
      			get_template_part('parts/login/register-form');
      		?>
  		</div>
	</div>
</div>

<?php get_footer(); ?>