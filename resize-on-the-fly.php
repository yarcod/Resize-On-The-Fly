<?php
/*
Plugin Name: Resize On the Fly 
Plugin URI: #
Description: Enables images resizing on the fly on the server before page is loaded at the client. This enables faster loading and smaller amounts of data needed to render the page. Images are cached on the server after the first render of every image. 
Version: 0.2
Author: Daniel Edholm
Author URI: #
License: GPL2
*/
/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : PLUGIN AUTHOR EMAIL)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once('aq_resizer.php');

add_filter('the_content', 'resize_images');

function resize_images($content) {
	// Do not display HTML errors on page render; keep them internal!
	libxml_use_internal_errors(true);
	// Retrieve the page content, and make content UTF-8 encoded.
	$doc = new DOMDocument();
	$doc->LoadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
	// We're only interested in the img parts of the page
	$images = $doc->getElementsByTagName('img');
	$attributes = array('src'=>'src');
	foreach ($images as $image) {
		$width = '';
		$heigth = '';
		foreach ($attributes as $key=>$value) {
			// Get the value of the current attributes and set them to variables.
			$$key = $image->getAttribute($key);
			// Remove the existing attributes.
			$image->removeAttribute($key);
			// Find the width and heigth of the image attributes
			$width = $image->getAttribute('width');
			$height = $image->getAttribute('height');
			// Set the new attribute.
			$key = aq_resize($src, $width, $height);
			$image->setAttribute($value, $key);
		}
	}
	return $doc->saveHTML();
}

if(class_exists('WP_Plugin_Template')) { // Installation and uninstallation hooks 
	register_activation_hook(__FILE__, array('WP_Plugin_Template', 'activate')); 
	register_deactivation_hook(__FILE__, array('WP_Plugin_Template', 'deactivate')); 
	
	// instantiate the plugin class 
	$wp_plugin_template = new WP_Plugin_Template(); 
} 

?>