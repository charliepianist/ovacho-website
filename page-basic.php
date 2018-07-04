<?php /*Template Name: Basic Page (Used for Disclaimer) */ ?>
<!--  Last Published: Tue Apr 10 2018 01:20:11 GMT+0000 (UTC)  -->
<!DOCTYPE html>
<html data-wf-page="5ac66f4b61777f386c8c1668" data-wf-site="5a7fa1f338edac00018725fb">
<?php get_header(); ?>
    <?php while(have_posts()): the_post(); 
            the_content();
            endwhile;
    ?>
<?php get_footer(); ?>