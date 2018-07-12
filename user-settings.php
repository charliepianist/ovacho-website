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
      if($sub_type !== 'Basic') {
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
        if(is_subscription_active(get_current_user_id())) echo 'Automatic Renewal: Active';
        else echo 'Automatic Renewal: <span style="color:red;">Inactive</span>';
      }
        ?></span></p>
        <?php $user_id = get_current_user_id(); 
        if(get_user_subscription($user_id) !== 'basic'): //check if user has subscription
          if(is_subscription_active($user_id)): //ACTIVE?>
          <a href="#" id="initial_cancel_button" data-w-id="6413a6ba-b27b-e4d2-f56c-bd4746d05e73" class="change_myaccount w-button" onclick="cancel_subscription_click();">Cancel Subscription</a>
          <form id="confirm_cancel" method="post" action="<?php echo site_url('account'); ?>" style="display:none; padding-top:0.2em; ">
            <p class="paragraph_privacy1"><span class="text_myaccount"> Enter your password to confirm: </span></p>
            <input type="password" class="text-field-2 w-input" maxlength="256" name="cancel_sub_password" data-name="Current Password" placeholder="Current Password" id="Current-Password" required="">
            <input type="hidden" name="cancel_sub" value="true">

            <input type="submit" value="Cancel Subscription"class="change_myaccount w-button">
          </form><br><br>
          <?php else: //USER CANCELED SUBSCRIPTION BUT CURRENT PERIOD STILL ACTIVE
            if(stored_payment_method(get_current_user_id())): //CHECK IF USER HAS PAYMENT METHOD?>
              <form method="post" action="<?php echo site_url('account'); ?>">
                <input type="hidden" name="renew_sub" value="true">
                <input type="submit" value="Renew Subscription (<?php echo nice_stored_payment_method(get_current_user_id()); ?>)" class="change_myaccount w-button">
              </form><br><br>
            <?php endif; ?>
          <?php endif; ?>
        <?php endif; ?>
    <p class="paragraph_privacy1"><span class="text_myaccount"><br>Change Password<br></span></p>
    <div class="w-form">
      <form id="reset-pass" name="reset-pass" class="form-2" action="<?php echo site_url('account'); ?>" method="post">
        <input type="password" class="text-field-2 w-input" maxlength="256" name="current_password" data-name="Current Password" placeholder="Current Password" id="Current-Password" required="">
        <input type="password" class="text-field-2 w-input" maxlength="256" name="new_password" data-name="New Password" placeholder="New Password" id="New-Password" required="">
        <input type="password" class="text-field-2 w-input" maxlength="256" name="confirm_password" data-name="Confirm Password 4" placeholder="Confirm Password" id="Confirm-Password-4" required="">
        <input class="change_myaccount w-button" type="submit" value="Change Password">
      </form>
      </div>
    <?php else: 
      get_template_part('parts/login/not-logged-in-error'); 
    endif; ?>
  <script>
    function cancel_subscription_click() {
      $('#initial_cancel_button').fadeOut(400, function() {
      $('#confirm_cancel').fadeIn(600);
    });
    }
  </script>
  <?php get_footer(); ?>