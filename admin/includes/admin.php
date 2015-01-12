<?php

	/**
	 * NOTE:     Returns true if job is completed, false otherwise
	 *
	 * @since    1.0.0
	 */

	function sc_is_job_ready_for_charting( $job ) {
		return 
			$job['status'] != 'queued' &&
			$job['status'] != 'started' &&
			$job['status'] != 'failed'
		;
	}

	/**
	 * NOTE:     Alias for sc_is_job_ready_for_charting for now
	 *
	 * @since    1.3.0
	 */

	function sc_is_job_successfully_completed( $job ) {
		return sc_is_job_ready_for_charting($job);
	}	

	/**
	 * NOTE:     Returns true if job is completed and finished early or reached max resources, false otherwise
	 *
	 * @since    1.3.3
	 */

	function sc_is_job_successfully_completed_early( $job ) {
		return 
			$job['status'] == 'finished early' ||
			$job['status'] == 'reached max resources' 
		;	
	}		

	/**
	 * NOTE:     SiteCondor resources all url
	 *
	 * @since    1.0.0
	 */

  function sc_resources_all_url( $job_id ) {
  	return "http://www.sitecondor.com/app/#/explore/" . $job_id . "/resources/all";
  }

	/**
	 * NOTE:     SiteCondor resources pages url
	 *
	 * @since    1.0.0
	 */

	function sc_resources_pages_url( $job_id ) {
  	return "http://www.sitecondor.com/app/#/explore/" . $job_id . "/resources/pages";
	}

	/**
	 * NOTE:     SiteCondor resources 404 url
	 *
	 * @since    1.0.0
	 */

	function sc_resources_404_url( $job_id ) {
		return "http://www.sitecondor.com/app/#/explore/" . $job_id . "/resources/404s";
	}

	/**
	 * NOTE:     SiteCondor redirects url
	 *
	 * @since    1.0.0
	 */

	function sc_resources_redirects_url( $job_id ) {
		return "http://www.sitecondor.com/app/#/explore/" . $job_id . "/resources/redirects";
	}

	/**
	 * NOTE:     SiteCondor other errors url
	 *
	 * @since    1.0.0
	 */

	function sc_resources_other_errors_url( $job_id ) {
		return "http://www.sitecondor.com/app/#/explore/" . $job_id . "/resources/other-errors";
	}

	/**
	 * NOTE:     SiteCondor titles missing url
	 *
	 * @since    1.0.0
	 */

	function sc_titles_missing_url( $job_id ) {
		return "http://www.sitecondor.com/app/#/explore/" . $job_id . "/titles/missing";
	}

	/**
	 * NOTE:     SiteCondor headings missing url
	 *
	 * @since    1.0.0
	 */	

	function sc_headings_missing_url( $job_id ) {
		return "http://www.sitecondor.com/app/#/explore/" . $job_id . "/h1s/missing";
	}

	/**
	 * NOTE:     SiteCondor meta descriptions missing url
	 *
	 * @since    1.0.0
	 */	

	function sc_meta_descriptions_missing_url( $job_id ) {
		return "http://www.sitecondor.com/app/#/explore/" . $job_id . "/meta-descriptions/missing";
	}

	/**
	 * NOTE:     SiteCondor images missing url
	 *
	 * @since    1.0.0
	 */	

	function sc_images_missing_url( $job_id ) {
		return "http://www.sitecondor.com/app/#/explore/" . $job_id . "/images/missing-alt";		
	}

	/**
	 * NOTE:     SiteCondor account url
	 *
	 * @since    1.0.0
	 */

	function sc_reset_password_url() {
		return "https://www.sitecondor.com/app/#/reset-password";	
	}

	/**
	 * NOTE:     SiteCondor account url
	 *
	 * @since    1.0.0
	 */

	function sc_account_url() {
		return "https://www.sitecondor.com/app/#/account";
	}

	/**
	 * NOTE:     SiteCondor upgrade url
	 *
	 * @since    1.3.2
	 */

	function sc_upgrade_url($source) {
		return "https://www.sitecondor.com/app/#/upgrade?source=" . $source;
	}
	
	/**
	 * NOTE:     SiteCondor schedules url
	 *
	 * @since    1.0.0
	 */

	function sc_schedules_url() {
		return "https://www.sitecondor.com/app/#/schedules";
	}

	/**
	 * NOTE:     SiteCondor job overview url
	 *
	 * @since    1.0.0
	 */

	function sc_job_url( $job_id ) {
		return "https://www.sitecondor.com/app/#/explore/" . $job_id . "/job";
	}

	/**
	 * NOTE:     Create a SiteCondor user
	 *
	 * @since    1.0.0
	 */
	function sc_create_user( $name, $company_name, $phone_number, $email, $password, $plan, $invite ) {
		$method = 'POST';
		$url = 'signup';
		$data = array(
			'name' => $name,
			'companyName'	=> $company_name,
			'phoneNumber' => $phone_number,
			'email' => $email,
			'password' => $password,
	    'plan' => $plan,
	    'invite' => $invite
		);

		$api_call = sc_call_sitecondor_api( $method, $url, $data );

		$response = sc_field_api_response( 'sc_create_user', $api_call );

		return $response;
	}

	/**
	 * NOTE:     Create a SiteCondor job
	 *
	 * @since    1.0.0
	 */
	function sc_create_job($api_key, $job_url) {
	
		$parsed_url = parse_url($job_url);
		$slash = '';

		if($parsed_url['host'][strlen($parsed_url['host']) - 1 ] != '/' && $parsed_url['path'][0] != '/') {
			$slash = '/';
		}

		$normalized_job_url = $parsed_url['host'] . $slash . $parsed_url['path'] . $parsed_url['query'] . $parsed_url['fragment'];

		// Call SiteCondor API - signup
		$url = 'jobs?apikey=' . $api_key;
		$data = array(
			# job domain URL
			'url' => $normalized_job_url,
			# initial protocol for job domain URL
			'initialProtocol' => $parsed_url['scheme'],
			# fetch page (html) resources
			'includePages' => true,
			# fetch image resources
			'includeImages' => false,
			# fetch javascript (JS) resources
			'includeJavascript' => false,
			# fetch stylesheet (css) resources
			'includeStylesheets' => false,
			# fetch PDF resources
			'includePDFs' => false,
			# filter URLs before fetching
			'urlFilter' => "",
			# disregard query strings
			'disregardQueryStrings' => true,
			# disregard jsession in urls
			'disregardJSession' => true,
			# user agent crawler sends to server
			'userAgent' => "Mozilla/5.0 (compatible; SiteCondor; http://www.sitecondor.com)",
			# interval between requests (in milliseconds)
			'interval' => 300,
			# maximum resource fetching concurrency
			'maxConcurrency' => 3,
			# maximum time crawler will wait for headers (in milliseconds)
			'timeout' => 60000,
			# treat www subdomain the same as non-www
			'ignoreWWWDomain' => true,
			# scan subdomains other than www
			'scanSubdomains' => false,
			# accept & send back cookies while crawling the site
			'acceptCookies' => true,
			# if using proxy, you should also provide proxyHostname and proxyPort attributes
			'useProxy' => false,
			# limit the maximum # of resources to fetch for the job
			'maxResources' => ""
		);

		$api_call = sc_call_sitecondor_api('POST', $url, $data);

		$response = sc_field_api_response( 'sc_create_job', $api_call );

		return $response;
	}
		

	/**
	 * NOTE:     Create a SiteCondor schedule
	 *
	 * @since    1.0.0
	 */
	function sc_create_schedule($api_key, $job_id) {
	
		// Call SiteCondor API - signup
		$url = 'schedules?apikey=' . $api_key;
		$data = array(
			'job' => $job_id,
      'frequency' => 'weekly',
      'resources404Threshold' => '',
      'resourcesOtherErrorsThreshold' => '',
      'titlesMissingThreshold' => '',
      'imagesMissingAltThreshold' => '',
      'metaDescriptionsMissingThreshold' => '',
      'headingsMissingThreshold' => ''
		);

		$api_call = sc_call_sitecondor_api('POST', $url, $data);

		$response = sc_field_api_response( 'sc_create_schedule', $api_call );

		return $response;		
	}		

	/**
	 * NOTE:     Get SiteCondor user
	 *
	 * @since    1.3.2
	 */

	function sc_get_user($api_key) {
	
		$url = 'user';
		$method = 'GET';
		$data = array('apikey' => $api_key);

		$res = sc_call_sitecondor_api($method, $url, $data);
		$result = json_decode($res['result'], true);

		if($res['status'] != '200') {
	    return false;	// leave error reporting for view
		} 	
		
		// if successful, user is the first element in decoded array
    $user = array_shift($result);
		return $user;

	}

	/**
	 * NOTE:     Get SiteCondor jobs
	 *
	 * @since    1.0.0
	 */

	function sc_get_jobs($api_key) {
	
		$url = 'jobs';
		$method = 'GET';
		$data = array('apikey' => $api_key);

		$res = sc_call_sitecondor_api($method, $url, $data);
		$result = json_decode($res['result'], true);

		if($res['status'] != '200') {
	    return false;	// leave error reporting for view
		} 	
		
		return $result;			// todo: maybe iterate through all job pages instead of just showing last page

	}

	/**
	 * NOTE:     Get SiteCondor job recommendations
	 *
	 * @since    1.3.0
	 */

	function sc_get_recommendations($api_key, $job_id) {
	
		$url = 'jobs/' . $job_id . '/recommendations';
		$method = 'GET';
		$data = array('apikey' => $api_key);

		$res = sc_call_sitecondor_api($method, $url, $data);
		$result = json_decode($res['result'], true);

		if($res['status'] != '200') {
	    return false;	// leave error reporting for view
		} 	
		
		return $result;

	}

	/**
	 * Calls SiteCondor API
	 * Method: POST, PUT, GET etc
	 * Data: array("param" => "value") ==> index.php?param=value
	 *
	 * @since    1.0.0
	 */	
	function sc_call_sitecondor_api( $method, $url, $data = false ) {

		$dev = false;

		if($dev) {
			$base_url = 'http://0.0.0.0:3000/api/v1/';			
    	$sslverify = false;
    } else {
    	$base_url = 'https://www.sitecondor.com/api/v1/';
    	$sslverify = true;
    }

  	$args = array(
			'timeout' => 15,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'user-agent'  => 'WordPress/; ' . get_bloginfo( 'url' ),
			'headers' => array(),
			'body' => $data,
			'cookies' => array(),
	    'compress'    => false,
	    'decompress'  => true,
	    'sslverify'   => $sslverify,
	    'stream'      => false,
	    'filename'    => null					
		);

    switch ( $method ) {
      case "POST":
      	$args['method'] = 'POST';
      	$args['data'] = $data;
				$result = wp_remote_post( $base_url . $url, $args );
        break;
      case "PUT":
      	$args['method'] = 'PUT';
      	$args['data'] = $data;
				$result = wp_remote_post( $base_url . $url, $args );
        break;
      default:      	
        if ( $data ) {
          $url = sprintf( "%s?%s", $url, http_build_query( $data ) );
        }				
        $result = wp_remote_get( $base_url . $url, $args );
        break;
    }

		if ( is_wp_error( $result ) ) {
		   return 
			   array(
					'status' => 599, 
					'result' => $result->get_error_message()
				);
		} 		
    return 
    	array(
				'status' => $result['response']['code'], 
				'result' => $result['body']
			);
	}

	/**
	 * Handles API responses
	 *
	 * @since    1.0.0
	 */	
	function sc_field_api_response( $call_name, $api_call  ) {
		// gotcha: api_call['status'] and  $result['status'] are completely unrelated
		$result = json_decode( $api_call['result'], true );
		switch ( $api_call['status'] ) {
			case '599':
				add_settings_error('sitecondor_options', $call_name, 'Unable to connect to SiteCondor, please try again or contact support.', 'error');			
				return false;
				break;
			case '200':
				return $result;
				break;
			default:
	      $errors = $result['status'];
	      if ( is_array( $errors ) ) {
		      $errors_markup = "<p>Oops, there were errors:</p><ul>";
	        foreach ( $errors as $error ) {
	          $errors_markup .= "<li><em>" . $error . "</em></li>";
	        }
	      } else {
	      	$errors_markup = "<p>Oops, there were errors. Please try again.</p><ul>";
	      }
	      $errors_markup .= "</ul>";
	      add_settings_error( 'sitecondor_options', $call_name, $errors_markup, 'error' );
	      return false;
				break;
		} // switch 
	} // sc_field_api_response

?>