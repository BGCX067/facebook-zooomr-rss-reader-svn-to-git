<?php

class zooomr_image
{
	var $m_rss_fragment;
	
	var $m_descripiton;
	var $m_image;
	var $m_user;
	var $m_title;
	var $m_link;
		
	// constructor
	function __construct($a_rss_fragment)
	{
		// break it up into the pieces
		$parts = preg_split("/\<br \/\>/", $a_rss_fragment);
		
		/////////////////////////////////////////////////////
		// extract the image
		//
		preg_match("/\<img src=\"(.*?)\"/", $parts[1], $image_parts);
		
		// get the length
		$link_length 	= strlen($image_parts[0]);
		
		// chop off the first 10 chars and the last 1
		$this->m_image	= substr($image_parts[0], 10, ($link_length - 11));
	
		/////////////////////////////////////////////////////
		// extract the image link
		//
		preg_match("/\<a href.*?>/", $parts[1], $image_parts);
		$link_length = strlen($image_parts[0]);
		$this->m_link = substr($image_parts[0], 9, ($link_length - 31));	  
			  
		/////////////////////////////////////////////////////
		// extract the description
		//
		$this->m_description = $parts[2];      
		
		/////////////////////////////////////////////////////
		// extract the user
		//
		preg_match("/\<a.*?\<\/a\>/", $parts[0], $author_parts);
		$this->m_author = $author_parts[0];
		
		/////////////////////////////////////////////////////
		// extrace the title
		//
		$this->m_title = $parts[3];
	}
	
	// image title
	function get_title()
	{
		return $this->m_title;
	}
	
	// image link
	function get_link()
	{
		return $this->m_link;
	}
	
	// thumbnail
	function get_thumbnail()
	{
		return $this->get_image_size('_t');
	}
	
	// medium - default size
	function get_medium()
	{
		return $this->get_image_size('');
	}
	
	// large
	function get_large()
	{
		return $this->get_image_size('_b');
	}
	
	// square
	function get_square()
  {
  	return $this->get_image_size('_s');
  }
	          
	          
	function get_author()
	{
		return $this->m_author;
	}
	
	function get_description()
	{
		return $this->m_description;
	}
	
	/////////////////////////////////
	// get the image in the specified size
	//
	function get_image_size($image_size)
	{
		// get the image length
	  $image_link_length = strlen($this->m_image);
	        
	  // grab the image and lop off the last 5 chars
	  $image = substr($this->m_image, 0, $image_link_length - 6);
	                
	  // switch the end to a t :)
	  $image = $image . $image_size . ".jpg";
	                        
	  return $image;
	}
}


?>
