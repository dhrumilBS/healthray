<!-- CF7 to any API Documentatiom -->
<div class="wrap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" >
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
    <div class="cf7anyapi_doc">
        <h1 class="wp-heading-inline"><?php esc_html_e( 'CF7 To Any API Documentation', 'contact-form-to-any-api' ); ?></h1>
        <h2 class="screen-reader-text"><?php esc_html_e( 'CF7 To Any API Documentation ', 'contact-form-to-any-api' ); ?></h2>
        <div class="row">
            <div class="col-xl-2 col-lg-3 col-md-3 col-12 tab column-tab-nav">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active tab-index-1" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">
                        <div class="tab-index"></div>
                        <?php esc_html_e( 'How to configure', 'contact-form-to-any-api' ); ?>
                    </a>
                    <a class="nav-link tab-index-2" id="v-pills-video-tab" data-toggle="pill" href="#v-pills-video" role="tab" aria-controls="v-pills-video" aria-selected="false">
                        <div class="tab-index"></div>
                        <?php esc_html_e( 'Video for configuration', 'contact-form-to-any-api' ); ?>
                    </a>
                    <a class="nav-link tab-index-3" id="v-pills-logs-tab" data-toggle="pill" href="#v-pills-logs" role="tab" aria-controls="v-pills-logs" aria-selected="false">
                        <div class="tab-index"></div>
                        <?php esc_html_e( 'Logs', 'contact-form-to-any-api' ); ?>
                    </a>
                    <a class="nav-link tab-index-4" id="v-pills-entries-tab" data-toggle="pill" href="#v-pills-entries" role="tab" aria-controls="v-pills-entries" aria-selected="false">
                        <div class="tab-index"></div>
                        <?php esc_html_e( 'Entries', 'contact-form-to-any-api' ); ?>
                    </a>
                    <a class="nav-link tab-index-5" id="v-pills-json-format-tab" data-toggle="pill" href="#v-pills-json-format" role="tab" aria-controls="v-pills-json-format" aria-selected="false">
                        <div class="tab-index"></div>
                        <?php esc_html_e( 'Supported JSON Format', 'contact-form-to-any-api' ); ?>
                    </a>
                    <a class="nav-link tab-index-6" id="v-pills-pre-defined-tags-tab" data-toggle="pill" href="#v-pills-pre-defined-tags" role="tab" aria-controls="v-pills-pre-defined-tags" aria-selected="false">
                        <div class="tab-index"></div>
                        <?php esc_html_e( 'Pre Defined Tags', 'contact-form-to-any-api' ); ?>
                    </a>
                    <a class="nav-link tab-index-7" id="v-pills-cf7-hidden-field-tab" data-toggle="pill" href="#v-pills-cf7-hidden-field" role="tab" aria-controls="v-pills-cf7-hidden-field" aria-selected="false">
                        <div class="tab-index"></div>
                        <?php esc_html_e( 'CF7 Hidden Fields', 'contact-form-to-any-api' ); ?>
                    </a>
                    <a class="nav-link tab-index-8" id="v-pills-pro-tab" data-toggle="pill" href="#v-pills-pro" role="tab" aria-controls="v-pills-pro" aria-selected="false">
                        <div class="tab-index"></div>
                        <?php esc_html_e( 'Contact form to any API PRO', 'contact-form-to-any-api' ); ?>
                    </a>
                    <a class="nav-link tab-index-9" id="v-pills-oauth-tab" data-toggle="pill" href="#v-pills-oauth" role="tab" aria-controls="v-pills-oauth" aria-selected="false">
                        <div class="tab-index"></div>
                        <?php esc_html_e( 'Oauth 2.0 API Integration', 'contact-form-to-any-api' ); ?>
                    </a>
                    <a class="nav-link tab-index-10" id="v-pills-contact-us-tab" data-toggle="pill" href="#v-pills-contact-us" role="tab" aria-controls="v-pills-contact-us" aria-selected="false">
                        <div class="tab-index"></div>
                        <?php esc_html_e( 'Contact Us', 'contact-form-to-any-api' ); ?>
                    </a>
                    <a class="nav-link tab-index-11" id="v-pills-other-plugins-tab" data-toggle="pill" href="#v-pills-other-plugins" role="tab" aria-controls="v-pills-other-plugins" aria-selected="false">
                        <div class="tab-index"></div>
                        <?php esc_html_e( 'Our Other Plugins', 'contact-form-to-any-api' ); ?>
                    </a>
                </div>
            </div>
            <div class="col-xl-10 col-lg-9 col-md-9 col-12 tab column-tab-content">
                <div class="tab-content" id="v-pills-tabContent">
                    <!-- cf7 API -->
                    <div class="tab-pane fade show active cf7anyapi_full_width" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                        <h5 class="tab-title"><?php esc_html_e( 'Guide to Adding a New CF7 API Integration', 'contact-form-to-any-api' ); ?></h5>
                        <p><?php esc_html_e( 'This guide walks you through every step in the correct order to send your CF7 submissions straight to HubSpot, Salesforce, Zoho, Google Sheets, and 150+ other platforms, directly from WordPress.', 'contact-form-to-any-api' ); ?></p>
                        <p><?php esc_html_e( 'No coding, no external tools, just pure integration.', 'contact-form-to-any-api' ); ?> </p>
                        <h5 class="text-left"><?php esc_html_e( 'Steps to Configure the API:', 'contact-form-to-any-api' ); ?></h5>
                        <ol>
                            <li>
                                <strong><?php esc_html_e( 'Add a New CF7 API', 'contact-form-to-any-api' ); ?></strong>
                                <ul>
                                    <li><?php echo wp_kses_post(__( 'Click on <strong>Add New CF7 API</strong>.', 'contact-form-to-any-api' )); ?></li>
                                    <li><?php echo wp_kses_post(__( 'Provide a suitable title for your API in the <strong>API Title</strong> field.', 'contact-form-to-any-api' )); ?></li>
                                </ul>
                            </li>
                            <li>
                                <strong><?php esc_html_e( 'Select the Form', 'contact-form-to-any-api' ); ?></strong>
                                <ul>
                                    <li><?php esc_html_e( 'Choose the Contact Form 7 form you want to connect with the API from the dropdown list.', 'contact-form-to-any-api' ); ?></li>
                                </ul>
                            </li>
                            <li>
                                <strong><?php esc_html_e( 'Enter the API URL', 'contact-form-to-any-api' ); ?></strong>
                                <ul>
                                    <li><?php echo wp_kses_post(__( 'Input the URL for your CRM or API in the <strong>API URL</strong> field.', 'contact-form-to-any-api' )); ?></li>
                                    <li>
                                        <?php esc_html_e( 'Example:', 'contact-form-to-any-api' ); ?> 
                                        <pre><?php esc_html_e( 'https://api.mailbluster.com/api/leads/', 'contact-form-to-any-api' ); ?></pre>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <strong><?php esc_html_e( 'Add Header Requests', 'contact-form-to-any-api' ); ?></strong>
                                <ul>
                                    <li><?php echo wp_kses_post(__( 'Include the necessary headers for the API in the <strong>Header Request</strong> field. ', 'contact-form-to-any-api' )); ?><?php esc_html_e( 'Examples:', 'contact-form-to-any-api' ); ?></li>
                                    <pre>
      Authorization: MY_API_KEY
      Authorization: Bearer xxxxxxx
      Authorization: Basic xxxxxx
      Content-Type: application/json</pre>
                                </ul>
                            </li>
                            <li>
                                <strong><?php esc_html_e( 'Authorization with Username and Password (Base64 Encoding)', 'contact-form-to-any-api' ); ?></strong>
                                <ul>
                                    <li>
                                        <div>
                                            <?php echo wp_kses( 
                                                __( 'If your API requires a username and password, convert them to <strong>Base64</strong> format. You can use an online Base64 converter to achieve this.', 'contact-form-to-any-api' ), 
                                                array( 'strong' => array() ) 
                                                ); ?>
                                        </div>
                                        <pre>  Authorization: Basic ' . base64_encode(YOUR_USERNAME . ':' . YOUR_PASSWORD)</pre>
                                    </li>
                                    <li>
                                        <?php esc_html_e( 'Add the converted string in the header:', 'contact-form-to-any-api' ); ?>
                                        <pre>
      Authorization: Basic c2FsdXRlLXZldGVyYW5zLWFwaSA6IDBjd1NURENTcE91MUNOQXFVRFFmajdN
      Content-Type: application/json</pre>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <strong><?php esc_html_e( 'Select Input Type', 'contact-form-to-any-api' ); ?></strong>
                                <ul>
                                    <li><?php echo wp_kses_post(__( 'Choose your input type: <strong>JSON</strong> or <strong>GET/POST</strong>.', 'contact-form-to-any-api' )); ?></li>
                                </ul>
                            </li>
                            <li>
                                <strong><?php esc_html_e( 'Select API Method', 'contact-form-to-any-api' ); ?></strong>
                                <ul>
                                    <li><?php echo wp_kses_post(__( 'Specify the HTTP method your API uses: <strong>POST</strong> or <strong>GET</strong>.', 'contact-form-to-any-api' )); ?></li>
                                </ul>
                            </li>
                            <li>
                                <strong><?php esc_html_e( 'Map Fields', 'contact-form-to-any-api' ); ?></strong>
                                <ul>
                                    <li><?php esc_html_e( 'Map the form fields to the corresponding API keys provided by your API documentation.', 'contact-form-to-any-api' ); ?></li>
                                </ul>
                            </li>
                            <li>
                                <strong><?php esc_html_e( 'Save Configuration', 'contact-form-to-any-api' ); ?></strong>
                                <ul>
                                    <li><?php esc_html_e( 'Click on', 'contact-form-to-any-api' ); ?> <strong><?php esc_html_e( 'Save', 'contact-form-to-any-api' ); ?></strong> <?php esc_html_e( 'to store your API configuration.', 'contact-form-to-any-api' ); ?></li>
                                </ul>
                            </li>
                        </ol>
                        <p><?php esc_html_e( 'Just follow along, and you will have your Contact Form 7 form sending data to your API exactly the way you need within a few minutes. ', 'contact-form-to-any-api' ); ?></p>
                    </div>
                    <!-- video tutorial -->
                    <div class="tab-pane fade cf7anyapi_full_width" id="v-pills-video" role="tabpanel" aria-labelledby="v-pills-video-tab">
                        <h5 class="tab-title"><?php esc_html_e( 'CF7 to any API video tutorial', 'contact-form-to-any-api' ); ?></h5>
                        <p><?php esc_html_e( 'Watch how fast you can connect Contact Form 7 to Zoho CRM, Odoo, ActiveCampaign, or even Notion. Within 2 minutes, add your API details, map your fields, send test data, and go live without writing a line of code.', 'contact-form-to-any-api' ); ?></p>

                        <div class="iframe-wrap embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/1K-JdXwDH_k" title="<?php esc_attr_e( 'YouTube video player', 'contact-form-to-any-api' ); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </div>
                    <!-- Logs -->
                    <!-- Logs -->
                    <div class="tab-pane fade cf7anyapi_full_width" id="v-pills-logs" role="tabpanel" aria-labelledby="v-pills-logs-tab">
                        <h5 class="tab-title"><?php esc_html_e( 'Logs', 'contact-form-to-any-api' ); ?></h5>
                        <p><?php esc_html_e( 'If you want to confirm what your form actually sent, the Logs tab shows every detail instantly. You can review each request, submission, and API response with complete clarity. Here is an overview:', 'contact-form-to-any-api' ); ?></p>
                        <ol>
                            <li><?php echo wp_kses(__( 'After submitting data, you can view your data in the Logs tab.', 'contact-form-to-any-api' ), array('b' => array()) ); ?></li>
                            <li><?php esc_html_e( 'You can see your API logs and their data that is submitted by the user.', 'contact-form-to-any-api' ); ?></li>
                            <li><?php echo wp_kses(__( 'You can see your <b>API response too</b>.', 'contact-form-to-any-api' ), array('b' => array() ) ); ?></li>
                            <p><?php esc_html_e( 'Example: ', 'contact-form-to-any-api' ); ?></p>
                            <img src="<?php echo esc_url( plugin_dir_url( __DIR__ ).'images/logs.png' ); ?>" alt="logs list" style="height:100%; width:100%;">
                        </ol>
                    </div>
                    <!-- entries -->
                    <div class="tab-pane fade" id="v-pills-entries" role="tabpanel" aria-labelledby="v-pills-entries-tab">
                        <h5 class="tab-title"><?php esc_html_e( 'Entries', 'contact-form-to-any-api' ); ?></h5>
                        <p><?php esc_html_e( 'Want a quick way to check what users submitted? The Entries tab saves every form entry in your dashboard. You can review, filter, or export in one click. Here is how it works:', 'contact-form-to-any-api' ); ?></p>
                        <ol>
                            <li><?php esc_html_e( 'Select the form, and its data will display.', 'contact-form-to-any-api' ); ?></li>
                            <li> <?php echo wp_kses(__( 'You can download your data in <b>CSV</b>, <b>Excel</b>, <b>PDF</b> and also you can <b>Print</b> your data. You can also print your data.', 'contact-form-to-any-api' ), array('b' => array() ) ); ?></li>
                            <p><?php esc_html_e( 'Example: ', 'contact-form-to-any-api' ); ?></p>
                            <img src="<?php echo esc_url( plugin_dir_url( __DIR__ ).'images/entries.png');?>" alt="entries list" style="height:100%; width:100%;">
                        </ol>
                    </div>
                    <!-- Supported JSON Format -->
                    <div class="tab-pane fade cf7anyapi_full_width" id="v-pills-json-format" role="tabpanel" aria-labelledby="v-pills-json-format-tab">
                        <h5 class="tab-title"><?php esc_html_e( 'Supported JSON format', 'contact-form-to-any-api' ); ?></h5>
                        <p><?php esc_html_e( 'With the free version, you can send straightforward JSON with simple key–value pairs.', 'contact-form-to-any-api' ); ?></p>
                        <p><?php esc_html_e( 'With the Pro version, you can send more structured JSON, including nested or multi-level data used by many CRMs and advanced APIs.', 'contact-form-to-any-api' ); ?></p>
                        <ol>
                            <li>
                                <b><?php esc_html_e( 'Supported JSON format by Free Version', 'contact-form-to-any-api' ); ?></b></br>
                                <pre>
      {
          Firstname : "your-first-name",
          Lastname  : "your-last-name",
          Email     : "your-email",
          Phone     : "your-phone"
      }         </pre>
                            </li>
                            <li>
                                <?php echo wp_kses(__( '<b>Nested JSON Format Required </b><a href="https://www.contactformtoapi.com/pricing/#pricing" class="cf7_to_any_api_doc_link" target="_blank"><strong>Pro Version</strong></a>', 'contact-form-to-any-api' ), array('b' => array(), 'a' => array('href' => array(), 'class' => array(), 'target' => array() ), 'strong' => array() ) ); ?></br>
                                <pre>
      {
          Firstname : "your-first-name",
          Lastname  : "your-last-name",
          Email     : "your-email",
          Phone     : { 
                        office-number   : "9898989898", 
                        helpline-number : "1800-125-125"
                       }
      }         </pre>
                                <h5 class="mt-5 mb-2"><?php echo wp_kses_post(__('<b>Your API has Nested or Multilevel format of JSON?</b>','contact-form-to-any-api'), array('b' => array())); ?></h5>
                                <h5><?php echo wp_kses(__('<b> Don\'t worry, our development team can customize our plugin as per your need.</b><p class="get_pro_version-btn"><a target="_blank" href="https://www.contactformtoapi.com/#contact_us">Click here to contact us</a></p>','contact-form-to-any-api'), array('b' => array(),'p' => array('class' => array()),'a' => array('href' => array(), 'target' => array()))); ?></h5>
                            </li>
                        </ol>
                    </div>
                    <!-- Pre Defined Tags -->
                    <div class="tab-pane fade cf7anyapi_full_width" id="v-pills-pre-defined-tags" role="tabpanel" aria-labelledby="v-pills-pre-defined-tags">
                        <h5 class="tab-title"><?php esc_html_e( 'Available Predefined Tags', 'contact-form-to-any-api' ); ?></h5>
                        <br>
                        <p>
                            <?php esc_html_e( 'These predefined tags can be used directly in your JSON payload. When the form is submitted, their values are automatically replaced with dynamic data (e.g., user IP, page URL, submission date, etc.). This ensures real-time data is passed without manual input.', 'contact-form-to-any-api' ); ?>
                        </p>
                        <ul>
                            <li><strong><?php esc_html_e( '[_user_ip]', 'contact-form-to-any-api' ); ?></strong> – <?php esc_html_e( 'Captures the visitor\'s IP address at the time of form submission.', 'contact-form-to-any-api' ); ?></li>
                            <li><strong><?php esc_html_e( '[_date]', 'contact-form-to-any-api' ); ?></strong> – <?php esc_html_e( 'Displays the submission date (based on your WordPress Settings → General → Date Format).', 'contact-form-to-any-api' ); ?></li>
                            <li><strong><?php esc_html_e( '[_time]', 'contact-form-to-any-api' ); ?></strong> – <?php esc_html_e( 'Displays the submission time (based on your WordPress Settings → General → Time Format).', 'contact-form-to-any-api' ); ?></li>
                            <li><strong><?php esc_html_e( '[_submitted_date_time]', 'contact-form-to-any-api' ); ?></strong> – <?php esc_html_e( 'Displays both date and time of submission (follows your WordPress Date & Time Settings).', 'contact-form-to-any-api' ); ?></li>
                            <li><strong><?php esc_html_e( '[_site_url]', 'contact-form-to-any-api' ); ?></strong> – <?php esc_html_e( 'The home URL of your website.', 'contact-form-to-any-api' ); ?></li>
                            <li><strong><?php esc_html_e( '[_submission_source_url]', 'contact-form-to-any-api' ); ?></strong> – <?php esc_html_e( 'The URL of the page where the form was submitted.', 'contact-form-to-any-api' ); ?></li>
                            <li><strong><?php esc_html_e( '[_post_id]', 'contact-form-to-any-api' ); ?></strong> – <?php esc_html_e( 'The ID of the current post or page where the form is placed.', 'contact-form-to-any-api' ); ?></li>
                            <li><strong><?php esc_html_e( '[_post_slug]', 'contact-form-to-any-api' ); ?></strong> – <?php esc_html_e( 'The slug (URL-friendly name) of the current post or page.', 'contact-form-to-any-api' ); ?></li>
                            <li><strong><?php esc_html_e( '[_post_title]', 'contact-form-to-any-api' ); ?></strong> – <?php esc_html_e( 'The title of the current post or page where the form is located.', 'contact-form-to-any-api' ); ?></li>
                            <li><strong><?php esc_html_e( '[_form_id]', 'contact-form-to-any-api' ); ?></strong> – <?php esc_html_e( 'The unique ID of the Contact Form 7 form.', 'contact-form-to-any-api' ); ?></li>
                            <li><strong><?php esc_html_e( '[_form_name]', 'contact-form-to-any-api' ); ?></strong> – <?php esc_html_e( 'The name/title of the Contact Form 7 form.', 'contact-form-to-any-api' ); ?></li>
                            <li><strong><?php esc_html_e( '[_http_referer]', 'contact-form-to-any-api' ); ?></strong> – <?php esc_html_e( 'The previous page URL that referred the user to the form page.', 'contact-form-to-any-api' ); ?></li>
                            <li><strong><?php esc_html_e( '[_browser_info]', 'contact-form-to-any-api' ); ?></strong> – <?php esc_html_e( 'Captures the user’s browser and operating system information (User Agent).', 'contact-form-to-any-api' ); ?></li>
                            <li><strong><?php esc_html_e( '[_server_name]', 'contact-form-to-any-api' ); ?></strong> – <?php esc_html_e( 'The server hostname where your WordPress website is hosted.', 'contact-form-to-any-api' ); ?></li>
                        </ul>
                    </div>
                    <!-- CF7 Hidden field -->
                    <div class="tab-pane fade cf7anyapi_full_width" id="v-pills-cf7-hidden-field" role="tabpanel" aria-labelledby="v-pills-cf7-hidden-field">
                        <h5 class="tab-title"><?php esc_html_e( 'How to use CF7 Hidden fields', 'contact-form-to-any-api' ); ?></h5>
                        <br>
                        <p><?php esc_html_e( 'Need to send data that users never fill in themselves, like tracking IDs or source values? Hidden fields let you pass this extra information to your API automatically. You can set them up in several simple ways, such as:', 'contact-form-to-any-api' ); ?></p>
                        <ul>
                            <li>
                                <p class="pro_tab_description"><?php esc_html_e( 'Hidden field without value: ', 'contact-form-to-any-api' ); ?><strong>[hidden tracking-id]</strong></p>
                            </li>
                            <li>
                                <p class="pro_tab_description"><?php esc_html_e( 'Hidden field with Default value: ', 'contact-form-to-any-api' ); ?><strong>[hidden tracking-id default "12345"]</strong></p>
                            </li>
                            <li>
                                <p class="pro_tab_description"><?php esc_html_e( 'Hidden field with fix/static value: ', 'contact-form-to-any-api' ); ?><strong>[hidden tracking-id "12345"]</strong></p>
                            </li>
                            <li>
                                <p class="pro_tab_description"><?php esc_html_e( 'Hidden field is important part whenver we want to send data to API. Many API has parameter that need to send with static value in that case we can create hidden field and put static value and simply Map Hidden field with API mapping Key', 'contact-form-to-any-api' ); ?></p>
                            </li>
                        </ul>
                    </div>
                    <!-- Pro Version -->
                    <div class="tab-pane fade cf7anyapi_full_width" id="v-pills-pro" role="tabpanel" aria-labelledby="v-pills-pro-tab">

                        <h5 class="pro_tab_title tab-title"><?php esc_html_e( 'Contact form to any API PRO', 'contact-form-to-any-api' ); ?></h5>
                        <p class="pro_tab_description"><?php esc_html_e( 'Still not convinced? Here is the list of features that shows how Contact Form to Any API is the best plugin to connect any contact form with your CRM or any other third party services.', 'contact-form-to-any-api' ); ?></p>
                        <h6 class=""><?php esc_html_e( 'Pro Version Features:', 'contact-form-to-any-api' ); ?></h6>
                        <ul class="pro_feature_list">
                            <li><?php esc_html_e( 'Support Multi Level or Any Format of JSON', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Send data to multiple API', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'File input supported – Uploaded file will convert into BASE64 and send to API', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Option to Choose when to send data to API – Before cf7 mail sent OR After mail sent', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Send attachments to any API', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Option to choose Numerical Fields / Integer Fields', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Compatible with Multiple files upload for contact form 7 Plugin', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Priority Support', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Paid plugin customization support', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Paid oauth 2.0 API Integration support', 'contact-form-to-any-api' ); ?></li>
                        </ul>
                        <h6 class=""><?php esc_html_e( 'Supported CRM/API:', 'contact-form-to-any-api' ); ?></h6>
                        <ul class="pro_crm_list">
                            <li><?php esc_html_e( 'Sage CRM', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Mail Chimp', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Zapier', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Odoo CRM/ERP System', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Mailbluster', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Lead Post API', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Virtuagym API', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Pilotsolution', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Clio Grow', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'OS Ticket', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Samdock CRM', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Mikrowisp', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Bats CRM', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'FRS Labs API', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Get Cobra by ArcaMax', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Network Worldfilia', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'One Page CRM', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'SingleOps', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'GorillaDesk API', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Hubspot', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Sembark API', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Superoffice CRM', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Flowdesk', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'JobAdder', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Unlatch CRM', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Mail2many', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Workato', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Jetbrains / Intellij Space API', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Fincenfetch', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Lead Docket', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Agendor API', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Lead IM Israel', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Personio', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Kala CRM Israel', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Twilio WhatsApp', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Pixxicrm', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'easybizy', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Brevo CRM', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Mailcoach CRM', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Pipeline CRM', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Fluent CRM', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'And many more', 'contact-form-to-any-api' ); ?></li>
                        </ul>
                        <p class="get_pro_version-btn text-center"><a href="https://www.contactformtoapi.com/pricing/#pricing" target="_blank"><?php esc_html_e( 'Get Pro Version', 'contact-form-to-any-api' ); ?></a></p>
                    </div>
                    <!-- Oauth 2 -->
                    <div class="tab-pane fade cf7anyapi_full_width" id="v-pills-oauth" role="tabpanel" aria-labelledby="v-pills-oauth-tab">
                        <h5 class="tab-title"><?php esc_html_e( 'OAuth 2.0 Customization', 'contact-form-to-any-api' ); ?></h5><br>
                        <p><?php esc_html_e( 'Struggling with APIs or CRMs that keep rejecting your requests?', 'contact-form-to-any-api' ); ?></p>
                        <p><?php esc_html_e( 'Platforms like Salesforce, HubSpot, Zoho CRM, Google APIs, and other enterprise systems often require secure, token-based access. For Pro users, we offer custom support through the OAuth 2.0 Add-On, which automates authorization flows and keeps tokens updated so your integrations remain stable.', 'contact-form-to-any-api' ); ?></p>
                        <h6><?php esc_html_e( 'Our OAuth 2.0 Customization Plan provides seamless authentication and integration solutions for your API. Here’s what we offer:' ); ?></h6>
                        <ul>
                            <li><?php esc_html_e( 'OAuth 2.0 Authentication & Integration for Any API.', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Dynamic Access and Refresh Tokens Update automatically based on expiration time.', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'JWT Token Integration with Any API.', 'contact-form-to-any-api' ); ?></li>
                        </ul>
                        <h6><?php esc_html_e( 'When You Should Use This Add-On', 'contact-form-to-any-api' ); ?></h6>
                        <p><?php esc_html_e( 'Choose this add-on if your API requires:', 'contact-form-to-any-api' ); ?></p>
                        <ul>
                            <li><?php esc_html_e( 'Access tokens that expire', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'Dynamic Access and Refresh Tokens Update automatically based on expiration time.', 'contact-form-to-any-api' ); ?></li>
                            <li><?php esc_html_e( 'JWT Token Integration with Any API.', 'contact-form-to-any-api' ); ?></li>
                        </ul>
                        <p><?php esc_html_e( 'Our team works with you directly to customize the integration so it fits your API environment perfectly.', 'contact-form-to-any-api' ); ?></p>

                        <h6><?php echo wp_kses(__( '<b>Note: </b>Please provide your API test or development account details to our development team at <a target="_blank" href="mailto:support@contactformtoapi.com"> support@contactformtoapi.com</a>. It will allow us to prepare a customized authorization code tailored to your API requirements.', 'contact-form-to-any-api' ), array('b' => array(), 'a' => array('href' => array(), 'target' => array() ) ) ); ?></h6>
                        <p class="get_pro_version-btn text-center"><a href="https://www.contactformtoapi.com/pricing/#oauth" target="_blank"><?php esc_html_e( 'Buy Now', 'contact-form-to-any-api' ); ?></a></p>
                    </div>
                    <!-- contact us -->
                    <div class="tab-pane fade cf7anyapi_full_width" id="v-pills-contact-us" role="tabpanel" aria-labelledby="v-pills-contact-us-tab">
                        <h5 class="tab-title"><?php esc_html_e( 'Contact Us', 'contact-form-to-any-api' ); ?></h5>
                        <br>
                        <p><?php esc_html_e( "If you're facing any issues during integration or are unsure about any step, we are happy to help you resolve them in minutes.", 'contact-form-to-any-api' ); ?></p>
                        <p><a target="_blank" href="https://www.contactformtoapi.com/#contact_us"><?php esc_html_e( 'Talk to Support', 'contact-form-to-any-api' ); ?></a></p>
                        <p><?php echo wp_kses(__( 'You can even email us your query at <b><a target="_blank" href="mailto:support@contactformtoapi.com">support@contactformtoapi.com</a></b>', 'contact-form-to-any-api' ), array('b' => array(), 'a' => array('href' => array() ) ) ); ?></p>
                    </div>
                    <!-- other plugins -->
                    <div class="tab-pane fade cf7anyapi_full_width" id="v-pills-other-plugins" role="tabpanel" aria-labelledby="v-pills-other-plugins-tab">
                        <h5 class="tab-title"><?php esc_html_e( 'Our Other Plugins', 'contact-form-to-any-api' ); ?></h5>
                        <br>
                        <div class="our-plugin-list">
                            <div class="our-plugin-card">
                                <div class="our-plugin-icon">
                                    <img src="<?php echo esc_url( plugin_dir_url( __DIR__ ) . 'images/contact-form-to-any-api-icon.svg' ); ?>" alt="<?php echo esc_attr__( 'Plugin Icon', 'contact-form-to-any-api' ); ?>">
                                </div>
                                <div class="our-plugin-details">
                                    <h4><?php echo esc_html__( 'Contact Form 7 to Any API PRO', 'contact-form-to-any-api' ); ?></h4>
                                    <ul>
                                        <li><?php echo esc_html__( 'Support Multi Level or Any Format of JSON', 'contact-form-to-any-api' ); ?></li>
                                        <li><?php echo esc_html__( 'Option to Choose when to send data to API – Before CF7 mail sent OR After mail sent', 'contact-form-to-any-api' ); ?></li>
                                        <li><?php echo esc_html__( 'Compatible with Multiple files upload for contact form 7 Plugin', 'contact-form-to-any-api' ); ?></li>
                                        <li><?php echo esc_html__( 'File input supported – Uploaded file will convert into BASE64 and send to API', 'contact-form-to-any-api' ); ?></li>
                                    </ul>
                                    <span class="our-plugin-badge"><?php echo esc_html__( 'PRO', 'contact-form-to-any-api' ); ?></span>
                                    <a target="_blank" href="<?php echo esc_url( 'https://www.contactformtoapi.com/pricing/' ); ?>">
                                    <?php echo esc_html__( 'Get Now', 'contact-form-to-any-api' ); ?>
                                    </a>
                                </div>
                            </div>
                            <div class="our-plugin-card">
                                <div class="our-plugin-icon">
                                    <img src="<?php echo esc_url( plugin_dir_url( __DIR__ ) . 'images/contact-form-to-any-api-icon.svg' ); ?>" alt="<?php echo esc_attr__( 'Plugin Icon', 'contact-form-to-any-api' ); ?>">
                                </div>
                                <div class="our-plugin-details">
                                    <h4><?php echo esc_html__( 'OAuth 2.0 Customization Addon for Contact Form 7 to Any API PRO', 'contact-form-to-any-api' ); ?></h4>
                                    <ul>
                                        <li><?php echo esc_html__( 'OAuth 2.0 Authentication & Integration for Any API.', 'contact-form-to-any-api' ); ?></li>
                                        <li><?php echo esc_html__( 'Dynamic Access and Refresh Tokens Update automatically based on expiration time.', 'contact-form-to-any-api' ); ?></li>
                                        <li><?php echo esc_html__( 'JWT Token Integration with Any API.', 'contact-form-to-any-api' ); ?></li>
                                        <li><?php echo esc_html__( 'Create a JSON file for each entry and upload it to an FTP server based on your required JSON payload or format.', 'contact-form-to-any-api' ); ?></li>
                                    </ul>
                                    <span class="our-plugin-badge"><?php echo esc_html__( 'PRO Addon', 'contact-form-to-any-api' ); ?></span>
                                    <a target="_blank" href="<?php echo esc_url( 'https://www.contactformtoapi.com/pricing/' ); ?>">
                                    <?php echo esc_html__( 'Get Now', 'contact-form-to-any-api' ); ?>
                                    </a>
                                </div>
                            </div>
                            <div class="our-plugin-card">
                                <div class="our-plugin-icon">
                                    <img src="<?php echo esc_url( plugin_dir_url( __DIR__ ) . 'images/wpform-to-any-api-icon.svg' ); ?>" alt="<?php echo esc_attr__( 'Plugin Icon', 'contact-form-to-any-api' ); ?>">
                                </div>
                                <div class="our-plugin-details">
                                    <h4><?php echo esc_html__( 'Connect WPForm to Any API', 'contact-form-to-any-api' ); ?></h4>
                                    <ul>
                                        <li><?php echo esc_html__( 'Send WPForm Leads to Remote API’s such as CRM and other External API using POST/GET', 'contact-form-to-any-api' ); ?></li>
                                        <li><?php echo esc_html__( 'Create unlimited connection with any API', 'contact-form-to-any-api' ); ?></li>
                                        <li><?php echo esc_html__( 'Supports Simple & Fixed Format of JSON', 'contact-form-to-any-api' ); ?></li>
                                        <li><?php echo esc_html__( 'API Logs Management with submitted data and API response', 'contact-form-to-any-api' ); ?></li>
                                    </ul>
                                    <span class="our-plugin-badge"><?php echo esc_html__( 'Free', 'contact-form-to-any-api' ); ?></span>
                                    <a target="_blank" href="<?php echo esc_url( 'https://wordpress.org/plugins/connect-wpform-to-any-api/' ); ?>">
                                    <?php echo esc_html__( 'Download Now', 'contact-form-to-any-api' ); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>