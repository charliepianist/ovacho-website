<?php /*Template Name: Pricing **Backend** */ ?>

<!DOCTYPE html>
<html>
<?php get_header(); 
//referrals
if(isset($_GET) && $_GET['ref'] && is_user_logged_in()) {
  $ref_id = reverse_referral_id($_GET['ref']);
  //valid referrer and current user hasn't subscribed yet
  if($ref_id && !has_subscribed(get_current_user_id()) && $ref_id != get_current_user_id()) {
    //ref_id field used to determine who to give referral credit to
    update_user_meta(get_current_user_id(), 'ref_id', $ref_id);
  }
}

//calculate amount
$amount = get_forms_amount(get_current_user_id());


?>

<div class="_3_container w-container">
    <h1 class="heading">Subscription Pricing</h1>
    <div class="_3_wlr1 w-row">
      <div class="w-col w-col-4">
        <h1 class="_3_chartname">Basic</h1>
        <p class="pricing"><span class="text-span-7">Watchlist/Recap<br></span>• Daily 4 Stock Watchlist<br><br><span><strong class="bold-text">Charts/Analysis</strong></span><br>• 3 - 10 Technical Stock Charts/Week<br>• 2 - 3 Stock Analyses/Week<br>‍<br><span class="text-span-9">Discord</span><br>• Access To Basic Chatrooms<br>• Limited Access To Stock Bots<br>‍• 1 - 5 Free Intra-Day Alerts<br><br><span class="text-span-10">Resources</span><br>• Access To YouTube Videos<br>• Access To Articles &amp; Guides<br>• Access To Blog &amp; Newsletters<br>• Access To Analyst Team<br>‍<br><br><br><br>‍<span class="text-span-3"><br></span></p><a href="#" data-w-id="16be0b84-1d1b-74e4-3c3d-a909d7d29609" class="pricing_button w-button">FREE</a></div>
      <div class="w-col w-col-4">
        <h1 class="_3_chartname">Classic [<span class="text-limited-time text-span-16">Limited Time</span>]</h1>
        <p class="pricing"><span class="text-span-7">Watchlist/Recap<br></span>• Daily 16 Stock Watchlist<br>• Daily 16 Stock Recap<br>• Special Watchlists<br>‍&emsp;• Large-Cap<br>‍&emsp;• Options<br><br><span><strong class="bold-text">Charts/Analysis</strong></span><br>• 5 - 25+  Technical Stock Charts/Week<br>• 3 - 12+ Stock Analyses/Week<br>• Request Tickers For Analyses<br>‍<br><span class="text-span-9">Discord</span><br>• Full Access To Discord Chatrooms<br>• Intra-Day Alerts<br>• Full Access To Stock Bots<br>‍<br><span class="text-span-10">Resources</span><br>• Access To YouTube Videos<br>• Access To Articles &amp; Guides<br>• Access To Blog &amp; Newsletters<br>• Priority Access To Analyst Team <br>
        </p><a href="#" data-w-id="c5199c12-e32e-d2be-a248-cf82a26d0f7a" class="pricing_button w-button" onclick="classicClick();" id="classic_button"><?php if((get_user_meta(get_current_user_id(), 'discount', true) == 'true' && firstTwoMonths()) || referred_first_time(get_current_user_id())) echo '$15 for first month, then ';?>$20/month</a>
        
        <?php 
        if(is_user_logged_in()) {
          //check if user has no subscriptions
        	if(get_user_subscription(get_current_user_id()) === 'basic') {
            //form with needed values
        		get_template_part('parts/payment/classic-form');
            echo '<p class="regular-text" style="text-align: center; margin-top:0.25em; line-height:20px;"><a class="white-link" href="' . site_url('account') . '">Referral Credit</a>: $' . get_referral_credit(get_current_user_id()) . '<br>(Automatically Applied)</p>';
        	}else echo '<p class="pricing to_fade_in" style="display:none;">You are already subscribed to our Classic Subscription.</p>';
        }else echo '<p class="pricing to_fade_in" style="display:none;">You must <a style="color: #fff;" href="' . site_url('login/?redirect=') . urlencode(site_url() . $_SERVER['REQUEST_URI']) . '">login</a> to purchase this subscription.</p>';
        ?>
      </div>
      <!--<div class="w-col w-col-4">
        <h1 class="_3_chartname">Premium [Coming Soon!]</h1>
        <p class="pricing_plat" style="padding-bottom:1.3em;"><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br></p><a href="#" data-w-id="e7b1faab-8b5a-267d-3e32-46f78309180a" class="pricing_button w-button">TBD</a></div>-->
    </div>
  </div>
<script src="<?php bloginfo('stylesheet_directory');?>/js/md5.min.js"></script>
<script>
  function renewSubscriptionButton() {
    $('#renew_form').submit();
  }
	function classicClick() {
		$('#classic_button').fadeOut(400, function() {
			$('.to_fade_in').fadeIn(600);
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
        }else submitClassicForm();
      },
      "error": function(obj, str) {
        alert('An error occurred validating your Discord ID with the following message: ' + str);
        $('#discord_id_validating').hide();
      }
    });
    $('#discord_id_error').hide();
    $('#discord_id_validating').show();
	}
	function discordHelpLinkClick() {
		$('#discord_help_link').fadeOut(400, function() {
			$('#discord_help').fadeIn(600);
		});
	}
  function noDiscordClick() {
    $('#no_discord_link').fadeOut(400, function() {
      $('#no_discord_submit').fadeIn(600);
    });
  }
  function payNowWithoutDiscordClick() {
    $('#discord_id').val('0');
    submitClassicForm();
  }
  function submitClassicForm() {
    var date = new Date();
      var seconds = date.getTime() / 1000 - ((date.getTime() / 1000) % 1);
      var hash = md5("<?php echo PAYEEZY_LOGIN; ?>^<?php echo FP_SEQUENCE; ?>^" + seconds + "^<?php echo $amount?>^USD", "<?php echo PAYEEZY_TRANSACTION_KEY; ?>");
      $('#x_fp_timestamp').val(seconds);
      $('#x_fp_hash').val(hash);
      $('#submit_form').click();
  }
</script>
<?php get_footer(); ?>