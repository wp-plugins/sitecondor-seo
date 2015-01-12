<?php

  function report_cell_markup( $jobs, $field_name, $job_index ) {

    $current_job = $jobs[$job_index];
    $current_count = $current_job[$field_name];

    // the first part to construct the result markup is the current count of fieldname for the current job
    $result = $current_count;

    // if deep results for current_job are not yet deleted and the current_count is greater than zero, 
    // turn the result into a link (report still available)
    if ( !$current_job['resultsDeletedAt'] && $current_count > 0 ) {
      $url = '';
      switch ( $field_name ) {
        case 'resourcesAllCount':      
          $url = sc_resources_all_url( $current_job['_id'] );
          break;
        case 'resourcesPagesCount':
          $url = sc_resources_pages_url( $current_job['_id'] );    
          break;
        case 'resources404Count':
          $url = sc_resources_404_url( $current_job['_id'] );        
          break;
        case 'resourcesRedirectsCount':
          $url = sc_resources_redirects_url( $current_job['_id'] );            
          break;
        case 'resourcesOtherErrorsCount':
          $url = sc_resources_other_errors_url( $current_job['_id'] );
          break;
        case 'titlesMissingCount':
          $url = sc_titles_missing_url( $current_job['_id'] );    
          break;
        case 'headingsMissingCount':
          $url = sc_headings_missing_url( $current_job['_id'] );
          break;
        case 'metaDescriptionsMissingCount':
          $url = sc_meta_descriptions_missing_url( $current_job['_id'] );    
          break;
        case 'imagesMissingAltCount':
          $url = sc_images_missing_url( $current_job['_id'] );
          break;
      }
      if($url) {
        $result = "<a target='_blank' href='" . $url . "'>" . $current_count . "</a>";
      }
    }

    // check and see if there is an older job available to compare current_count against
    if($job_index < count($jobs) - 1) {
      $previous_job = $jobs[$job_index + 1];    // next job in array is the chronologically previous job
      $previous_count = $previous_job[$field_name];
    } else {
      return $result;     // the current_job is the oldest job and is therefore the baseline (nothing to compare against)
    }

    // if there has been no change between previous and current count, display a dash
    if($previous_count == $current_count) {
      // no change - display a dash
      return $result . "<br><div class='trend-icon'><span class='dashicons dashicons-minus'></span></div>";  
    }

    // we have a change between previous and current count, display change percent and color indicator
    $difference = $current_count - $previous_count;
    if($previous_count == 0) {
      // a change from 4,000 404s to 0 404s may be more significant than a change from 20 404s to 0 404s
      // but this is a good option for a quick summary
      if($current_count > 0) {
        $percent = 100;
      } else {
        $percent = -100;
      }
    } else {
      $percent = round($difference * 100.0 / $previous_count, 1);
    }

    // reprecent a positive percent by arrow up and negative percent by arrow down
    if($percent > 0) {
      $arrow_class = "dashicons-arrow-up";
    } else {
      $arrow_class = "dashicons-arrow-down";
    }

    // represent a good change with green, and a bad change with red
    if($field_name == 'resourcesAllCount' || $field_name == 'resourcesPagesCount') {
      // more resources and more pages is a good thing (+ is green, - is red)
      if($percent > 0) {
        $color_class = "trend-green";
      } else {
        $color_class = "trend-red";
      }
    } else {
      // more errors is a bad thing (+ is red, - is green)
      if($percent > 0) {
        $color_class = "trend-red";
      } else {
        $color_class = "trend-green";
      }      
    }

    // for display, we only use the absolute (ie non-signed) percent and represent the rest through arrow/color
    $absolute_percent = abs($percent);

    // return the whole enchilada
    return $result . "<br><div class='trend-icon " . $color_class . "'><span class='dashicons ". $arrow_class . "'></span> " . $absolute_percent . "%</div>";    
  }
?>

<div id="poststuff">
  <div id="post-body" class="metabox-holder columns-1">

    <!-- main content -->
    <div id="post-body-content">
      <div class="meta-box-sortables ui-sortable">
        <div class="postbox">          
          <h3><span>Crawl reports for <em class="reports-domain"><?php echo preg_replace( "(https?://)", "", get_option( 'siteurl' ) ); ?></em></span></h3>
          <div class="inside">
            <?php
              $options = get_option('sitecondor_options');

              if ( !$options['apikey'] || !$options['job_id'] || !$options['schedule_id'] ) {
                echo "<p>Your SiteCondor account isn't fully set up yet, please check your <a href='?page=sitecondor-seo&tab=settings'>Settings</a>.</p>";
              } else {
                $res = sc_get_jobs( $options['apikey'] );
                $jobs = $res['jobs'];
                $user = sc_get_user( $options['apikey'] );
                
                if ( !is_array($jobs) || !$user ) {
                  echo "<p><strong>Sorry, we were unable to connect to SiteCondor. Please try again or contact support.</strong></p>";
                } else {

                  if ( $user['plan'] == 'wpfree' ) {                  
                  ?>
                  
                    <div class="upgrade-bar">
                      <span>Need more credits or to create reports anytime?</span> <a href="<?php echo sc_upgrade_url('reports'); ?>" target="_blank" class="button-secondary">Upgrade!</a>
                    </div><!-- /.upgrade-bar -->
                  
                  <?php
                  }

                  if($user['plan'] != 'wpfree' && $user['crawlsLeft'] > 0 && $user['resourcesLeft'] > 0) {
                    echo '<form method="post" action="' . admin_url( 'admin-post.php' ) . '">';
                    echo '<input type="hidden" name="action" value="create">';
                    echo '<input type="submit" value="Create New Report" class="button-primary">';
                    echo '</form>';
                  }

                  if ( count($jobs) < 1 ) {
                    echo "<p>Sorry, you don't have any Reports yet.</p>";
                  } else {
                    if ( count( array_filter( $jobs, "sc_is_job_ready_for_charting" ) ) > 1 ) {
                    ?>
                    
                    <div id="legend" class="legend">

                      <span class="title" style="color: rgb(64, 127, 0); background-color: rgba(64, 127, 0, 0.2);">
                        <input class="refresh-chart" type="checkbox" id="resources" checked>Resources</input>
                      </span>
                      <span class="title" style="color: rgb(102, 204, 0); background-color: rgba(102, 204, 0, 0.2);">
                        <input class="refresh-chart" type="checkbox" id="pages" checked>Pages</input>
                      </span>
                      <span class="title" style="color: rgb(204, 20, 20); background-color: rgba(204, 20, 20, 0.2);">
                        <input class="refresh-chart" type="checkbox" id="404s" checked>404s</input>
                      </span>
                      <span class="title" style="color: rgb(255, 127, 70); background-color: rgba(255, 127, 70, 0.2);">
                        <input class="refresh-chart" type="checkbox" id="redirects" checked>Redirects</input>
                      </span>
                      <span class="title" style="color: rgb(255, 51, 153); background-color: rgba(255, 51, 153, 0.2);">
                        <input class="refresh-chart" type="checkbox" id="other-errors" checked>Other Errors</input>
                      </span>
                      <span class="title" style="color: rgb(224, 224, 0); background-color: rgba(224, 224, 0, 0.2);">
                        <input class="refresh-chart" type="checkbox" id="missing-titles" checked>Missing Titles</input>
                      </span>
                      <span class="title" style="color: rgb(204, 51, 204); background-color: rgba(204, 51, 204, 0.2);">
                        <input class="refresh-chart" type="checkbox" id="missing-h1s" checked>Missing H1s</input>
                      </span>
                      <span class="title" style="color: rgb(204, 80, 0); background-color: rgba(204, 80, 0, 0.2);">
                        <input class="refresh-chart" type="checkbox" id="missing-meta-descriptions" checked>Missing Meta Descriptions</input>
                      </span>
                      <span class="title" style="color: rgb(204, 102, 102); background-color: rgba(204, 102, 102, 0.2);">
                        <input class="refresh-chart" type="checkbox" id="missing-image-alt" checked>Missing Image Alternate Text</input>
                      </span>
                    </div>

                    <?php
                    echo '<div class="chart-wrap"><canvas id="my-chart"></canvas></div>';
                    } else {
                      echo "<p>This section will include charts when at least two Reports have finished running. Click on <em>Overview</em> below to view results.</p>";
                    } // array_filter($jobs)
                  } // count($jobs)

                } // is_array($jobs) || $user

              } // !$options['apikey'] || !$options['job_id'] || !$options['schedule_id']
            ?>    
            <table class="widefat sc-table">
                <thead>
                  <tr>
                    <th>
                      Date                   
                    </th>
                    <th>
                      Status                      
                    </th>                    
                    <th>
                      Resources
                      <a href="#" title="Unique # of Resources downloaded. Click the count for a full report (URL, status code, content type, size, and duplicate content).">
                      <span data-code="f348" class="dashicons dashicons-info"></span></a>
                    </th>
                    <th>
                      Pages
                      <a href="#" title="Unique # of Pages downloaded. Click the count for a full report (URL, Title, Meta Description, H1, duplicate pages, ...).">                      
                      <span data-code="f348" class="dashicons dashicons-info"></span></a>
                    </th>
                    <th>
                      404s
                      <a href="#" title="# of Resources not found (404). For example, broken links and missing images. Click the count for a full report. Fix these!">
                      <span data-code="f348" class="dashicons dashicons-info"></span></a>                      
                    </th>    
                    <th>
                      Redirects
                      <a href="#" title="# of permanent (301) and temporary (302) Redirects. Eliminating redirects can improve user experience and increase search engine rankings. Click the count for a full report.">
                      <span data-code="f348" class="dashicons dashicons-info"></span></a>                                            
                    </th>        
                    <th>
                      Other Errors
                      <a href="#" title="# of Other Errors. For example, internal server errors (500) and forbidden (403). You will want to fix most of these. Click the count for a full report.">
                      <span data-code="f348" class="dashicons dashicons-info"></span></a>                      
                    </th>                  
                    <th>
                      Missing Titles
                      <a href="#" title="# of Pages missing Title. Titles are a key on-page SEO factor often used in search results. Definitely worth fixing. Click the count for a full report.">
                      <span data-code="f348" class="dashicons dashicons-info"></span></a>                                              
                    </th>
                    <th>
                      Missing H1s
                      <a href="#" title="# of Pages missing H1 heading. Include H1s, they're a key on-page SEO and usability factor. Click the count for a full report.">
                      <span data-code="f348" class="dashicons dashicons-info"></span></a>                                                                    
                    </th>          
                    <th>
                      Missing Meta Desc
                      <a href="#" title="# of Pages missing Meta Descriptions. A key SEO on-page factor commonly used in search results. Click the count for a full report.">
                      <span data-code="f348" class="dashicons dashicons-info"></span></a>                                                                                          
                    </th>                    
                    <th>
                      Missing Image Alt
                      <a href="#" title="# of Images missing Image Alternate Text. Having image alt texts helps with accessibility and ranking for image searches. Click the count for a full report.">
                      <span data-code="f348" class="dashicons dashicons-info"></span></a>                                                                                                                
                    </th>                              
                    <th>
                      Explore &amp; Visualize
                      <a href="#" title="Click Overview to access all your SiteCondor account features!">
                      <span data-code="f348" class="dashicons dashicons-info"></span></a>                                                                                                                                      
                    </th>                                        
                  </tr>
                </thead>
                <tbody>
                <?php
                  $labelsArr = $resourcesAllArr = $resourcesPagesArr = $resources404Arr = $resourcesRedirectsArr = $resourcesOtherErrorsArr = $titlesMissingArr = $headingsMissingArr = $metaDescriptionsMissingArr = $imagesMissingAltArr = array();
                  
                  foreach ( $jobs as $jobIndex => $job ) {

                    $created_on = substr( $job['createdAt'], 5, 2) . "/" . substr( $job['createdAt'], 8, 2 ) . "/" . substr( $job['createdAt'], 2, 2 );

                    if ( sc_is_job_ready_for_charting( $job ) ) {
                      // build reverse ordered labels & data array for chart (displays most recent results to the right)
                      array_unshift( $labelsArr, $created_on );
                      array_unshift( $resourcesAllArr, $job['resourcesAllCount'] );
                      array_unshift( $resourcesPagesArr, $job['resourcesPagesCount'] );
                      array_unshift( $resources404Arr, $job['resources404Count'] );
                      array_unshift( $resourcesRedirectsArr, $job['resourcesRedirectsCount'] );
                      array_unshift( $resourcesOtherErrorsArr, $job['resourcesOtherErrorsCount'] );
                      array_unshift( $titlesMissingArr, $job['titlesMissingCount'] );
                      array_unshift( $headingsMissingArr, $job['headingsMissingCount'] );
                      array_unshift( $metaDescriptionsMissingArr, $job['metaDescriptionsMissingCount'] );
                      array_unshift( $imagesMissingAltArr, $job['imagesMissingAltCount'] );
                    } // sc_is_job_ready_for_charting
                ?>
                  <tr id="<?php echo "job-index-" .  $jobIndex ?>" <?php echo ( $jobIndex % 2 == 0 ? 'class="alternate"' : '' ); ?>>
                    <td><?php echo $created_on; ?></td>
                    <td>

                      <?php 

                        echo ucfirst( $job['status'] ); 

                        switch($job['status']) {
                          case 'queued':
                            $tooltip = "Created but not started";
                            break;
                          case 'started':
                            $tooltip = "In progress";                          
                            break;
                          case 'finished':
                            $tooltip = "Completed successfully";                          
                            break;
                          case 'finished early':
                            $tooltip = "Out of credits - partially completed";                          
                            break;
                          case 'reached max resources':
                            $tooltip = "Partially completed - please consider upgrading your plan";                          
                            break;                                                                                                                
                          case 'failed':
                            $tooltip = "Please contact us for support";                          
                            break;                                                                                                                                            
                        }

                      ?>

                      <a href="#" class="inline-tooltip" title="<?php echo $tooltip ?>"><span data-code="f348" class="dashicons dashicons-info"></span></a>
                      
                    </td>
                    <td class="digit"><?php echo report_cell_markup( $jobs, 'resourcesAllCount', $jobIndex ); ?></td>          
                    <td class="digit"><?php echo report_cell_markup( $jobs, 'resourcesPagesCount', $jobIndex ); ?></td>          
                    <td class="digit"><?php echo report_cell_markup( $jobs, 'resources404Count', $jobIndex ); ?></td>          
                    <td class="digit"><?php echo report_cell_markup( $jobs, 'resourcesRedirectsCount', $jobIndex ); ?></td>          
                    <td class="digit"><?php echo report_cell_markup( $jobs, 'resourcesOtherErrorsCount', $jobIndex ); ?></td>
                    <td class="digit"><?php echo report_cell_markup( $jobs, 'titlesMissingCount', $jobIndex ); ?></td>
                    <td class="digit"><?php echo report_cell_markup( $jobs, 'headingsMissingCount', $jobIndex ); ?></td>
                    <td class="digit"><?php echo report_cell_markup( $jobs, 'metaDescriptionsMissingCount', $jobIndex ); ?></td>          
                    <td class="digit"><?php echo report_cell_markup( $jobs, 'imagesMissingAltCount', $jobIndex ); ?></td>          
                    <td class="last-cell"><a target="_blank" href="<?php echo sc_job_url( $job['_id'] ); ?>">Overview</a></td>
                  </tr>
                <?php } // foreach $jobs as $jobIndex => $job ?>
              </tbody>
            </table><!-- /.widefat -->
          </div><!-- /.inside -->
        </div><!-- /.postbox -->
      </div> <!-- /.meta-box-sortables.ui-sortable -->
    </div><!-- /.post-body-content -->

  </div><!-- /#post-body.metabox-holder.columns-1 -->
</div><!-- /#poststuff -->

<?php if ( is_array( $jobs ) && count( array_filter( $jobs, "sc_is_job_ready_for_charting" ) ) > 1 ) { ?>

  <script type="text/javascript">
    jQuery(function($) {   

      // refresh chart
      $.refreshChart = function() {

        var datasets = [];
        
        if($('#resources').is(':checked')) {
          datasets.push({ label: 'Resources', data: <?php echo json_encode( $resourcesAllArr ); ?> });
        }
        if($('#pages').is(':checked')) {
          datasets.push({ label: 'Pages', data: <?php echo json_encode( $resourcesPagesArr ); ?> });
        }
        if($('#404s').is(':checked')) {
          datasets.push({ label: '404s', data: <?php echo json_encode( $resources404Arr ); ?> }); 
        }
        if($('#redirects').is(':checked')) {
          datasets.push({ label: 'Redirects', data: <?php echo json_encode( $resourcesRedirectsArr ); ?> });          
        }        
        if($('#other-errors').is(':checked')) {
          datasets.push({ label: 'Other Errors', data: <?php echo json_encode( $resourcesOtherErrorsArr ); ?> });  
        }       
        if($('#missing-titles').is(':checked')) {
          datasets.push({ label: 'Missing Titles', data: <?php echo json_encode( $titlesMissingArr ); ?> });  
        }
        if($('#missing-h1s').is(':checked')) {
          datasets.push({ label: 'Missing H1s', data: <?php echo json_encode( $headingsMissingArr ); ?> });  
        }
        if($('#missing-meta-descriptions').is(':checked')) {
          datasets.push({ label: 'Missing Meta Descriptions', data: <?php echo json_encode( $metaDescriptionsMissingArr ); ?> });  
        }           
        if($('#missing-image-alt').is(':checked')) {
          datasets.push({ label: 'Missing Image Alternate Text', data: <?php echo json_encode( $imagesMissingAltArr ); ?> });   
        }                           

        $.chartJS(
          <?php echo json_encode( $labelsArr ); ?>, 
          datasets
        );
      }

      // refresh chart when one of the legend checkboxes changes status
      $('.refresh-chart').on('change', function(event) {
        $.refreshChart();
      });

      // refresh chart on load
      $.refreshChart();
    });

  </script>    

<?php } // count array_filter $jobs sc_is_job_ready_for_charting ?>