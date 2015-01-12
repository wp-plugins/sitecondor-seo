<?php function recommendation_info_markup( $recommendation, $index ) { ?>

    <a id="show-info-<?php echo $index; ?>"> 
      <?php
        if ( $recommendation['type'] == 'not-found-internal' || $recommendation['type'] == 'not-found-external' ) {
          $arr = $recommendation['info']['linkedFrom'];
          $count = $recommendation['info']['linkedFromCount'];
          echo 'Linked from ' . $count . ( $count > 1 ? ' Pages' : ' Page' );
        } else { // if($recommendation['type'] == 'missing-title' || $recommendation['type'] == 'missing-meta-description' || $recommendation['type'] == 'missing-h1' || $recommendation['type'] == 'image-missing-alt') {
          $arr = $recommendation['info']['urls'];
          $count = count( $recommendation['info']['urls'] );
          echo 'Show ' . $count . ( $count > 1 ? ' Pages' : ' Page' );
        }
      ?>
      <span class='dashicons dashicons-arrow-down-alt2'></span>
    </a>

    <a id="hide-info-<?php echo $index; ?>" class="hide">
      Close 
      <span class='dashicons dashicons-arrow-up-alt2'></span>
    </a>

    <ul id="info-<?php echo $index ?>" class="hide url-list">
      <?php foreach($arr as $urlIndex => $url) { ?>
        <li>
          <a href="<?php echo $url; ?>" target="_blank">
            <?php echo $url; ?>
          </a>
        </li>
      <?php } ?>
    </ul>     

    <script>
      jQuery(function($) { 
        var info = jQuery('#info-' + <?php echo $index; ?>);
        var showLink = jQuery('#show-info-' + <?php echo $index; ?>);
        var closeLink = jQuery('#hide-info-' + <?php echo $index; ?>);
        showLink.on('click', function(event) {
          closeLink.show();
          showLink.hide();
          info.show();
        });        
        closeLink.on('click', function(event) {
          showLink.show();
          closeLink.hide();
          info.hide();
        });
      });
    </script>

<?php } ?>

<div id="poststuff">
  <div id="post-body" class="metabox-holder columns-1">

    <!-- main content -->
    <div id="post-body-content">
      <div class="meta-box-sortables ui-sortable">
        <div class="postbox">          
          <h3><span>Recommendations for <em class="reports-domain"><?php echo preg_replace( "(https?://)", "", get_option( 'siteurl' ) ); ?></em></span></h3>
          <div class="inside">
            <?php
              $options = get_option( 'sitecondor_options' );

              if ( !$options['apikey'] || !$options['job_id'] || !$options['schedule_id'] ) {
                echo "<p>Your SiteCondor account isn't fully set up yet, please check your <a href='?page=sitecondor-seo&tab=settings'>Settings</a>.</p>";
              } else {
                $user = sc_get_user( $options['apikey'] );                
                $res = sc_get_jobs( $options['apikey'] );
                $jobs = $res['jobs'];
                
                if ( !is_array($jobs) || !$user ) {
                  echo "<p><strong>Sorry, we were unable to connect to SiteCondor. Please try again or contact support.</strong></p>";
                } else {

                  $successfully_completed_jobs = array_filter( $jobs, "sc_is_job_successfully_completed" );
                    
                  if ( $user['plan'] == 'wpfree' ) {                  
                  ?>

                    <div class="upgrade-bar">
                      <span>Upgrade and create Reports whenever you'd like.</span><a href="<?php echo sc_upgrade_url('recommendations'); ?>" target="_blank" class="button-secondary">Upgrade!</a>
                    </div><!-- /.upgrade-bar -->

                  <?php
                  }

                  if ( count( $successfully_completed_jobs ) < 1 ) {
                    echo "<p>Sorry, you don't have any recommendations yet.</p>";
                  } else {                  

                    $job = array_shift($successfully_completed_jobs);
                    $recommendations = sc_get_recommendations( $options['apikey'], $job['_id'] );
                    
                    if ( !$recommendations || count( $recommendations['list'] ) < 1 ) {
                      echo "<p>You don't have any Recommendations at this time.</p>";
                    } else {
                      echo "<p>The recommendations below are based on your report from <strong>" . substr( $job['createdAt'], 5, 2) . "/" . substr( $job['createdAt'], 8, 2 ) . "/" . substr( $job['createdAt'], 2, 2 ) . "</strong>.</p>";        
            
                    ?>

                <table class="widefat sc-table sc-recommendation-table">
                  <?php 
                    $current_priority = ''; 
                    foreach ( $recommendations['list'] as $index => $recommendation ) {
                  ?>
                    <!-- PRIORITY HEADINGS -->
                    <?php if ( $recommendation['priority'] != $current_priority ) { $current_priority = $recommendation['priority']; ?>
                      <tr id="priority-<?php echo $recommendation['priority']; ?>">
                        <th colspan="2"><?php echo strtoupper( $recommendation['priority'] ); ?> PRIORITY</th>
                      </tr>
                    <?php } ?>
                    <!-- RESULT ROWS -->                    
                    <tr <?php echo ( $index % 2 == 0 ? 'class="alternate"' : '' ); ?>>
                      <!-- NOT-FOUND-INTERNAL -->
                      <?php switch( $recommendation['type'] ) { case 'not-found-internal': ?>
                        <td>
                          <h5>
                            404 Not Found: Internal
                            <a class="inline-tooltip" href="#" title="Most visitors will leave your site when faced with a missing resource or 404 error. If the resource is located at a different address, update the pages to link to the correct address. If the resource doesn't exist, remove the links from the pages.">
                            <span data-code="f348" class="dashicons dashicons-info"></span></a>
                          </h5>
                          <?php echo $recommendation['info']['url']; ?>
                        </td>
                        <td>
                          <?php echo recommendation_info_markup( $recommendation, $index ); ?>
                        </td>                          
                      <?php break; case 'not-found-external': ?>
                        <td>
                          <h5>
                            404 Not Found: External
                            <a class="inline-tooltip" href="#" title="If the resource is located at a different address, update the pages to link to the correct address. If the resource doesn't exist, remove the links from the pages.">
                            <span data-code="f348" class="dashicons dashicons-info"></span></a>
                          </h5>
                          <?php echo $recommendation['info']['url']; ?>
                        </td>
                        <td>
                          <?php echo recommendation_info_markup( $recommendation, $index ); ?>
                        </td>
                        <?php break; case 'missing-title': ?>                      


                        <?php break; case 'missing-title': ?>
                          <td>
                            <h5>
                              Missing Titles
                              <a class="inline-tooltip" href="#" title="Titles are a key on-page SEO factor often used in search results. Make sure to add relevant and keyword-rich titles to these pages.">
                              <span data-code="f348" class="dashicons dashicons-info"></span></a>
                            </h5>
                          </td>
                          <td>
                            <?php echo recommendation_info_markup( $recommendation, $index ); ?>
                          </td>
                        <?php   
                          break; case 'missing-meta-description': ?>
                          <td>
                            <h5>
                              Missing Meta Description
                              <a class="inline-tooltip" href="#" title="Meta Descriptions are a key SEO on-page factor commonly used in search results. Make sure to add relevant and keyword-rich meta descriptions to these pages.">
                              <span data-code="f348" class="dashicons dashicons-info"></span></a>
                            </h5>
                          </td>
                          <td>
                            <?php echo recommendation_info_markup( $recommendation, $index ); ?>
                          </td>
                        <?php break; case 'redirect-internal-temporary': ?>
                          <td>
                            <h5>
                              302 Internal Redirect
                              <a class="inline-tooltip" href="#" title="Eliminating redirects can improve user experience. Link to the destination page in the most direct manner possible.">
                              <span data-code="f348" class="dashicons dashicons-info"></span></a>
                            </h5>
                            <?php echo $recommendation['info']['url']; ?>
                          </td>
                          <td>
                            <?php echo 'Redirected to: ' . $recommendation['info']['redirectedToUrl']; ?>
                          </td>
                        <?php break; case 'redirect-internal-permanent': ?>
                          <td>
                            <h5>
                              301 Internal Redirect
                              <a class="inline-tooltip" href="#" title="Eliminating redirects can improve user experience. Link to the destination page in the most direct manner possible.">
                              <span data-code="f348" class="dashicons dashicons-info"></span></a>
                            </h5>
                            <?php echo $recommendation['info']['url']; ?>
                          </td>
                          <td>
                            <?php echo 'Redirected to: ' . $recommendation['info']['redirectedToUrl']; ?>
                          </td>
                        <?php break; case 'redirect-external-temporary': ?>
                          <td>
                            <h5>
                              302 External Redirect
                              <a class="inline-tooltip" href="#" title="Eliminating redirects can improve user experience. Link to the destination page in the most direct manner possible.">
                              <span data-code="f348" class="dashicons dashicons-info"></span></a>
                            </h5>
                            <?php echo $recommendation['info']['url']; ?>
                          </td>
                          <td>
                            <?php echo 'Redirected to: ' . $recommendation['info']['redirectedToUrl']; ?>                                  
                          </td>
                        <?php break; case 'redirect-external-permanent': ?>
                          <td>
                            <h5>
                              301 External Redirect
                              <a class="inline-tooltip" href="#" title="Eliminating redirects can improve user experience. Link to the destination page in the most direct manner possible.">
                              <span data-code="f348" class="dashicons dashicons-info"></span></a>
                            </h5>
                            <?php echo $recommendation['info']['url']; ?>
                          </td>
                          <td>
                            <?php echo 'Redirected to: ' . $recommendation['info']['redirectedToUrl']; ?>                                  
                          </td>
                        <?php break; case 'other-error': ?>
                          <td>
                            <h5>
                              Status Code: <?php echo $recommendation['info']['statusCode']; ?>
                              <a class="inline-tooltip" href="#" title="Please review and fix as necessary.">
                              <span data-code="f348" class="dashicons dashicons-info"></span></a>
                            </h5>
                          </td>
                          <td>
                            <?php echo $recommendation['info']['url']; ?>
                          </td>
                        <?php break; case 'missing-h1': ?>
                          <td>
                            <h5>
                              Missing H1
                              <a class="inline-tooltip" href="#" title="Make sure to add relevant H1s to these pages, H1 headings are a key on-page SEO and usability factor.">
                              <span data-code="f348" class="dashicons dashicons-info"></span></a>
                            </h5>
                          </td>
                          <td>
                            <?php echo recommendation_info_markup( $recommendation, $index ); ?>
                          </td>
                        <?php break; case 'image-missing-alt': ?>
                          <td>
                            <h5>
                              Image Missing Alt Attribute
                              <a class="inline-tooltip" href="#" title="Having image alt text helps with accessibility and ranking for image searches. If this is an important image, make sure to add alternate text attributes when used in the pages below.">
                              <span data-code="f348" class="dashicons dashicons-info"></span></a>
                            </h5>  
                            <?php echo $recommendation['info']['src']; ?>
                          </td>
                          <td>
                            <?php echo recommendation_info_markup( $recommendation, $index ); ?>
                          </td>
                        <?php break; } // switch ?>
                    </tr>
                  <?php } // foreach ?>
                </table>
            <?php
                    } // !$recommendations || count($recommendations['list']) < 1

                  } // count($successfully_completed_jobs) < 1

                } // is_array($jobs)
                
              } // !$options['apikey'] || !$options['job_id'] || !$options['schedule_id']
            ?>    

          </div><!-- /.inside -->
        </div><!-- /.postbox -->
      </div> <!-- /.meta-box-sortables.ui-sortable -->
    </div><!-- /.post-body-content -->

  </div><!-- /#post-body.metabox-holder.columns-1 -->
</div><!-- /#poststuff -->