<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   SiteCondor_SEO
 * @author    Sebastián Brocher <seb@sitecondor.com> and Judd Lyon <judd@sitecondor.com>
 * @license   GPL-2.0+
 * @link      https://www.sitecondor.com/wordpress-plugin
 * @copyright 2015 Noctual, LLC
 */
?>

<?php 
	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'reports'; 

	$opt = get_option( 'sitecondor_options' );

	if ( !$opt['apikey'] || !$opt['job_id'] || !$opt['schedule_id'] ) {
		$active_tab = 'settings';
	}
?>

<div class="wrap">
  <!-- logo & tabs -->
	<h2 class="nav-tab-wrapper">
    <a href="https://www.sitecondor.com" target="_blank" class="sitecondor-logo">
      <img src="<?php echo plugins_url( 'assets/sitecondor-logo.png', dirname( dirname( __FILE__ ) ) ); ?>">
    </a>            
    <a href="?page=sitecondor-seo&tab=reports" class="nav-tab <?php if ($active_tab == 'reports') { echo 'nav-tab-active'; } ?>"><span class="dashicons dashicons-chart-bar"></span> Reports</a>    
		<a href="?page=sitecondor-seo&tab=recommendations" class="nav-tab <?php if ($active_tab == 'recommendations') { echo 'nav-tab-active'; } ?>"><span class="dashicons dashicons-clipboard"></span> Recommendations</a>
		<a href="?page=sitecondor-seo&tab=settings" class="nav-tab <?php if ($active_tab == 'settings') { echo 'nav-tab-active'; } ?>"><span class="dashicons dashicons-admin-settings"></span> Settings</a>
	</h2>

  <!-- load inc based on tab -->
  <?php 
    switch ( $active_tab ) {
      case 'recommendations':
        require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'views/_tab-recommendations.php' );
        break;      
      case 'reports':
    		require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'views/_tab-reports.php' );
      	break;
      case 'settings':
    		require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'views/_tab-settings.php' );
        break;
    }
  ?>
</div><!-- /.wrap -->



