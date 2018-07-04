<div class="w-form">
	<form name="email-form2" id="email-form2" action="<?php echo site_url('wp-login.php?action=register'); ?>" method="post" novalidate="novalidate" data-name="Email Form" autocomplete="off">
		<input type="text" class="text-field w-input" maxlength="256" name="full_name" data-name="Full Name" placeholder="Full Name" id="full_name">
		<input type="text" class="text-field w-input" maxlength="256" name="user_login" data-name="Username" placeholder="Username" id="user_login">
		<input type="email" class="text-field w-input" maxlength="256" name="user_email" data-name="Email 3" placeholder="Email Address" id="user_email" value="">
		<input type="password" class="text-field-2 w-input" maxlength="256" name="pwd" data-name="Password" placeholder="Password" id="pwd" required="">
		<input type="password" class="text-field-2 w-input" maxlength="256" name="confirm_pwd" data-name="Confirm Password" placeholder="Confirm Password" id="confirm_pwd" required="">
		<div class="_1_login-text" style="margin-bottom:0.5em">By clicking "Sign Up", you agree to our<br><a href="<?php echo site_url('terms');?>">Terms of Service</a> and <a href="<?php echo site_url('privacy-policy');?>">Privacy Policy</a><br></div>.
		<input type="submit" value="Sign Up" data-wait="Please wait..." class="_1_login-in-wrapper w-button">
		</form>
        <div class="w-form-done">
          <div>Thank you! An e-mail has been sent for confirmation.</div>
        </div>
        <div class="w-form-fail">
          <div>Oops! Something went wrong while submitting the form.</div>
        </div>
      </div>