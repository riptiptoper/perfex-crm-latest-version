<?php defined('BASEPATH') or exit('No direct script access allowed');
echo theme_head_view();
if(is_sale_agent_logged_in()){
    get_sale_agent_template_part($navigationEnabled ? 'navigation' : '');
   get_sale_agent_template_part('aside'); ?>
<?php
  }
?>

<div id="wrapper">
   <div id="content">
         <?php hooks()->do_action('sale_agent_content_container_start'); ?>
         <?php echo theme_template_view(); ?>
   </div>
   <?php
   echo theme_footer_view();
   ?>
</div>
<?php
  /* Always have app_affiliates_footer() just before the closing </body>  */
  app_sale_agent_footer();
   /**
   * Check for any alerts stored in session
   */
   app_js_alerts();
   ?>
</body>
</html>