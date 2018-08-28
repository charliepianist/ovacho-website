<?php /*Template Name: User Settings **Backend** */ ?>
<!DOCTYPE html>
<html data-wf-page="5b12d54611a9de64fa351e38" data-wf-site="5a7fa1f338edac00018725fb">
<?php //check if password reset was requested
  $o_error = '';
  if(is_user_logged_in() && isset($_POST))
    //make sure proper fields are filled out
    if((isset($_POST['current_password']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) || isset($_POST['cancel_sub_password']) || isset($_POST['renew_sub'])) {

      //check which button was pressed
      if($_POST['cancel_sub'] === 'true') {
        //check that current password is correct
        if(!wp_check_password($_POST['cancel_sub_password'], wp_get_current_user()->data->user_pass)) {
            $o_error = '<p class="o-error-msg">Error: Current password does not match.</p>';
          }else {
            //discontinue subscription
            update_user_meta(get_current_user_id(), 'subscription_active', 'false');
            $o_error = '<p class="o-error-msg" style="margin-bottom:0em;"><span class="text_myaccount">Subscription canceled.</span></p>';
          }
      }else if($_POST['renew_sub'] === 'true') {
        //renew subscription
        update_user_meta(get_current_user_id(), 'subscription_active', 'true');
        $o_error = '<p class="o-error-msg" style="margin-bottom:0em;"><span class="text_myaccount">Subscription renewed.</span></p>';
      }else {
        //check that passwords match
        if($_POST['new_password'] !== $_POST['confirm_password']) {
          $o_error = '<p class="o-error-msg">Error: New passwords must match.</p>';
        }else {
          //check that current password is correct
          if(!wp_check_password($_POST['current_password'], wp_get_current_user()->data->user_pass)) {
            $o_error = '<p class="o-error-msg">Error: Old password does not match.</p>';
          }else {
            //change password, then redirect to login
            wp_set_password($_POST['new_password'], get_current_user_id());
            wp_redirect(site_url('login/?password=changed&redirect=') . urlencode(site_url('account')));
          }
        }
      }
  }
?>
<?php get_header(); ?>
<?php if(is_user_logged_in()): ?>
  <div class="_3_container w-container">
    <h1 class="heading">My Account</h1>
    <?php echo $o_error; ?>
    <p class="paragraph_privacy1"><span class="text_myaccount">Email Address: <?php echo wp_get_current_user()->data->user_email;?><br>
      Subscription Plan: <?php $sub_type = get_user_subscription(get_current_user_id()); echo ucwords($sub_type); 
      if($sub_type !== 'basic') {
        $date = intval(get_user_meta(get_current_user_id(), 'subscription_end_time', true));
        $time_left = $date - time();
        $hours_left = ceil($time_left / 3600);
        $days_left = (int) ($hours_left / 24);
        $hours_left = $hours_left % 24;
        echo '<br>Current Period Ends: ';
        if($days_left === 0) {
          if($hours_left === 1) {
            echo '<1 hour';
          }else echo $hours_left . ' hours';
        }else {
          if($hours_left === 1) {
            echo $days_left . ' days, 1 hour';
          }else echo $days_left . ' days, ' . $hours_left . ' hours';
        }

        //whether subscription is active
        echo '<br>';
        if(is_subscription_active(get_current_user_id())) echo 'Automatic Renewal: Active - ' . nice_stored_payment_method(get_current_user_id());
        else echo 'Automatic Renewal: <span style="color:red;">Inactive</span>';
      }
        ?></span></p>
        <?php $user_id = get_current_user_id(); 
        if(get_user_subscription($user_id) !== 'basic'): //check if user has subscription
          if(is_subscription_active($user_id)): //ACTIVE?>
          <!--Confirm Password Cancel Card Form-->
          <?php if(stored_payment_method(get_current_user_id()) === 'card'):?>
            <a href="#" id="initial_cancel_button" data-w-id="6413a6ba-b27b-e4d2-f56c-bd4746d05e73" class="change_myaccount w-button" onclick="cancel_subscription_click();">Cancel Subscription</a>
            <form id="confirm_cancel" method="post" action="<?php echo site_url('account'); ?>" style="display:none; padding-top:0.2em; ">
              <p class="paragraph_privacy1"><span class="text_myaccount"> Enter your password to confirm: </span></p>
              <input type="password" class="text-field-2 w-input" maxlength="256" name="cancel_sub_password" data-name="Current Password" placeholder="Current Password" id="cancel_sub_password" required="">
              <input type="hidden" name="cancel_sub" value="true">
              <input type="submit" value="Cancel Subscription"class="change_myaccount w-button">
          </form><br><br>
          <?php endif; ?>
          <!--End Confirm Pass Cancel Card Form-->
          <!--Cancel PayPal Button-->
          <?php if(stored_payment_method(get_current_user_id()) === 'paypal'): ?>
            <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias=82GTXR7CG35M8" id="paypal_cancel_button" data-w-id="6413a6ba-b27b-e4d2-f56c-bd4746d05e73" class="change_myaccount w-button">Cancel Subscription</a><br><br>
          <?php endif; ?>
          <!--End Cancel PayPal Button-->

          <?php else: //USER CANCELED SUBSCRIPTION BUT CURRENT PERIOD STILL ACTIVE
            if(stored_payment_method(get_current_user_id()) === 'card'): //CHECK IF USER HAS CARD PAYMENT METHOD?>
              <form method="post" action="<?php echo site_url('account'); ?>">
                <input type="hidden" name="renew_sub" value="true">
                <input type="submit" value="Renew Subscription (<?php echo nice_stored_payment_method(get_current_user_id()); ?>)" class="change_myaccount w-button">
              </form><br><br>
            <?php endif; ?>
          <?php endif; ?>
        <?php endif; ?>
    <!-- REFERRAL LINK -->
    <p class="paragraph_privacy1 settings-header"><br>Referrals<br></p>
    <p class="paragraph_privacy1"><span class="text_myaccount">
      <span style="font-size:16px; line-height: 20px;" class="bold-text">Give someone a <span class="bold-text" style="text-decoration: underline;">25% discount</span> on their first month. When they subscribe, get $5 in credit to be automatically applied! (Credit/Debit)</span><br>
      Referral Credit: $<?php echo get_referral_credit(get_current_user_id())?><br>
      Referred Users: <?php echo get_paid_referred_users_count(get_current_user_id());?><br>
      <!-- Registration Link -->
      Registration Link: <input id="registration_link_input" class="mini-text-field" readonly value="<?php $registration_link = site_url('register/?ref=' . get_referral_id(get_current_user_id())); echo $registration_link; ?>"></span></p>
      <div id="registration_link_text" style="display:none; height:auto; width:auto; white-space: nowrap; padding: 2px 8px; font-size: 14px; border-radius: 50px; line-height: 1.4;"><?php echo $registration_link; ?></div>
      <!-- Purchase Link -->
      <p class="paragraph_privacy1"><span class="text_myaccount">
      Purchase Link: <input id="purchase_link_input" class="mini-text-field" readonly value="<?php $purchase_link = site_url('pricing/?ref=' . get_referral_id(get_current_user_id())); echo $purchase_link; ?>"></span></p>
      <div id="purchase_link_text" style="display:none; height:auto; width:auto; white-space: nowrap; padding: 2px 8px; font-size: 14px; border-radius: 50px; line-height: 1.4;"><?php echo $purchase_link; ?></div>
    
    <!-- CHANGE PASSWORD -->
    <p class="paragraph_privacy1 settings-header"><br>Change Password<br></p>
    <div class="w-form">
      <form id="reset-pass" name="reset-pass" class="form-2" action="<?php echo site_url('account'); ?>" method="post">
        <input type="password" class="text-field-2 w-input" maxlength="256" name="current_password" data-name="Current Password" placeholder="Current Password" id="Current-Password" required="">
        <input type="password" class="text-field-2 w-input" maxlength="256" name="new_password" data-name="New Password" placeholder="New Password" id="New-Password" required="">
        <input type="password" class="text-field-2 w-input" maxlength="256" name="confirm_password" data-name="Confirm Password 4" placeholder="Confirm Password" id="Confirm-Password-4" required="">
        <input class="change_myaccount w-button" type="submit" value="Change Password">
      </form>
      </div>
    </div>
    <?php else: 
      get_template_part('parts/login/not-logged-in-error'); 
    endif; ?>
  <script>
    //set widths for inputs to wrap text
    var registration_link_width = $('#registration_link_text').innerWidth();
    var purchase_link_width = $('#purchase_link_text').innerWidth();
    $('#registration_link_input').width(registration_link_width);
    $('#purchase_link_input').width(purchase_link_width);

    function cancel_subscription_click() {
      $('#initial_cancel_button').fadeOut(400, function() {
        $('#confirm_cancel').fadeIn(600);
      });
    }
  </script>
  <?php get_footer(); ?>