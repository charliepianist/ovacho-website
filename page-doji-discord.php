<?php /*Template Name: Doji Discord **Backend** */ ?>
<?php get_header();?>
<?php
	$auth = (is_user_logged_in() && get_user_meta(get_current_user_id(), 'permissions', true)->doji_discord === 'true') || current_user_can('administrator');

	if($auth):

		if(isset($_GET['discord_id'])) {
			$discord_id = $_GET['discord_id'];
			$response = add_discord_user($discord_id, DOJIYOGI_DISCORD_ROLE_ID);
			if(isset($response->code)) {
				if($response->code === 10013) $error = 'Error: Invalid User ID';
				else $error = 'Error Code ' . $response->code;
			}else {
				$user = get_discord_user($discord_id);
				$error = 'User ' . $user->username . ' (' . $discord_id . ') added successfully.';
				append_discord_nickname($discord_id, DOJIYOGI_DISCORD_NICK_SUFFIX);
			}
		}
	
?>

<!--BEGIN HTML-->
<div class="_3_container w-container">
	<h1 class="heading" <?php if(!$error_code && !isset($_POST['timestamp'])) echo 'style="text-align:center;"'?>>Doji Yogi Add Discord Role</h1>

	<div class="w-col w-col-14"></div>
	<div class="w-col w-col-13">
		<p class="paragraph_privacy1" style="padding-left:10px; text-align:center; color:red"><?php if($error) echo $error;?></p>
		<p class="paragraph_privacy1" style="padding-left:10px; padding-bottom:0.5em; text-align:center;">Discord ID of User:</p>
		<form method="get" style="margin-bottom: 0px;">
			<input type="number" class="text-field w-input" maxlength="30" name="discord_id" placeholder="Discord ID (Ex: 239132435706761325)" id="discord_id" required style="margin-bottom: 0.5em; -webkit-appearance: none;">
			<input type="submit" style="display:none;" id="submit_form">
			<p id="discord_id_validating" style="display: none; text-align:center; margin-bottom: 0.5em; line-height: 15px; font-size: 13px; color:red;">Validating Discord ID...</p>
			<p id="discord_id_error" style="display: none; text-align:center; margin-bottom: 0.5em; color: red; line-height: 15px; font-size: 13px;">Invalid Discord ID.</p>
		</form>
		<button data-w-id="c5199c12-e32e-d2be-a248-cf82a26d0f7a" class="pricing_button w-button" id="submit_classic_form" style="margin-top:0px; width:100%;" onclick="payNowClick();">Add Discord Role</button>
	</div>
	<div class="w-col w-col-14"></div>
</div>
<?php else: //USER NOT AUTHORIZED OR NOT LOGGED IN
if(!is_user_logged_in()) get_template_part('parts/login/not-logged-in-error'); //USER NOT LOGGED IN 
else get_template_part('parts/login/not-authorized-error');  //USER NOT AUTHORIZED?>
<?php endif; ?>
<!--Override footer type-->
<input style="display:none;" id="footer_class" value="_1_6">
<script>
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
  function submitForm() {
  	$('#submit_form').click();
  }
</script>
<?php get_footer(); ?>