<div class="to_fade_in" style="display: none;">
	<?php 
	//amount to charge
	$amount = get_forms_amount(get_current_user_id());
	//product code
	$product_code = '';
	switch($amount) {
		case '15.00':
		$product_code = '099100';
		break;
		case '10.00':
		$product_code = '099104';
		break;
		case '5.00':
		$product_code = '099103';
		break;
		default:
		$product_code = '099102';
	}
//renew option
    /*if(stored_payment_method(get_current_user_id())) {
        echo '<a onclick="renewSubscriptionButton();" class="w-button pricing_button">Use Stored Payment Method (' . nice_stored_payment_method(get_current_user_id()) . ')</a><p style="text-align:center; margin-bottom: 0.5em; color: #f3f3f3; line-height: 15px; font-size: 13px; margin-top:0.5em;"><strong>Or use a new payment method:</strong></p>';
        echo '<form method="post" action="' . site_url('account') . '" style="display:none;" id="renew_form">
                <input type="hidden" name="renew_sub" value="true">
              </form>';
    }*/
	?>
	<br>
	<!--Credit/Debit Form-->
	<form action="https://checkout.globalgatewaye4.firstdata.com/pay" id="pay_now_form_9887b9a25c" method="post" style="margin-bottom: 0px;">
		<input type="number" class="text-field w-input" maxlength="30" value="<?php echo get_user_meta(get_current_user_id(), 'discord_id', true);?>" name="x_reference_3" placeholder="Discord ID (Ex: 239132435706761325)" id="discord_id" required style="margin-bottom: 0.5em; -webkit-appearance: none;">
		<p id="discord_help_link" style="text-align:center; margin-bottom: 0.5em; color: #f3f3f3; line-height: 15px; font-size: 13px;"><a href="#" class="white-link" onclick="discordHelpLinkClick();">How do I find my Discord ID?</a></p>
		<p id="discord_help" style="display: none; text-align:center; margin-bottom: 0.5em; color: #f3f3f3; line-height: 15px; font-size: 13px;">You can find your Discord ID by sending the message !id anywhere in our discord.</p>
		<p style="text-align:center; margin-bottom: 0.5em; color: #f3f3f3; line-height: 15px; font-size: 13px;">By purchasing our subscription, you agree to our <a class="white-link" href="<?php echo site_url('terms'); ?>">Terms</a>.</p>
		<p id="discord_id_validating" style="display: none; text-align:center; margin-bottom: 0.5em; line-height: 15px; font-size: 13px; color:red;">Validating Discord ID...</p>
		<p id="discord_id_error" style="display: none; text-align:center; margin-bottom: 0.5em; color: red; line-height: 15px; font-size: 13px;">Invalid Discord ID.</p>

		<input type="hidden" name="x_login" value="<?php echo PAYEEZY_LOGIN; ?>">
		<input type="hidden" name="x_fp_sequence" value="<?php echo FP_SEQUENCE; ?>">
		<input type="hidden" name="x_fp_timestamp" id="x_fp_timestamp" value="">
		<input type="hidden" name="x_amount" value="<?php echo $amount; ?>">
		<input type="hidden" name="x_fp_hash" id="x_fp_hash" value="">
		<input type="hidden" name="x_show_form" value="PAYMENT_FORM">
		<input type="hidden" name="x_type" value="<?php if($amount === '0.00') echo 'AUTH_ONLY'; else echo 'AUTH_CAPTURE'; ?>">
		<input type="hidden" name="x_currency_code" value="USD">
		<!--<input type="hidden" name="x_recurring_billing" value="TRUE">
		<input type="hidden" name="x_recurring_billing_id" value="<?php echo PAYEEZY_CLASSIC_SUBSCRIPTION_ID; ?>">
		<input type="hidden" name="x_recurring_billing_amount" value="15.00">
		<input type="hidden" name="x_recurring_billing_start_date" value="<?php echo date('Y-m-d');?>">-->
		<input type="hidden" name="x_cust_id" value="<?php echo get_current_user_id(); ?>">
		<input type="hidden" name="x_user1" value="<?php echo get_current_user_id(); ?>">
		<!--<input type="hidden" name="x_po_num" value="<?php echo uniqid('', TRUE); ?>">-->
		<input type="hidden" name="x_invoice_num" value="<?php $order_id = uniqid('', TRUE); echo $order_id; ?>">
		<input type="hidden" name="x_po_num" value="<?php echo $order_id; ?>">
		<input type="hidden" name="x_description" value="Classic Subscription">
		<?php function discounted() {
			return firstWeek() || (get_user_meta(get_current_user_id(), 'discount', true) === 'true' && firstTwoMonths()) || referred_first_time(get_current_user_id());
		}?>
		<input type="hidden" name="x_line_item" value="<|><|><?php if($amount < DEFAULT_SUBSCRIPTION_AMOUNT) echo 'Discounted '; ?>Classic Subscription (1 Month)<|>1<|><?php echo $amount; ?><|><|><?php echo $product_code;?><|>91528<|>Each<|><|><|>0.00<|><|>0.00<|><?php echo $amount; ?>">

		<!-- LEVEL 3 PROCESSING FIELDS -->
		<input type="hidden" name="x_tax" value="0.00">
		<input type="hidden" name="x_freight" value="0.00">
		<input type="hidden" name="x_duty" value="0.00">
		<input type="hidden" name="discount_amount" value="0.00">
		<!-- END LEVEL 3 PROCESSING FIELDS -->

		<!-- SHIPPING ADDRESS FOR PAYPAL -->
		<input type="hidden" name="x_ship_to_address" value="19 Birch Drive">
		<input type="hidden" name="x_ship_to_city" value="Plainsboro">
		<input type="hidden" name="x_ship_to_state" value="NJ">
		<input type="hidden" name="x_ship_to_zip" value="08536">
		<input type="hidden" name="x_ship_to_country" value="United States">
		<!-- END SHIPPING ADDRESS FOR PAYPAL -->

		<input type="hidden" name="x_ga_tracking_id" value="<?php echo PAYEEZY_GA_TRACKING_ID; ?>">

		<input type="hidden" name="button_code" value="Pay Now Ovacho Investments">

		<input type="submit" style="display:none;" id="submit_form">
	</form>
	<!--End Credit/Debit Form-->
	<!--PayPal Form-->
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
    <input type="hidden" name="cmd" value="_s-xclick">
	<input type="hidden" name="hosted_button_id" value="US3DGHT88U8MN">
    <input type="hidden" id="custom_field" name="custom" value="">
    <input type="submit" style="display:none;" id="submit_paypal_form">
    </form>
    <!--End PayPal Form-->
    <!--Pay Now Button-->
	<button data-w-id="c5199c12-e32e-d2be-a248-cf82a26d0f7a" class="pricing_button w-button" id="pay_now_button" style="margin-top:0px; width:100%;" onclick="payNowClick();">Pay Now</button>
	<!--Buttons Using Discord ID-->
	<div id="card_paypal_div" style="display: none;">
		<button data-w-id="c5199c12-e32e-d2be-a248-cf82a26d0f7a" class="pricing_button w-button" id="submit_classic_form" style="float:left; margin-top:0px; width:49.5%; margin-left:0px; margin-right:0px; display:inline-block; margin-bottom:0.45em;" onclick="creditDebitClick();">Credit/Debit</button>
		<button class="pricing_button w-button" id="submit_classic_form_paypal" style="float:right; margin-top:0px; width:49.5%; display:inline-block; margin-right:0px; margin-left:0px; margin-bottom:0.45em;" onclick="paypalClick();">PayPal</button>
	</div>
	<!--Text/Buttons without using Discord ID-->
	<p style="text-align:center; margin-bottom: 0.5em; margin-top:0.5em; color: #f3f3f3; line-height: 15px; font-size: 13px; display:inline-block; <?php if($amount !== '0.00'): ?>width:100%;<?php else: ?>margin-left:9.5%; <?php endif; ?>"><a id="no_discord_link" href="#" class="white-link" onclick="noDiscordClick();">Don't use Discord?</a></p>
	<div id="no_discord_submit" style="display:none;">
		<div style="text-align:center; color:#fff"><span>No Discord:</span></div>
		<button data-w-id="c5199c12-e32e-d2be-a248-cf82a26d0f7a" class="pricing_button w-button" style="margin-top:0.5em; margin-bottom:0.5em; width:49.5%; float:left; display:inline-block;" onclick="payNowWithoutDiscordClick();">Credit/Debit</button>
		<button data-w-id="c5199c12-e32e-d2be-a248-cf82a26d0f7a" class="pricing_button w-button" style="margin-top:0.5em; margin-bottom:0.5em; width:49.5%; float:right; display:inline-block;" onclick="paypalWithoutDiscordClick();">PayPal</button>
	</div>
	<?php if($amount === '0.00'): ?>
		<p style="text-align:center; margin-bottom: 0.5em; margin-top:0.5em; color: #f3f3f3; line-height: 15px; font-size: 13px; display:inline-block;" id="ref_credit_only"><a href="<?php echo site_url('subscribe-ref/?prod=' . CLASSIC_SUBSCRIPTION_ID . '&timestamp=' . time()); ?>" class="white-link">No credit/debit card?</a></p>
	<?php endif; ?>
</div>