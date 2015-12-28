<?php

class painter
{
  var $m_rss_object;

  function __construct($a_rss_object)
  {
    $this->m_rss_object = $a_rss_object;
  }
  
  function create_form_html($a_fb_user_id)
  {
            
  	$form_html = '<p><form action="index.php5" method="post">';
    $form_html .= 'Change Zooomr User ID <input type="text" name="zooomr_id">&nbsp;';
    $form_html .= '<input type="hidden" name="facebook_id" value="' . $a_fb_user_id . '">';
    $form_html .= '<input type="Submit" value="Get Me Photos!">';
    $form_html .= '</form></p>';
    
    $form_html .= '<p><a href="http://apps.facebook.com/zooomr_rss_reader/help.php5">Get some Help</a> | <a href="http://www.facebook.com/apps/application.php?id=6037801363">Share your thoughts</a></p>';
    $form_html .= '<hr/>';
    
    return $form_html;
  }
  
  function show_profile_error($a_error_message)
  {
  	$error_html = '<p>' . $a_error_message . '</p>';
            
    $full_page =  $error_html;
                
    return $full_page;
  }
                      
                      
  function show_feed_error($a_error_message, $a_user_id)
  {
  	$error_html = '<p>' . $a_error_message . '</p>';
  	$title_html = '<fb:header>Zooomr RSS Feed Reader</fb:header><div style="margin:0 10px 0 10px;">';
  	
  	$form_html = $this->create_form_html($a_user_id);
  	
  	$full_page = $title_html . $error_html . $form_html . '</div>';
  	                
  	return $full_page;                    
  }
  
  function create_image_html($a_item, $a_with_descriptions)
  {
    $fbml = "";
    $fbml .= '<div style="border-bottom: 2px solid #CCCCCC; padding-bottom:5px;">';
    $fbml .= '  <br>';
    $fbml .= '  <div style="border-bottom: 0px dotted #CCCCCC; border-top: 0px dotted #CCCCCC;">';
    $fbml .= '    <table border="0" width="100%" style="margin: 5px 5px 5px 5px;">';
    $fbml .= '      <tr>';
    $fbml .= '        <td valign="top" width="80%">';
    $fbml .= '          <a href="'.$a_item['link'].'" style="font-weight: bold; font-family:arial; font-size: 1.5em;">'.$a_item['title'].'</a>';
    $fbml .= '        </td>';
    $fbml .= '        <td valign="top" width="80%">';
    
    if (1 == $a_with_descriptions)
    {
    	$fbml .= '          <fb:share-button class="meta">';
  		$fbml .= '         <meta name="medium" content="blog" />';
  		$fbml .= '	 <meta name="title" content="'.htmlspecialchars(strip_tags($a_item['title'])).'" />';
  		$fbml .= '	 <meta name="description" content="'.htmlspecialchars(strip_tags($a_item['description'])).'" />';
  		$fbml .= '	 <link rel="target_url" href="'.$a_item['link'].'" />';
  		$fbml .= '	 <link rel="image_src" href="'. $this->get_image($a_item['description']) . '" />';
	  	$fbml .= '    </fb:share-button>';
	  }
	  
		$fbml .= '  </td>';  
	  $fbml .= '</tr>';
    $fbml .= '</table>';
    $fbml .= '</div>';
        
  //  if($a_item['description']) $fbml .= $a_item['description'];
    $fbml .= $this->parse_description($a_item['description'], $a_with_descriptions);
    $fbml .= '</div>';

    return $fbml;
  }
  
  function get_image($a_description)
  {
  	$a_desc_parts = preg_split("/\<br \/\>/", $a_description);

 
    preg_match("/\<img src=\"(.*?)\"/", $a_desc_parts[1], $temp_parts);      
    
    $link_length = strlen($temp_parts[0]);
    $image_link = substr($temp_parts[0], 10, ($link_length - 11));
     
//    $myFile = "testFile.txt";
 //   $fh = @fopen($myFile, 'a');
 //   fwrite($fh, $image_link);
  //  fclose($fh);
    
    //return $a_desc_parts[1];
    return $image_link;
  }
  
  function parse_description($a_description, $a_with_descriptions)
  {
  	$a_desc_parts = preg_split("/\<br \/\>/", $a_description);
  	
  	$image = $a_desc_parts[1];
  	
  	$desc  =  $a_desc_parts[2];
  	
  	if (1 == $a_with_descriptions)
  	{
  		$image_html = "<table border='0' width='100%'><tr><td>$image</td><td style='font-family:arial; font-size:1.1em;'>$desc</td></tr></table>";
  	}
  	else
  	{
  		$image_html = $image;
  	}
  	return $image_html;
  }

  function create_header_html($a_rss_object)
  { 
  	$fbml .= '<table border="0" width="100%" style="margin: 5px 5px 5px 5px;">';
  	$fbml .= '<tr><td valign="top" width="80%">';  
  	$fbml .= '<a href="'.$a_rss_object->channel['link'].'" style="font-weight:bold;">';
  	$fbml .= $a_rss_object->channel['title'];
  	$fbml .= '</a></td><td valign="top" width="80%">';
  	$fbml .= '<fb:share-button class="meta">';
  	$fbml .= '<meta name="medium" content="blog"/>';
  	$fbml .= '<meta name="title" content="' . htmlspecialchars(strip_tags($a_rss_object->channel['title'])) . '"/>';
  	$fbml .= '<meta name="description" content="' . htmlspecialchars(strip_tags($a_rss_object->channel['description'])) . '"/>';
  	$fbml .= '<link rel="target_url" href="' . $a_rss_object->channel['link'] . '"/>';
  	$fbml .= '</fb:share-button></td></tr></table>';
  	          
  	return $fbml;
  }
  
  function draw_feed($a_fb_user_id)
  {
  	$rss_object = $this->m_rss_object->items;

    $title_html = '<fb:header>Zooomr RSS Feed Reader</fb:header><div style="margin:0 10px 0 10px;">'; 
  	
  	$header_html = $this->create_header_html($this->m_rss_object);
  	
  	$image_html = $this->get_images($rss_object, 20, 1);
  	
  	$form_html = $this->create_form_html($a_fb_user_id);
  	
  	$full_page = $title_html . $form_html . $header_html . $image_html . '</div>';
  	
  	return $full_page;
  }
  
  function get_images($a_rss_object, $a_number_of_images, $a_with_descriptions)
  {
    $image_html = "";
  	
  	for ($i = 0 ; $i < $a_number_of_images ; $i++)
  	{
  		$item = $a_rss_object[$i];
  		
  		$image_html .= $this->create_image_html($item, $a_with_descriptions);
  	}
  	
  	return $image_html;
  }
  
  function draw_profile()
  {
  	$rss_object = $this->m_rss_object->items;
  
    $header_html = $this->create_header_html($this->m_rss_object);
      
    $image_html = $this->get_images($rss_object, 4, 0);
          
    $full_page = $header_html . $image_html . '</div>';
              
    return $full_page;              
  }
}

?>
