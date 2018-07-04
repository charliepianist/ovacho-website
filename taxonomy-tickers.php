<!DOCTYPE html>
<html data-wf-page="5acbb34e9ed369cd3a1146da" data-wf-site="5a7fa1f338edac00018725fb">
<?php get_header(); ?>
<?php if(is_user_logged_in() && get_user_meta(get_current_user_id(), 'subscription_type', true) === 'classic'): ?>

<div class="_3_container w-container">
	<h1 class="heading"><?php echo get_queried_object()->name . ' Archive';?></h1>
    <div class="_3_wlr1 w-row">
    <?php 
    $o_post_count = 0;
    while(have_posts()): the_post(); 
    if((get_the_date('n') < date('n') && get_the_date('j') < date('j')) || get_the_date('Y') < date('Y')) break;
    if(get_post_type() === 'chart-analysis'):
    $o_post_count++;
    ?>
      <div class="w-col w-col-6">
      <h1 class="_3_chartname">Chart/Analysis, <?php echo get_the_date('m/d/y');?>
      </h1>
      <?php echo o_filter_charts_analysis(get_the_content_with_formatting(), get_the_permalink());?>
      </div>
    <?php endif; endwhile; 
    	if($o_post_count === 0) echo '<div class="w-col"><p class="paragraph_privacy1">Sorry, no chart/analysis on this ticker has been posted in the last month.<span class="text-span-3">
</span></p></div>';
    ?>
    </div>
    <?php get_template_part('tickers-archive'); ?>
  </div>

<?php else: ?>
<div class="_3_container w-container">
  <h1 class="heading_login_error">Authentication Failed</h1>
    <p class="paragraph_login_error">You must be subscribed to our Classic Subscription to view Ticker Archives.</p>
</div>
<?php endif; ?>
<?php get_footer(); ?>