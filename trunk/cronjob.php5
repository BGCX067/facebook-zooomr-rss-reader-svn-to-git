<?php

//define('MAGPIE_CACHE_DIR', './magpie_cache');
define('MAGPIE_CACHE_ON', 0);
//define('MAGPIE_CACHE_AGE', 600);

include('conf.php5');

require_once 'facebook.php5';
require_once('rss/rss_fetch.inc');
require_once('database.php5');
require_once('zooomr_image.php5');
require_once('layouts/layout_functions.php5');
        
include('layouts/profile_layouts/normal.php5');
include('layouts/profile_layouts/block.php5');

// create a log file
$myFile = "cronjob.log";
$fh = fopen($myFile, 'w');

$facebook = new Facebook($appapikey, $appsecret);

$facebook->api_client->session_key = $infinite_session_key;

// create a db conectoin
$database_connection = new database();

$all_users = $database_connection->get_all_users();

$profile_fbml = "";

$num_rows = mysql_num_rows($all_users);
$count = 0;

// set that user
$facebook->set_user('741028155', $infinite_session_key);        

while ($row = mysql_fetch_row($all_users))
{
	$fb_id = $row[0];
	$zooomr_id = $row[1];
  $layout_id=$row[2];
  	
	fwrite($fh, "Updating user: " . $fb_id . " with zooomr ID " . $zooomr_id . " using layout " . $layout_id ."\n");
	
	$rss = @fetch_rss("http://www.zooomr.com/services/feeds/public_photos/?id=$zooomr_id&format=rss_200");
	
  fwrite($fh, "Got RSS\n");

	if (0 != count($rss->items))
  {
    $image_array = array();

 		fwrite($fh, "Creating image set\n");

    foreach ($rss->items as $item)
    {
      // create an image
      $image = new zooomr_image($item['description'] . "<br />" . $item['title']);
      array_push($image_array, $image);

 			fwrite($fh, "Created an image...\n");

    }

 		fwrite($fh, "Created image set\n");

		$layout;  

    switch ($layout_id)
    {
      case 1:
        $layout       = new normal($image_array);
 				fwrite($fh, "Loaded normal profile\n");
        break;
      case 2:
        $layout       = new block($image_array);
 				fwrite($fh, "Loaded block profile\n");
        break;
      default:
        $layout       = new normal($image_array); 		
				fwrite($fh, "Loaded normal profile\n");
        break;
    }

		// make the profile html
    {
			fwrite($fh, "Drawing profile\n");
      $profile_fbml = $layout->draw_profile($fb_id);
			fwrite($fh, "Drawn profile\n");
    }	                                   
	}
	else
	{
		$profile_fbml = "Unknown Zooomr ID '" . $zooomr_id . "'<br> Maybe you need some <a href='http://apps.facebook.com/zooomr_rss_reader/help.php5'>help?</a>";
	}


  $return_code;
  
  try
  {
  	//if ($facebook->api_client->users_isAppAdded())
  	//{	
			// Now you can update FBML pages, update your fb:ref tags, etc.
			//$return_code = $facebook->api_client->profile_setFBML($profile_fbml, $fb_id);
			$return_code = $facebook->api_client->profile_setFBML(null, $fb_id, $profile_fbml);

      $temp_code = print_r($return_code, TRUE);
			//$return_code = $facebook->api_client->profile_setFBML(, microtime(true), 'profile FBML here', 'profile action fbml here', 'mobile fbml here');

		//}
		//else
		//{
		//	fwrite($fh, "user " . $fb_id . " has removed the app\n");
			fwrite($fh, "return code is " . $return_code . "\n");
			fwrite($fh, "return code is " . $temp_code . "\n");
		//}
	}
	catch (Exception $ex)
	{
		die("exception code for user " . $fb_id . " was " . $ex . "\n");
	}
	$count++;
  
  fwrite($fh, "completed " . $count . " of " . $num_rows . " users " . $return_code . "\n");
}

fwrite($fh, "done\n");

fclose($fh);
?>
