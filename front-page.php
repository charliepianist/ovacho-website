<!DOCTYPE html>
<html data-wf-page="5aac99ecaf97838325230d58" data-wf-site="5a7fa1f338edac00018725fb">
<?php get_header();
/*send_email('ovachoinvestments@gmail.com', 'Thank you! Your subscription was renewed automatically.', '<p style="text-align:center;">Your subscription with us has been automatically renewed. Here is your official receipt:</p>' . format_ctr('========== TRANSACTION RECORD ==========
OVACHO LLC
19 BIRCH DR
PLAINSBORO, NJ 08536
United States


TYPE: Purchase

ACCT: Visa                    $ 0.50 USD

CARDHOLDER NAME : Charles Liu
CARD NUMBER     : ############0000
DATE/TIME       : 27 Jun 18 11:37:31
REFERENCE #     : 001 1492039 T
AUTHOR. #       : 031294
TRANS. REF.     : 

    Approved - Thank You 100


Please retain this copy for your records.

Cardholder will pay above amount to
card issuer pursuant to cardholder
agreement.
========================================') . '<p style="color:#898989; text-align:center;"><a style="color:#898989;" href="' . site_url('privacy-policy') . '">PRIVACY POLICY</a> | <a style="color:#898989;" href="' . site_url('terms') . '">TERMS</a> | <a style="color:#898989;" href="' . site_url('account') . '">UNSUBSCRIBE</a></p>');*/

  //var_dump(get_user_meta(get_current_user_id()));
  //identify_discord();
  //send_discord_message_to_user('194984299836080129', 'test');
?>
  <?php if(!is_user_logged_in()): ?>
  <div data-w-id="ad5bf1b0-05dd-7d5f-c4ea-c2e73af836cb" style="opacity:1" class="_1_wrapper">
    <div data-w-id="fbd8402c-9979-f7f3-54cf-f12f9375d5d8" style="-webkit-transform:translateX(0) translateY(0) translateZ(0) scaleX(1) scaleY(1) scaleZ(1) rotateX(0) rotateY(0) rotateZ(0) skewX(0) skewY(0);-moz-transform:translateX(0) translateY(0) translateZ(0) scaleX(1) scaleY(1) scaleZ(1) rotateX(0) rotateY(0) rotateZ(0) skewX(0) skewY(0);-ms-transform:translateX(0) translateY(0) translateZ(0) scaleX(1) scaleY(1) scaleZ(1) rotateX(0) rotateY(0) rotateZ(0) skewX(0) skewY(0);transform:translateX(0) translateY(0) translateZ(0) scaleX(1) scaleY(1) scaleZ(1) rotateX(0) rotateY(0) rotateZ(0) skewX(0) skewY(0)" class="_1_login-wrapper"><img src="<?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO.png" srcset="<?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO-p-500.png 500w, <?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO-p-800.png 800w, <?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO-p-1080.png 1080w, <?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO.png 1193w" sizes="100vw" data-w-id="1b765782-d06a-2516-6677-4c27a4ed8756" class="_1_logo-rotate-log-in-2">
      <h1 class="_1_login-wrapper-heading">Log In</h1>
        <?php get_template_part('parts/login/login-form'); ?>
        <div data-w-id="3004f7f0-0f2d-e4e0-0ddb-84ca3c0394aa" class="_1_login-text-close"><a href="#" class="o-login-form-link">Close</a></div>
    </div>
  </div>
  <div data-w-id="5b0fafd8-0e5a-c647-b1cd-319a5d4edf60" style="opacity:1" class="_2_wrapper">
    <div data-w-id="5b0fafd8-0e5a-c647-b1cd-319a5d4edf61" class="_2_sign-up-wrapper"><img src="<?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO.png" srcset="<?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO-p-500.png 500w, <?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO-p-800.png 800w, <?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO-p-1080.png 1080w, <?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO.png 1193w" sizes="100vw" data-w-id="5b0fafd8-0e5a-c647-b1cd-319a5d4edf62" class="_1_logo-rotate-log-in">
      <h1 class="_1_login-wrapper-heading">Sign Up</h1>
        <?php get_template_part('parts/login/register-form'); ?>
      <div data-w-id="5b0fafd8-0e5a-c647-b1cd-319a5d4edf78" style="-webkit-transform:translateX(0) translateY(0) translateZ(0) scaleX(1) scaleY(1) scaleZ(1) rotateX(0) rotateY(0) rotateZ(0) skewX(0) skewY(0);-moz-transform:translateX(0) translateY(0) translateZ(0) scaleX(1) scaleY(1) scaleZ(1) rotateX(0) rotateY(0) rotateZ(0) skewX(0) skewY(0);-ms-transform:translateX(0) translateY(0) translateZ(0) scaleX(1) scaleY(1) scaleZ(1) rotateX(0) rotateY(0) rotateZ(0) skewX(0) skewY(0);transform:translateX(0) translateY(0) translateZ(0) scaleX(1) scaleY(1) scaleZ(1) rotateX(0) rotateY(0) rotateZ(0) skewX(0) skewY(0)" class="_1_login-text-close"><a href="#" class="o-login-form-link">Close</a></div>
    </div>
  </div>
<?php endif; ?>
  <div id="1_1" class="_1_1">
    <div class="_1_container1 w-container">
      <h1 data-w-id="9199996d-766f-36cf-a923-645d512e8f0a" style="opacity:0;-webkit-transform:translateX(-7VH) translateY(0) translateZ(0) scaleX(1) scaleY(1) scaleZ(1) rotateX(0) rotateY(0) rotateZ(0) skewX(0) skewY(0);-moz-transform:translateX(-7VH) translateY(0) translateZ(0) scaleX(1) scaleY(1) scaleZ(1) rotateX(0) rotateY(0) rotateZ(0) skewX(0) skewY(0);-ms-transform:translateX(-7VH) translateY(0) translateZ(0) scaleX(1) scaleY(1) scaleZ(1) rotateX(0) rotateY(0) rotateZ(0) skewX(0) skewY(0);transform:translateX(-7VH) translateY(0) translateZ(0) scaleX(1) scaleY(1) scaleZ(1) rotateX(0) rotateY(0) rotateZ(0) skewX(0) skewY(0)" class="_1_title">Stocks.<br>Let&#x27;s Make It <span>Easy</span>.</h1>
      <p data-w-id="9199996d-766f-36cf-a923-645d512e8f0c" style="opacity:0" class="_1_subtitle">Empowering You To Make Your Own Financial Decisions</p>
      
      <div class="div-block-14 w-clearfix">
        <?php if(!is_user_logged_in()): ?><a href="#" data-w-id="77523f5d-0212-658e-3bcd-6d833ee4a5af" style="opacity:0" class="_1_logina w-button">Log In</a><a href="#" data-w-id="d05e7c3e-753c-2311-810a-ddd94034f227" style="opacity:0" class="_1_signup w-button">Sign Up</a>
        <?php else: ?>
          <a href="<?php echo site_url('watchlist-recap');?>" data-w-id="77523f5d-0212-658e-3bcd-6d833ee4a5af" style="opacity:0" class="_1_wl w-button">Watchlist/Recap</a><a href="<?php echo site_url('charts-analysis');?>" data-w-id="d05e7c3e-753c-2311-810a-ddd94034f227" style="opacity:0" class="_1_ca w-button">Charts/Analysis</a>
        <?php endif;?>
    </div><img src="<?php bloginfo('stylesheet_directory');?>/images/486805-200.png" width="80" height="80" data-w-id="bc8fb39e-0f36-d6ab-0877-a160e95c00d4" style="opacity:1" class="image"><a href="#1_2" class="link-block w-inline-block w-clearfix"><img src="<?php bloginfo('stylesheet_directory');?>/images/Untitled-3.png" width="35" srcset="<?php bloginfo('stylesheet_directory');?>/images/Untitled-3-p-500.png 500w, <?php bloginfo('stylesheet_directory');?>/images/Untitled-3-p-800.png 800w, <?php bloginfo('stylesheet_directory');?>/images/Untitled-3-p-1080.png 1080w, <?php bloginfo('stylesheet_directory');?>/images/Untitled-3.png 1500w" sizes="35px" data-w-id="aaab6c3b-07f1-7ead-b216-2652e1bd1aa2" style="opacity:0" class="image-2"></a></div>
  </div>
  <div id="1_2" class="_1_2">
    <div class="_1_container3 w-container">
      <h1 data-w-id="db652989-1bfc-6dd8-49f8-11046aedc074" style="opacity:0" class="_1_subheader">Our Mission</h1>
      <p class="_1_description">Created specifically to help investors grow and thrive.</p>
      <p class="_1_text">Our goal is to make the complex simple. We aim to provide quality, in-depth information and resources to help investors make educated decisions in the stock market. <br><br>Believe us when we say that we understand with the difficulty of entering into the financial world and the feeling of having no idea where to turn for financial assistance. We care deeply about our clients and aim to provide straightforward, and easy-to-understand advice.</p>
    </div>
  </div>
  <div id="1_3" class="_1_3">
    <div class="_1_container3a w-container">
      <div class="w-clearfix">
        <h1 data-w-id="bd68d00f-54eb-c878-04e6-fef859d21f21" style="opacity:0" class="_1_subheadera">Our Services</h1>
      </div>
      <div class="div-block w-clearfix">
        <p class="_1_descriptiona">We take pride in our diverse array of products.</p>
        <p class="_1_text"></p>
      </div>
      <div class="div-block-10">
        <div class="div-block w-clearfix"><a href="<?php bloginfo('url'); ?>/watchlist-recap/" data-w-id="bd68d00f-54eb-c878-04e6-fef859d21f2c" class="w-button _autowidthmobile"> Watchlist &amp; Recap</a>
          <p class="_1_text2">Our watchlist gives you our daily opinion while our recap covers the day&#x27;s events.</p>
        </div>
        <div class="div-block w-clearfix"><a href="<?php bloginfo('url'); ?>/charts-analysis/" data-w-id="bd68d00f-54eb-c878-04e6-fef859d21f34" class="w-button _autowidthmobile">Charts &amp; Analysis</a>
          <p class="_1_text2">Our analysis includes charts along with an analysis on upcoming catalysts, balance sheets, technical analysis, and a potential course of action.</p>
        </div>
        <div class="div-block w-clearfix"><a href="<?php echo DISCORD_URL; ?>" data-w-id="bd68d00f-54eb-c878-04e6-fef859d21f39" class="w-button _autowidthmobile">Discord Group</a>
          <p class="_1_text2">We strive to spark conversations and build a community of traders and enthusiasts.</p>
        </div>
        <div class="div-block w-clearfix"><!--<a href="<?php bloginfo('url'); ?>/resources/" data-w-id="bd68d00f-54eb-c878-04e6-fef859d21f3e" class="_1_buttonb w-button">--><a href="#" data-w-id="bd68d00f-54eb-c878-04e6-fef859d21f3e" class="w-button _autowidthmobile">Resources</a>
          <p class="_1_text2">Our resources help educate investors with a variety of topics and build knowledge for long-term success. <strong>Coming Soon.</strong></p>
        </div>
      </div>
    </div>
  </div>
  <div id="1_4" class="_1_4">
    <div class="_1_container3 w-container">
      <h1 data-w-id="11c7cb1e-0c0c-596b-a137-c689b2ce0ac5" style="opacity:0" class="_1_subheader">Testimonials</h1>
      <p class="_1_description">Hear from our clients.</p>
      <p class="_1_text">&quot;Robinhood jumpstarted my investing career, but I lost half of my money in a month. Through Ovacho, I not only recouped my losses but turned a profit.&quot; - Sean H.<br><br>&quot;I&#x27;ve been learning more about the markets, and your information has proven to be rather valuable. I also appreciate the rules for the group, and your discipline in what you post and how you encourage people to use their monetary resources. Thanks again!&quot; -Lucas N.</p>
    </div>
  </div>
  <div id="1_5" class="_1_5">
    <div class="_1_container4 w-container"></div>
    <div class="_1_container1 w-container">
      <h1 data-w-id="4598e7f8-8b2f-e384-e203-0df354e3866b" class="_1_subheader2">Media</h1>
      <p class="_1_subtitle2">Follow us on Social Media</p><a href="<?php echo FACEBOOK_URL; ?>" target="_blank" class="w-inline-block"><img src="<?php bloginfo('stylesheet_directory');?>/images/social-03-white.svg" class="_1_media-icon"></a><a href="<?php echo STOCKTWITS_URL; ?>" target="_blank" class="w-inline-block"><img src="<?php bloginfo('stylesheet_directory');?>/images/Untitled-4-Recovered.png" width="30" height="30" class="_1_media-icon"></a></div>
  </div>
<?php get_footer(); ?>