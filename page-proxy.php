<?php /*Template Name: Proxy **Backend** */ ?>

<?php 
if($_GET['auth'] !== PROXY_AUTH) wp_redirect(site_url('404'));
switch($_GET['action']) {
	case 'get_discord_user':
	echo get_discord_user_raw($_GET['discord_id']);
	break;
}


?>