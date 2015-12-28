<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

define('MAGPIE_CACHE_DIR', './magpie_cache');
define('MAGPIE_CACHE_ON', 1);
define('MAGPIE_CACHE_AGE', 600);

require_once('appinclude.php5');
require_once('rss/rss_fetch.inc');
require_once('database.php5');
require_once('zooomr_image.php5');
require_once('layouts/layout_functions.php5');

include('layouts/profile_layouts/normal.php5');
include('layouts/profile_layouts/block.php5');

// create a db conection
$database_connection = new database();

// check to see if we've been posted the correct info
if ($_POST['zooomr_id'] and $_POST['facebook_id'] and $_POST['layout'])
{
  // create or update the user
  $database_connection->insert_or_update_id($_POST['facebook_id'], $_POST['zooomr_id'], $_POST['layout']);
}

$page_viewer = $user;

//echo "page viewer is: $page_viewer<br/>";
//echo "page viewer is: " . $_GET['facebook_id'] . "<br/>";

if ($_GET['facebook_id'])
{
  $page_viewer = $_GET['facebook_id'];

  //echo "page viewer is: $page_viewer<br/>";
}

// attempt to get the zooomr ID from the database
//$zooomr_id = $database_connection->get_user_id($user);
//$layout_id = $database_connection->get_user_layout($user);
$zooomr_id = $database_connection->get_user_id($page_viewer);
$layout_id = $database_connection->get_user_layout($page_viewer);

// show an error by default
$app_error_string = 'You have not yet setup a Zooomr user ID<br /><a href="http://apps.facebook.com/zooomr_rss_reader">Best set one up</a>';
//$full_page_fbml = show_app_error		($app_error_string . draw_form($user));
$full_page_fbml = show_app_error		($app_error_string . draw_form($page_viewer));
$profile_fbml 	= show_profile_error($app_error_string);


if (NULL != $zooomr_id)
{
	// create an rss object
  $rss = @fetch_rss("http://www.zooomr.com/services/feeds/public_photos/?id=$zooomr_id&format=rss_200");
	                    
  if (0 != count($rss->items))
  { 
		$image_array = array();

		foreach ($rss->items as $item)
		{
	  	// create an image
	   	$image = new zooomr_image($item['description'] . "<br />" . $item['title']);
	    array_push($image_array, $image);
	  }

		$layout;

		switch ($layout_id) 
		{	
			case 1:
				$layout       = new normal($image_array);
      	break;
    	case 2:
				$layout       = new block($image_array);
      	break;
    	default:
				$layout       = new normal($image_array);
      	break;
  	}
	  
	  // make the profile html
	  {
     	$profile_fbml = $layout->draw_profile($page_viewer);
    }
    
    // make the app page HTML
	  {
 			$full_page_fbml = '<fb:header>Zooomr RSS Feed Reader: <a href="http://www.zooomr.com/photos/' . $zooomr_id . '">' . $rss->channel['title'] . '</a></fb:header><div style="margin:0 10px 0 10px;">';
     	//$full_page_fbml .= $layout->draw_app($user) . '</div>';

      if ($user == $page_viewer)
      {
     	  $full_page_fbml .= $layout->draw_app($page_viewer, 0) . '</div>';
      }
      else
      {
        $full_page_fbml .= $layout->draw_app($page_viewer, 1) . '</div>';
      }
    }
  }
  else
  {
  	$full_page_fbml = show_app_error		("Unknown Zooomr ID " . $zooomr_id .".  Maybe you need some <a href='http://apps.facebook.com/zooomr_rss_reader/help.php5'>help?</a>");
    if ($user == $page_viewer)
    {
		  $full_page_fbml.= draw_form($user);
		}
    $profile_fbml 	=	show_profile_error("Unknown Zooomr ID " . $zooomr_id . ".  Maybe you need some <a href='http://apps.facebook.com/zooomr_rss_reader/help.php5'>help?</a>");
  }  
}

if ($user == $page_viewer)
{
  // write the data to the profile
  //$facebook->api_client->profile_setFBML($profile_fbml, $user);
  $return_code = $facebook->api_client->profile_setFBML(null, $user, $profile_fbml, $profile_fbml, $profile_fbml);

  echo $return_code . "<br/>";
}
// write the form for configuring stuff
//include('form.inc');

// and echo it out for when this page is called direcly
echo $full_page_fbml;

?>
