<?php /*Template Name: Subscribe Ref **Backend** */ ?>
<?php get_header();
$amount = get_forms_amount(get_current_user_id());

//error codes
$error_code = '';

if(isset($_POST['timestamp'])) {
	//user sent request to subscribe

	//timestamp after current time
	if($_POST['timestamp'] > time()) $error_code = 'Invalid Timestamp';

	//product code invalid (should never happen)
	if($_GET['prod'] !== CLASSIC_SUBSCRIPTION_ID) $error_code = 'Invalid request (Error code 104)';
}else {
	//user clicked link to form
	//problem with request
	if(!isset($_GET)) $error_code = 'Invalid request (Error code 100)'; 
	else {
		if(!isset($_GET['prod'])) $error_code = 'Invalid request (Error code 101)'; 
		if(!isset($_GET['timestamp'])) $error_code = 'Invalid request (Error code 102)';
		if($_GET['prod'] !== CLASSIC_SUBSCRIPTION_ID) $error_code = 'Invalid request (Error code 103)';  
	}
}

//not enough referral credit
if($amount !== '0.00') $error_code = 'Insufficient referral credit to fully cover purchase (You have $' . get_referral_credit(get_current_user_id()) . ')';

//not logged in or already subscribed
if(!is_user_logged_in()) $error_code = 'You are not logged in';
else if(get_user_subscription(get_current_user_id()) !== 'basic') $error_code = 'You are already subscribed';
?>

<!--BEGIN HTML-->
<div class="_3_container w-container">
	<h1 class="heading" <?php if(!$error_code && !isset($_POST['timestamp'])) echo 'style="text-align:center;"'?>>Subscribe using Referral Credit</h1>

<?php if($error_code): //ERROR IN REQUEST?>
	<p class="paragraph_privacy1">An error occurred: <?php echo $error_code; ?>.</p>

<?php else: //VALID REQUEST
if(isset($_POST['timestamp'])): //USER PRESSED BUTTON ALREADY

//add subscriber
$cust_id = get_current_user_id();
$prev_referral_credit = get_referral_credit($cust_id);
$new_referral_credit = number_format($prev_referral_credit - get_next_amount($cust_id), 2); //amount of referral credit after this
add_referral_credit($cust_id, -1 * get_next_amount($cust_id));
update_user_meta($cust_id, 'discord_id', $_POST['discord_id']);
update_user_meta($cust_id, 'subscription_type', 'classic');
update_user_meta($cust_id, 'subscription_end_time', strtotime('+1 month'));
update_user_meta($cust_id, 'has_subscribed', 'true');
add_discord_user($_POST['discord_id']);

//add transaction
add_transaction($cust_id, 'Paid fully by referral credit ($' . $prev_referral_credit . ' to $' . $new_referral_credit . '), timestamp ' . time());
//email
$links_html = '<p style="color:#898989; text-align:center;"><a style="color:#898989;" href="' . site_url('privacy-policy') . '">PRIVACY POLICY</a> | <a style="color:#898989;" href="' . site_url('terms') . '">TERMS</a> | <a style="color:#898989;" href="' . site_url('account') . '">UNSUBSCRIBE</a></p>';
send_email(get_user_email($cust_id), 'Thank you for joining our subscription!', '<p>Thank you for joining us! You have been subscribed to our Classic Subscription using referral credit.<br><br>Your previous referral credit: <strong>$' . $prev_referral_credit . '</strong><br>Your new referral credit: <strong>$' . $new_referral_credit . '</strong></p><br>' . $links_html);

echo '<p class="paragraph_privacy1">Thank you for joining us! You have successfully been subscribed to our Classic Subscription. Good luck trading and we hope you find our resources helpful!<br><br><a href="' . site_url() . '" style="color: #fff;">Back to Site Home</a></p>';

else: //USER HAS NOT PRESSED BUTTON
?>
	
	<div class="w-col w-col-14"></div>
	<div class="w-col w-col-13">
		<p class="paragraph_privacy1" style="padding-left:10px; padding-bottom:0.5em; text-align:center;">Subscribe using only referral credit! (you currently have $<?php echo number_format(get_referral_credit(get_current_user_id()), 2); ?>) <br>(if you have a credit/debit card, use our <a class="white-link" href="<?php echo site_url('pricing');?>">pricing page</a> instead)</p>
		<form action="<?php echo site_url('subscribe-ref/?prod=' . $_GET['prod']);?>" method="post" style="margin-bottom: 0px;">
			<input type="number" class="text-field w-input" maxlength="30" value="<?php echo get_user_meta(get_current_user_id(), 'discord_id', true);?>" name="discord_id" placeholder="Discord ID (Ex: 239132435706761325)" id="discord_id" required style="margin-bottom: 0.5em; -webkit-appearance: none;">
			<input type="hidden" name="timestamp" id="timestamp" value="">
			<input type="submit" style="display:none;" id="submit_form">
			<p id="discord_help_link" style="text-align:center; margin-bottom: 0.5em; color: #f3f3f3; line-height: 15px; font-size: 13px;"><a href="#" class="white-link" onclick="discordHelpLinkClick();">How do I find my Discord ID?</a></p>
			<p id="discord_help" style="display: none; text-align:center; margin-bottom: 0.5em; color: #f3f3f3; line-height: 15px; font-size: 13px;">You can find your Discord ID by sending the message !id anywhere in our discord.</p>
			<p style="text-align:center; margin-bottom: 0.5em; color: #f3f3f3; line-height: 15px; font-size: 13px;">By clicking "Subscribe Now", you agree to our <a class="white-link" href="<?php echo site_url('terms'); ?>">Terms</a>.</p>
			<p id="discord_id_validating" style="display: none; text-align:center; margin-bottom: 0.5em; line-height: 15px; font-size: 13px; color:red;">Validating Discord ID...</p>
			<p id="discord_id_error" style="display: none; text-align:center; margin-bottom: 0.5em; color: red; line-height: 15px; font-size: 13px;">Invalid Discord ID.</p>
		</form>
		<button data-w-id="c5199c12-e32e-d2be-a248-cf82a26d0f7a" class="pricing_button w-button" id="submit_classic_form" style="margin-top:0px; width:100%;" onclick="subscribeNowClick();">Subscribe Now</button>
		<p style="text-align:center; margin-bottom: 0.5em; margin-top:0.5em; color: #f3f3f3; line-height: 15px; font-size: 13px;"><a id="no_discord_link" href="#" class="white-link" onclick="noDiscordClick();">Don't use Discord?</a>
		<button id="no_discord_submit" data-w-id="c5199c12-e32e-d2be-a248-cf82a26d0f7a" class="pricing_button w-button" style="display:none; margin-top:0.5em; width:100%;" onclick="subscribeWithoutDiscordClick();">Subscribe Now (without Discord)</button>
	</div>
	<div class="w-col w-col-14"></div>
<?php endif; endif; ?>
</div>
<!--Override footer type-->
<input style="display:none;" id="footer_class" value="_1_6">
<script>
function discordHelpLinkClick() {
	$('#discord_help_link').fadeOut(400, function() {
		$('#discord_help').fadeIn(600);
	});
}
function payNowClick() {
    $.get({
      "url": "<?php echo site_url('proxy/?auth=' . PROXY_AUTH . '&action=get_discord_user&discord_id=')?>" + $('#discord_id').val(),
      "dataType": "json",
      "success": function(data) {
        if(data.username === undefined && data.code !== undefined) {
          $('#discord_id_validating').hide();
          $('#discord_id_error').fadeIn(600);
        }else submitForm();
      },
      "error": function(obj, str) {
        alert('An error occurred validating your Discord ID with the following message: ' + str);
        $('#discord_id_validating').hide();
      }
    });
    $('#discord_id_error').hide();
    $('#discord_id_validating').show();
}
  function noDiscordClick() {
    $('#no_discord_link').fadeOut(400, function() {
      $('#no_discord_submit').fadeIn(600);
    });
  }
  function subscribeWithoutDiscordClick() {
    $('#discord_id').val('0');
    submitForm();
  }
function subscribeNowClick() {
    if($('#discord_id').val().length < 5) {
      $('#discord_id_error').fadeIn(600);
    }else {
      submitForm();
    }
}
  function submitForm() {
    var date = new Date();
    var seconds = date.getTime() / 1000 - ((date.getTime() / 1000) % 1);
  	$('#timestamp').val(seconds);
  	$('#submit_form').click();
  }
</script>
<?php get_footer(); ?>