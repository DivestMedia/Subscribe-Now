<?php if (is_admin()) { // note the use of is_admin() to double check that this is happening in the admin
     $config = array(
         'slug' => "subscribe-now", // this is the slug of your plugin
         'proper_folder_name' => 'dm-subscribe-now', // this is the name of the folder your plugin lives in
         'api_url' => 'https://api.github.com/repos/ralphjesy12/Subscribe-Now', // the GitHub API url of your GitHub repo
         'raw_url' => 'https://github.com/ralphjesy12/Subscribe-Now/tree/Master', // the GitHub raw url of your GitHub repo
         'github_url' => 'https://github.com/ralphjesy12/Subscribe-Now', // the GitHub url of your GitHub repo
         'zip_url' => 'https://github.com/ralphjesy12/Subscribe-Now/archive/Master.zip', // the zip url of the GitHub repo
         'sslverify' => true, // whether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
         'requires' => '3.0', // which version of WordPress does your plugin require?
         'tested' => '4.0.0', // which version of WordPress is your plugin tested up to?
         'readme' => 'README.md', // which file to use as the readme for the version number
         'access_token' => '', // Access private repositories by authorizing under Appearance > GitHub Updates when this example plugin is installed
     );
     new WP_GitHub_Updater($config);
 }
