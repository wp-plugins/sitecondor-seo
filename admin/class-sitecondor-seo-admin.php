<?php
/**
 * SiteCondor_SEO
 *
 * @package   SiteCondor_SEO
 * @author    Sebastián Brocher <seb@sitecondor.com> and Judd Lyon <judd@sitecondor.com>
 * @license   GPL-2.0+
 * @link      https://www.sitecondor.com/wordpress-plugin
 * @copyright 2015 Noctual, LLC
 */

/**
 * Plugin class (Admin side)
 *
 * @package SiteCondor_SEO_Admin
 * @author  Sebastián Brocher <seb@sitecondor.com>
 */
class SiteCondor_SEO_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 *
		 */
		$plugin = SiteCondor_SEO::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		// Add form settings initialization before any admin page loads
	  add_action( 'admin_init', array($this, 'register_sitecondor_settings_cb') );
		add_action( 'admin_notices', array($this, 'sitecondor_notices_cb') );

		// Add form post action to process create new job
		add_action( 'admin_post_create', array($this, 'sitecondor_create_job_cb') );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), SiteCondor_SEO::VERSION );
			wp_enqueue_style( $this->plugin_slug .'-admin-tooltips', plugins_url( 'assets/css/tooltips.css', __FILE__ ), array(), SiteCondor_SEO::VERSION );			
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-chart-script', plugins_url( 'assets/js/chart.min.js', __FILE__ ), array(), SiteCondor_SEO::VERSION );
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-tooltip' ), SiteCondor_SEO::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu for users with manage_options capabilities
		 * For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */
		$this->plugin_screen_hook_suffix = add_menu_page(
			'SiteCondor SEO',
			'SiteCondor SEO',      
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' ),
			plugins_url( 'assets/scs-menu-icon.png' , dirname(__FILE__) )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	/**
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function register_sitecondor_settings_cb() {
		register_setting( 'sitecondor_options_group', 'sitecondor_options', array( $this, 'sitecondor_options_validate_cb' ) );
		add_settings_section( 'sitecondor_options_section_main', '', array( $this, 'sitecondor_options_section_main_cb' ), 'sitecondor' );
		add_settings_field( 'sitecondor_options_field_email', 'Email', array( $this, 'sitecondor_options_field_email_cb'), 'sitecondor', 'sitecondor_options_section_main' );
		add_settings_field( 'sitecondor_options_field_password', 'Password', array( $this, 'sitecondor_options_field_password_cb'), 'sitecondor', 'sitecondor_options_section_main' );		
	}

	/**
	 * NOTE:     Creates a job per request
	 *
	 * @since    1.3.2
	 */	
	public function sitecondor_create_job_cb() {

		$options = get_option( 'sitecondor_options' );

		// Create job
		$site_url = get_option( 'siteurl' );
		$job_res = sc_create_job( $options['apikey'], $site_url );

		if ( $job_res ) { 
			$msg = 'success';
		} else {
			$msg = 'failure';
		}
	
    $redirect_to_url = admin_url('?page=sitecondor-seo&tab=reports&msg=' . $msg);
    wp_safe_redirect( $redirect_to_url );
    exit;
	}

	/**
	 * NOTE:     Displays error setting page error messages
	 *
	 * @since    1.0.0
	 */	
	public function sitecondor_notices_cb() {
		// for regular, wordpress options update error messages
		settings_errors( 'sitecondor_options' );

		// 
		if(isset($_GET['msg'])) {
      if('success' === $_GET['msg']) {
      	echo '<div class="updated"><p>Success! Your new report will be processed soon.</p></div>';
      } else {
      	echo '<div class="error"><p>Sorry, we were unable to create your report, please try again.</p></div>';
      }
    }
	}

	/**
	 * Render HTML markup for section text
	 *
	 * @since    1.0.0
	 */
	public function sitecondor_options_section_main_cb() {
		echo '<p>Create your free account to get started!</p>';
	}	

	/**
	 * Render HTML markup for email input text
	 *
	 * @since    1.0.0
	 */
	public function sitecondor_options_field_email_cb() {
		$options = get_option( 'sitecondor_options' );
		$value = '';
		if ( $options['email'] ) {
			$value = $options['email'];
		} else {
		  $current_user = wp_get_current_user();
		  if ( $current_user instanceof WP_User && $current_user->user_email ) {
				$value = $current_user->user_email;
			}
		}
		echo "<input id='sitecondor_email' name='sitecondor_options[email]' size='40' type='text' value='" . $value . "'>";
	}	

	/**
	 * Render HTML markup for password input text
	 *
	 * @since    1.0.0
	 */
	public function sitecondor_options_field_password_cb() {
		echo "<input id='sitecondor_password' name='sitecondor_options[password]' size='40' type='password' value=''>";		
	}	

	/**
	 * Validate user input
	 *
	 * @since    1.0.0
	 */


	function sitecondor_options_validate_cb( $input ) {

		// @TODO: fix this hack
		// WP misteriously calls this validate cb twice when on first POST after installing plugin
		global $prevent_double_execution;
		if(isset($prevent_double_execution)) {
			return $input;
		}
		$prevent_double_execution = true;

		$options = get_option( 'sitecondor_options' );

		// check site url is not blacklisted
		$site_url = get_option( 'siteurl' );		
		$parsed_url = parse_url( $site_url );

		if ( in_array( $parsed_url['host'], array( '', 'localhost', '127.0.0.1', '0.0.0.0' ) ) ) {
		  add_settings_error( 'sitecondor_options', 'sc_options_pre_api_host', 'SiteCondor cannot crawl the detected host name (' . $parsed_url['host'] . '). Please make sure this WordPress site is public and the site URL is configured in the General Settings section before creating your account.', 'error' );			
			return array();
		}

		// create user if we don't have one yet
		if ( !$options['apikey'] ) {

			$options['email'] = trim( $input['email'] );
			$options['password'] = trim( $input['password'] );		

			// check email/password presence before calling API
			if ( $options['email'] == '' ) {
		    add_settings_error( 'sitecondor_options', 'sc_options_pre_api_email', 'Please enter a valid email address.', 'error' );
		    return array();
			}

			if ( $options['password'] == '' ) {
		    add_settings_error( 'sitecondor_options', 'sc_options_pre_api_password', 'Please enter a valid password.', 'error' );
		    return $options;	// return $options and not just array to preserve email input
			}
			
		  $current_user = wp_get_current_user();
		  if ( $current_user instanceof WP_User ) {
		  	if ( $current_user->user_firstname && $current_user->user_lastname ) {
		    	$name = $current_user->user_firstname . " " . $current_user->user_lastname;
		    } elseif ( $current_user->user_nicename ) {
		    	$name = $current_user->user_nicename;
		    } elseif ( $current_user->display_name ) {
		    	$name = $current_user->display_name;
		    } else {
		    	$name = "WordPress Admin";
		    }
		  }

			$company_name = get_option( 'siteurl' );

			// call SiteCondor API - signup				
			$account_res = sc_create_user( $name, $company_name, '', $options['email'], $options['password'], 'wpfree', '' );

			// clean up password to prevent saving it
			$options['password'] = '';

			if ( $account_res ) { 
				$options['apikey'] = $account_res['apiKey'];
				$options['user_id'] = $account_res['id'];				
				$account_success = true;
			} else {
				return $options; 		// error from API or could not reach API
			}

		}

		// create job if we don't yet have one
		if ( !$options['job_id'] ) {

			// Create job
			$job_res = sc_create_job( $options['apikey'], $site_url );

			if ( $job_res ) { 
				$options['job_id'] = $job_res['_id'];				
				$job_success = true;
			} else {
				return $options; 		// error from API or could not reach API
			}

		}

		// create schedule if we don't yet have one
		if ( !$options['schedule_id'] ) {

			$sched_res = sc_create_schedule( $options['apikey'], $options['job_id'] );

			if ( $sched_res ) { 
				$options['schedule_id'] = $sched_res['_id'];
				$sched_success = true;
			} else {
				return $options; 			// error from API or could not reach API			
			}
		}

		if ( isset( $account_success ) && isset( $job_success ) && isset( $sched_success ) ) {
			$notice_markup = "<p>Success! Your first weekly Report and Recommendations will be available shortly.</p>";
	    add_settings_error( 'sitecondor_options', 'sc_account_job_sched_success', $notice_markup, 'updated' );			
		} 

		return $options;				    
	}

}
