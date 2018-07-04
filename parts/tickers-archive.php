<h1 class="heading_privacy1">Tickers Archive</h1>
    <div class="div-block-16 w-clearfix">
      <?php 
      $o_args = array(
        'taxonomy' => 'tickers',
        'orderby' => 'name',
        'hide_empty' => true,
      );
      //get all tickers
      $o_terms = get_terms($o_args);

      if(!$o_terms):
        echo '<p class="paragraph_privacy1">Sorry, no tickers are available at this time.<span class="text-span-3">
</span></p>';
      else:
        foreach($o_terms as $o_term): 
          $o_term_array = $o_term->to_array(); ?>
          <a href="<?php echo get_term_link($o_term_array['term_id'], $o_term_array['taxonomy']); ?>" class="_3_archive w-button"><?php echo $o_term_array['name']; ?></a>
      <?php 
        endforeach;
      endif;
      ?>
    </div>