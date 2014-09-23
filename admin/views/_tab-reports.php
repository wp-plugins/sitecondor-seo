<?php
  function report_cell_markup( $job, $field_name, $job_index ) {

    $count = $job[$field_name];

    if ( $job_index > 0 || $count == 0 ) {
      return $count;
    } 

    $url = '';
    switch ( $field_name ) {
      case 'resourcesAllCount':      
        $url = sc_resources_all_url( $job['_id'] );
        break;
      case 'resourcesPagesCount':
        $url = sc_resources_pages_url( $job['_id'] );    
        break;
      case 'resources404Count':
        $url = sc_resources_404_url( $job['_id'] );        
        break;
      case 'resourcesRedirectsCount':
        $url = sc_resources_redirects_url( $job['_id'] );            
        break;
      case 'resourcesOtherErrorsCount':
        $url = sc_resources_other_errors_url( $job['_id'] );
        break;
      case 'titlesMissingCount':
        $url = sc_titles_missing_url( $job['_id'] );    
        break;
      case 'headingsMissingCount':
        $url = sc_headings_missing_url( $job['_id'] );
        break;
      case 'metaDescriptionsMissingCount':
        $url = sc_meta_descriptions_missing_url( $job['_id'] );    
        break;
      case 'imagesMissingAltCount':
        $url = sc_images_missing_url( $job['_id'] );
        break;
    }
    return "<a target='_blank' href='" . $url . "'>" . $count . "</a>";
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
                
                if ( !is_array($jobs) ) {
                  echo "<p><strong>Sorry, we were unable to connect to SiteCondor. Please try again or contact support.</strong></p>";
                } elseif ( count($jobs) < 1 ) {
                  echo "<p>Sorry, you don't have any Reports yet.</p>";
                } else {
                  if ( count( array_filter( $jobs, "sc_is_job_ready_for_charting" ) ) > 1 ) {
                    echo '<div id="legend"></div>';
                    echo '<div class="chart-wrap"><canvas id="my-chart"></canvas></div>';
                  } else {
                    echo "<p>This section will include charts when at least two weekly crawls have finished running. Click on <em>Overview</em> below to view results.</p>";
                  } // array_filter($jobs)
                } // is_array($jobs)

              } // !$options['apikey'] || !$options['job_id'] || !$options['schedule_id']
            ?>    
            <table class="widefat sc-table">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Resources</th>
                    <th>Pages</th>
                    <th>404s</th>          
                    <th>Redirects</th>        
                    <th>Other Errors</th>                  
                    <th>Missing Titles</th>
                    <th>Missing H1s</th>          
                    <th>Missing Meta Desc</th>                    
                    <th>Missing Image Alt</th>                              
                    <th>Explore &amp; Visualize</th>                                        
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
                    <td><?php echo ucfirst( $job['status'] ); ?></td>
                    <td class="digit"><?php echo report_cell_markup( $job, 'resourcesAllCount', $jobIndex ); ?></td>          
                    <td class="digit"><?php echo report_cell_markup( $job, 'resourcesPagesCount', $jobIndex ); ?></td>          
                    <td class="digit"><?php echo report_cell_markup( $job, 'resources404Count', $jobIndex ); ?></td>          
                    <td class="digit"><?php echo report_cell_markup( $job, 'resourcesRedirectsCount', $jobIndex ); ?></td>          
                    <td class="digit"><?php echo report_cell_markup( $job, 'resourcesOtherErrorsCount', $jobIndex ); ?></td>
                    <td class="digit"><?php echo report_cell_markup( $job, 'titlesMissingCount', $jobIndex ); ?></td>
                    <td class="digit"><?php echo report_cell_markup( $job, 'headingsMissingCount', $jobIndex ); ?></td>
                    <td class="digit"><?php echo report_cell_markup( $job, 'metaDescriptionsMissingCount', $jobIndex ); ?></td>          
                    <td class="digit"><?php echo report_cell_markup( $job, 'imagesMissingAltCount', $jobIndex ); ?></td>          
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
      $.chartJS(
        <?php echo json_encode( $labelsArr ); ?>, 
        [
          { label: 'Resources', data: <?php echo json_encode( $resourcesAllArr ); ?> }, 
          { label: 'Pages', data: <?php echo json_encode( $resourcesPagesArr ); ?> },
          { label: '404s', data: <?php echo json_encode( $resources404Arr ); ?> },
          { label: 'Redirects', data: <?php echo json_encode( $resourcesRedirectsArr ); ?> },
          { label: 'Other Errors', data: <?php echo json_encode( $resourcesOtherErrorsArr ); ?> },
          { label: 'Missing Titles', data: <?php echo json_encode( $titlesMissingArr ); ?> },
          { label: 'Missing H1s', data: <?php echo json_encode( $headingsMissingArr ); ?> },
          { label: 'Missing Meta Descriptions', data: <?php echo json_encode( $metaDescriptionsMissingArr ); ?> },
          { label: 'Missing Image Alternate Text', data: <?php echo json_encode( $imagesMissingAltArr ); ?> }
        ]
      );
    });
  </script>    

<?php } // count array_filter $jobs sc_is_job_ready_for_charting ?>