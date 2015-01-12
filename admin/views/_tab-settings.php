<div id="poststuff">
  <div id="post-body" class="metabox-holder columns-2">

    <!-- main content -->
    <div id="post-body-content">
      <div class="meta-box-sortables ui-sortable">
        <div class="postbox"> 
          <h3><span>SiteCondor Account Settings</span></h3>
            <div class="inside">
              <?php
                $options = get_option('sitecondor_options');

                $terms_blurb = "<p><em>By creating your account you agree to SiteCondor's <a href='https://sitecondor.com/terms' target='_blank'>Terms</a><br> and understand that your email, password and site URL will be sent.</em></p>";
                if ( !$options['apikey'] ) {
                  // we don't have a user
                  echo '<form method="post" action="options.php">';
                  settings_fields( 'sitecondor_options_group' );            
                  do_settings_sections( 'sitecondor' );
                  echo $terms_blurb;
                  submit_button( 'Create Account' ); 
                  echo '</form>';
                } elseif( !$options['job_id'] ) {
                  // we got a user but no job
                  echo '<p>Please click below to complete your account setup:</p>';
                  echo '<form method="post" action="options.php">';
                  settings_fields( 'sitecondor_options_group' );                  
                  submit_button( 'Create Job' ); 
                  echo '</form>';
                } elseif( !$options['schedule_id'] ) {
                  // we got a user & job but no schedule
                  echo '<p>Please click below to complete your account setup:</p>';                  
                  echo '<form method="post" action="options.php">';
                  settings_fields( 'sitecondor_options_group' );                  
                  submit_button( 'Create Schedule' ); 
                  echo '</form>';
                } else { // we got a user, a job, and a schedule, get the user data from API
                  $user = sc_get_user($options['apikey']);
                  if(!$user) {
                    echo "<p><strong>Sorry, we were unable to connect to SiteCondor. Please try again or contact support.</strong></p>";
                  } else {
                    ?>
                    <table class="form-table sc-table sc-settings-table">
                      <tr>
                        <th>Email</th>
                        <td><?php echo $options['email']; ?></td>
                      </tr> 
                      <tr class="alternate">
                        <th>Plan</th>
                        <td><?php 
                          echo strtoupper($user['plan']);
                          if($user['plan'] == 'wpfree') {
                            ?>
                              - <strong>
                                <a target="_blank" href="<?php echo sc_upgrade_url('settings'); ?>">Upgrade</a>
                                <a class="inline-tooltip" title="Get more Credits and create Reports anytime." href="#">
                                <span data-code="f348" class="dashicons dashicons-info"></span>
                                </a>
                              </strong>
                            <?php
                          }
                        ?>
                        </td>
                      </tr>
                      <tr>
                        <th>Report Credits Left</th>
                        <td><?php echo number_format($user['crawlsLeft']); ?></td>
                      </tr>                      
                      <tr class="alternate">
                        <th>URL Credits Left</th>
                        <td><?php echo number_format($user['resourcesLeft']); ?></td>
                      </tr>                      
                      <tr>
                        <th>Account</th>
                        <td>
                          <a target="_blank" href="<?php echo sc_account_url(); ?>">Settings &amp; Plan</a>
                        </td>
                      </tr>   
                      <tr class="alternate">
                        <th>Scheduling</th>
                        <td><a target="_blank" href="<?php echo sc_schedules_url(); ?>">Settings</a></td>
                      </tr>  
                      <tr>
                        <th>Crawl Settings</th>
                        <td><a target="_blank" href="<?php echo sc_job_url($options['job_id']); ?>">Overview</a></td>
                      </tr>       
                      <tr class="alternate">
                        <th>Password</th>
                        <td><a target="_blank" href="<?php echo sc_reset_password_url(); ?>">Reset</a></td>
                      </tr>
                    </table>
                  <?php } 
                    } // we got a user, a job, and a schedule ?>
          </div><!-- /.inside -->
        </div><!-- /.postbox -->
      </div> <!-- /.meta-box-sortables.ui-sortable -->
    </div><!-- /.post-body-content -->

    <!-- sidebar -->
    <div id="postbox-container-1" class="postbox-container">
      <div class="meta-box-sortables">    
        <div class="postbox">
        <h3>SiteCondor Quicklinks</h3>
          <div class="inside">
            <ul>
              <li><a href="https://www.sitecondor.com/about/" target="_blank">About</a></li>
              <li><a href="https://www.sitecondor.com/contact/" target="_blank">Contact</a></li>
            </ul>
          </div><!-- /.inside -->
        </div><!-- /.postbox -->
      </div><!-- /.meta-box-sortables -->
    </div><!-- /#postbox-container-1.postbox-container -->    

  </div><!-- /#post-body.metabox-holder.columns-2 -->
</div><!-- /#poststuff -->