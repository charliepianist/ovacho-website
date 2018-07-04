<?php /*Template Name: Watchlist/Recap **Backend** */ ?>
<!--  Last Published: Tue Apr 10 2018 01:20:11 GMT+0000 (UTC)  -->
<!DOCTYPE html>
<html data-wf-page="5acbac765edee8052c0d9efc" data-wf-site="5a7fa1f338edac00018725fb">
<?php get_header();
if(is_user_logged_in()): 

  //check if user has subscription
  $o_user_id = get_current_user_id(); 
  if(get_user_meta($o_user_id, 'subscription_type', true) === 'classic' || current_user_can('administrator')): 

      //check if subscription still valid
      if(time() <= get_user_meta($o_user_id, 'subscription_end_time', true) || current_user_can('administrator')): ?>
        <div class="_3_container w-container">
          <?php while(have_posts()): the_post(); 
                  the_content();
                  endwhile;
          ?>
          <?php 
          /*$o_date = getdate(strtotime('-1 week'));
          $o_after_array = array(
              'year' => $o_date['year'], 
              'month' => $o_date['month'],
              'day' => $o_date['mday']
          );*/

          //args for WP_Query objects
          $argsWatchlist = array(
            'post_type' => 'watchlist',
            'orderby' => 'date',
            'posts_per_page' => 5,
          );
          $argsRecap = array(
            'post_type' => 'recap',
            'orderby' => 'date',
            'posts_per_page' => 10,
          );
          $o_watchlists = new WP_Query($argsWatchlist);
          $o_recaps = new WP_Query($argsRecap);

          $o_watchlist_recap_array = array();
          //1 most recent, 5 oldest
          $o_post_num = 1;

          //create array of post htmls/info
          while($o_watchlists->have_posts()) {
            $o_watchlists->the_post();
            $o_watchlist_recap_array[$o_post_num] = array(
              'watchlist' => o_format_watchlist_recap_img(get_the_content_with_formatting()),
              'next_day' => strtotime(get_the_date('n/j/Y') . '+1 days'),
              'date' => date('m/d l', strtotime(get_the_date('n/j/Y') . '+1 days')),
            );
            $o_post_num++;
          }
          $o_post_num = 1;
          while($o_recaps->have_posts()) {
            $o_recaps->the_post();
            if(get_the_date('n') === date('n', $o_watchlist_recap_array[$o_post_num]['next_day']) && get_the_date('j') === date('j', $o_watchlist_recap_array[$o_post_num]['next_day']) && get_the_date('Y') === date('Y', $o_watchlist_recap_array[$o_post_num]['next_day'])) {
              $o_watchlist_recap_array[$o_post_num]['recap'] = o_format_watchlist_recap_img(get_the_content_with_formatting());
            } else {
              $o_post_num++;
              if(get_the_date('n') === date('n', $o_watchlist_recap_array[$o_post_num]['next_day']) && get_the_date('j') === date('j', $o_watchlist_recap_array[$o_post_num]['next_day']) && get_the_date('Y') === date('Y', $o_watchlist_recap_array[$o_post_num]['next_day'])) {
              $o_watchlist_recap_array[$o_post_num]['recap'] = o_format_watchlist_recap_img(get_the_content_with_formatting());
              }
            }
            $o_post_num++;
            if($o_post_num > 5) break;
          }
          //echo var_dump($o_watchlist_recap_array);
          ?>

        <?php for($o_post_num = 1; $o_post_num <= 5; $o_post_num++): 
                if($o_watchlist_recap_array[$o_post_num]):
                  $o_post_array = $o_watchlist_recap_array[$o_post_num];
        ?>
          <h1 class="heading_privacy1"><?php echo $o_post_array['date']; ?></h1>

          <div class="_3_wlr1 w-row">
            <div class="w-col w-col-6"><?php echo $o_post_array['watchlist']; ?></div>
            <div class="w-col w-col-6"><?php if(isMobile()) echo '<br>'; ?> <?php echo $o_post_array['recap']; ?></div>
          </div>
        <?php endif; endfor; ?>
      </div>
<?php 
else: //CLASSIC EXPIRED 
update_user_meta($o_user_id, 'subscription_type', 'basic');
remove_discord_user(get_user_meta($o_user_id, 'discord_id', true));
get_template_part('parts/payment/classic-expired');
endif;
else: //BASIC SUBSCRIPTION ?>

    <div class="_3_container w-container">
      <?php while(have_posts()): the_post(); 
              the_content();
              endwhile;
      ?>
      <?php 
      /*$o_date = getdate(strtotime('-1 week'));
      $o_after_array = array(
          'year' => $o_date['year'], 
          'month' => $o_date['month'],
          'day' => $o_date['mday']
      );*/

      //args for WP_Query objects
      $argsWatchlist = array(
        'post_type' => 'basic-watchlist',
        'orderby' => 'date',
        'posts_per_page' => 5,
      );
      $o_watchlists = new WP_Query($argsWatchlist);

      $o_watchlist_recap_array = array();
      //1 most recent, 5 oldest
      $o_post_num = 1;

      //create array of post htmls/info
      while($o_watchlists->have_posts()) {
        $o_watchlists->the_post();
        $o_watchlist_recap_array[$o_post_num] = array(
          'watchlist' => o_format_watchlist_recap_img(get_the_content_with_formatting()),
          'date' => date('m/d l', strtotime(get_the_date('n/j/Y') . '+1 days')),
        );
        $o_post_num++;
      }
      //echo var_dump($o_watchlist_recap_array);
      ?>

    <?php for($o_post_num = 1; $o_post_num <= 5; $o_post_num++): 
            if($o_watchlist_recap_array[$o_post_num]):
              $o_post_array = $o_watchlist_recap_array[$o_post_num];
    ?>
      <h1 class="heading_privacy1"><?php echo $o_post_array['date']; ?></h1>

      <div class="_3_wlr1 w-row">
        <div class="w-col w-col-6"><?php echo $o_post_array['watchlist']; ?></div>
      </div>
    <?php endif; endfor; ?>
  </div>
  
<?php 
endif;
else: //NOT LOGGED IN ?>
  <?php get_template_part('parts/login/not-logged-in-error'); ?>
<?php endif; ?>
  <?php get_footer(); ?>