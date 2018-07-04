<div class="w-form">
  <?php $o_args = array(
                  'echo' => false,
                  'form_id' => 'email-form1',
      );
      $o_orig_form = wp_login_form($o_args);
      $o_begin_form_index = strpos($o_orig_form, '<');
      $o_form = substr($o_orig_form, $o_begin_form_index, strpos($o_orig_form, '>') - $o_begin_form_index) . ' data-name="Email Form">';
      echo $o_form;
      //should be <form name="loginform" id="loginform" action="http://SITEURLlocalhost/wp-login.php" method="post" data-name = "Email Form">

      /*$o_login_str = '<input type="text" name="';
      $o_login_input_index = strpos($o_orig_form, $o_login_str);
      $o_offset = strlen($o_login_str);
      $o_login_name = substr($o_orig_form, $o_login_input_index + $o_offset, strpos($o_orig_form, '"', $o_login_input_index + $o_offset + 1) - $o_login_input_index - $o_offset);

      $o_password_str = '<input type="password" name="';
      $o_password_input_index = strpos($o_orig_form, $o_password_str);
      $o_offset = strlen($o_password_str);
      $o_login_name = substr($o_orig_form, $o_password_input_index + $o_offset, strpos($o_orig_form, '"', $o_password_input_index + $o_offset + 1) - $o_password_input_index - $o_offset);*/
      ?>
<input type="text" class="text-field w-input" maxlength="256" name="log" data-name="log" placeholder="Username or Email Address" id="email-2">
<input type="password" class="text-field-2 w-input" maxlength="256" name="pwd" data-name="pwd" placeholder="Password" id="password" required="">
    <div class="w-row">
      <div class="w-col w-col-4">
        <div class="_1_login-text"><a href="<?php echo site_url('register'); ?>" class="o-login-form-link">Create Account</a><br><br></div>
      </div>
      <div class="w-col w-col-4"><input type="submit" name="wp-submit" id="wp-submit" value="Log In" data-wait="Please wait..." class="_1_login-in-wrapper w-button"></div>
      <div class="w-col w-col-4">
        <div class="_1_login-text"><a href="<?php echo home_url('reset-password');?>" class="o-login-form-link">I Forgot My Password</a></div>
      </div>
    </div>
    <input type="hidden" name="redirect_to" value="<?php if('GET' == $_SERVER['REQUEST_METHOD'] && isset($_REQUEST['redirect']) && !empty($_REQUEST['redirect'])) echo urldecode(esc_attr($_REQUEST['redirect'])); else echo home_url();?>" />
  </form>
  <div class="w-form-done">
    <div>Thank you! Your submission has been received!</div>
  </div>
  <div class="w-form-fail">
    <div>Oops! Something went wrong while submitting the form.</div>
  </div>
</div>

