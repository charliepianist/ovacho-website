<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-118022164-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-118022164-1');
  </script>
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" type="text/javascript" intergrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

  <?php
    //current url
    global $wp;
    $current_url = home_url( add_query_arg( array(), $wp->request ) );

    //page title
    $o_page_title = str_replace('<<', '', wp_title('', false));
    if(preg_match('/^ +Tickers +[A-Z]{1,5}$/', $o_page_title)) {
      $o_matches = array();
      preg_match('/[A-Z]{1,5}$/', $o_page_title, $o_matches);
      $o_ticker = $o_matches[0];
      $o_page_title = $o_ticker . ' Archive';
    }
    if($o_page_title !== '') $o_page_title .= ' | ';
    else $o_page_title = 'Home | ';
    $o_page_title .= get_bloginfo('name');
  ?>
  <meta charset="utf-8">
  <meta property="og:image" content="<?php bloginfo('stylesheet_directory');?>/images/thumbnail.png">
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?php echo $current_url; ?>">
  <meta property="og:description" content="Our goal is to make the complex simple. We aim to provide quality, in-depth information and resources to help investors make educated decisions in the stock market.">
  <meta property="og:title" content="<?php echo $o_page_title; ?>">
  <title><?php echo $o_page_title; ?></title>
  <?php /*if(strpos(get_page_template(), 'watchlist-recap.php') !== false): ?> 
    <meta content="Watchlist &amp; Recap" property="og:title"> 
  <?php elseif(strpos(get_page_template(), 'charts-analysis.php') !== false): ?> 
    <meta content="Charts &amp; Analysis" property="og:title">
  <?php elseif(strpos(get_page_template(), 'discord.php') !== false): ?> 
    <meta content="Discord" property="og:title"> 
    <?php elseif(strpos(get_page_template(), 'basic.php') !== false): ?> 
    <meta content="Disclaimer" property="og:title">
  <?php endif; */

  /*if($o_page_title !== '') echo '<meta content="' . $o_page_title . '" property="og:title">';*/
  ?>
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="<?php bloginfo('stylesheet_directory');?>/images/Webicom.jpg" rel="shortcut icon" type="image/x-icon">
  <link href="https://daks2k3a4ib2z.cloudfront.net/img/webclip.png" rel="apple-touch-icon">
  <?php wp_head(); ?>
</head>
<?php if(is_front_page() || is_404()): ?>
  <body class="body"> 
  <?php else: ?>
  <body class="body-2"> 
  <?php endif; ?> 
  <div data-collapse="medium" data-animation="default" data-duration="500" class="_1_navbar2 w-nav">
    <div class="container-3 w-container">
      <nav role="navigation" class="nav-menu-2 w-nav-menu">
        <div class="div-block-9"><a href="<?php bloginfo('url'); ?>"><img src="<?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO.png" width="40" srcset="<?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO-p-500.png 500w, <?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO-p-800.png 800w, <?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO-p-1080.png 1080w, <?php bloginfo('stylesheet_directory');?>/images/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO.png 1193w" sizes="(max-width: 767px) 100vw, 35px" data-w-id="3df837b9-4160-8770-527f-fd1fba6f9c88" class="_1_logo w-hidden-small w-hidden-tiny" data-ix="hidden-to-visible"></a></div><a href="<?php bloginfo('url'); ?>" class="_1_navlink w-nav-link">Home</a><a href="<?php bloginfo('url'); ?>/watchlist-recap/" class="_1_navlink w-nav-link">Watchlist/Recap</a><a href="<?php bloginfo('url'); ?>/charts-analysis/" class="_1_navlink w-nav-link">Charts/Analysis</a><a href="<?php bloginfo('url'); ?>/discord/" class="_1_navlink w-nav-link">Discord</a><a href="<?php bloginfo('url'); ?>/pricing/" class="_1_navlink w-nav-link">Pricing</a><!--<a href="#" class="_1_navlink w-nav-link">Resources</a>--><a href="<?php bloginfo('url'); ?>/about/" class="_1_navlink w-nav-link">About</a><?php if(is_user_logged_in()) echo '<a href="' . site_url('account') . '" class="_1_navlink w-nav-link">Account</a>';?><a href="<?php if(is_user_logged_in()) echo wp_logout_url(); else echo site_url('login/?redirect=') . urlencode(site_url() . $_SERVER['REQUEST_URI']); ?>" class="_1_navlink w-nav-link"><?php if(is_user_logged_in()) echo 'Log Out'; else echo 'Log In/Register'; ?></a></nav>
      <div class="menu-button-2 w-nav-button">
        <div class="menu-icon w-icon-nav-menu"></div>
      </div>
    </div>
  </div>