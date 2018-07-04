<div class="_3_container w-container">
	<h1 class="heading_login_error">Authentication Failed</h1>
    <p class="paragraph_login_error">Please Log In or Register to view <strong><?php echo str_replace('<<', '', wp_title('', false)); ?></strong>. Thanks!<br> <a href="<?php echo site_url('login/?redirect=') . urlencode(site_url() . $_SERVER['REQUEST_URI']);?>" class="white-link">Log In/Register</a><span class="text-span-3"><br></span></p>
</div>