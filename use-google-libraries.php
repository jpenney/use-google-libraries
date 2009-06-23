<?php
/*
  Plugin Name: Use Google Libraries
  Plugin URI: http://jasonpenney.net/wordpress-plugins/use-google-libraries/
  Description:Allows your site to use common javascript libraries from Google's AJAX Libraries CDN, rather than from Wordpress's own copies. 
  Version: 1.0.7
  Author: Jason Penney
  Author URI: http://jasonpenney.net/
*/ 

/*  Copyright 2008  Jason Penney (email : jpenney@jczorkmid.net )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation using version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


*/

if (!class_exists('JCP_UseGoogleLibraries')) {

  $google_scripts;

  if ( ! defined( 'WP_CONTENT_URL' ) )
    define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
  if ( ! defined( 'WP_CONTENT_DIR' ) )
    define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
  if ( ! defined( 'WP_PLUGIN_URL' ) )
    define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
  if ( ! defined( 'WP_PLUGIN_DIR' ) )
    define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
  

  class JCP_UseGoogleLibraries	{
    var $google_scripts;
    var $url;
    var $path;
    var $plugin;
    var $optionsName;
    var $version = '1.0.7';
    
    /**
     * PHP 4 Compatible Constructor
     */
    function JCP_UseGoogleLibraries(){$this->__construct();}
    
    /**
     * PHP 5 Constructor
     */		
    function __construct(){
      $this->name = plugin_basename(__FILE__);
      if (function_exists('plugin_dir_url')) {
        $this->url = plugin_dir_url(__FILE__);
      } else  {
        $this->url = trailingshashit(WP_PLUGIN_URL . $this->name);
      }
      if(function_exists('plugin_dir_path')) {
        $this->path = plugin_dir_path(__FILE__);
      } else {
        $this->path = trailingshashit(WP_PLUGIN_DIR . $this->name);
      }
      $this->optionsName = $this->name . '-options';
      $this->google_scripts =   array(
                                      'jquery' => array( 'jquery','jquery.min'),
                                      'jquery-ui-core' => array('jqueryui','jquery-ui.min'),
                                      'jquery-ui-tabs' => array('',''),
                                      'jquery-ui-sortable' => array('',''),
                                      'jquery-ui-draggable' => array('',''),
                                      'jquery-ui-resizable' => array('',''),
                                      'jquery-ui-dialog' => array('',''),
                                      'prototype' => array('prototype','prototype'),
                                      'scriptaculous-root' => array('scriptaculous', 'scriptaculous'),
                                      'scriptaculous-builder' => array('',''),
                                      'scriptaculous-effects' => array('',''),
                                      'scriptaculous-dragdrop' => array('',''),
                                      'scriptaculous-controls' => array('',''),
                                      'scriptaculous-slider' => array('',''),
                                      'scriptaculous-sound' => array('',''),
                                      'mootools' => array('mootools','mootools-yui-compressed'),
                                      'dojo' => array('dojo','dojo.xd'),
                                      'swfobject' => array('swfobject','swfobject'),
                                      'yui' => array('yui','build/yuiloader/yuiloader-min'),
                                      'ext-core' => array('ext-core','ext-core')
                                      );
      add_filter( 'init', array(&$this,"setup"));
    }

    /**
     * Disables script concatination, which breaks when dependencies are not 
     * all loaded locally
     */
    function setup() {
      global $concatenate_scripts;
      $concatenate_scripts = false;
      add_action( 'wp_default_scripts', array(&$this,"replace_default_scripts"),1000);
      add_filter( 'print_scripts_array',array(&$this,"jquery_noconflict"),1000);
      add_filter( 'script_loader_src', array(&$this,"remove_ver_query"),1000);
      add_filter("plugin_action_links_".$this->name, 
                 array(&$this,"action_links"));
    }

    /**
     * Replace as many of the wordpress default script registrations as possible
     * with ones from google 
     *
     * @param object $scripts WP_Scripts object.
     */
    function replace_default_scripts ( &$scripts ) { 
      $newscripts = array();
      foreach ( $this->google_scripts as $name => $values ) {
	if ($script = $scripts->query($name)) {
	  $lib = $values[0];
	  $js = $values[1];

	  // default to requested ver
	  $ver = $script->ver;

          // TODO: replace with more flexible option
          // quick and dirty work around for scriptaculous 1.8.0
          if (strpos($name,'scriptaculous')  && $ver = '1.8.0') {
            $ver = '1.8';
          }

          // if $lib is empty, then this script does not need to be 
          // exlicitly loaded when using googleapis.com, but we need to keep
          // it around for dependencies
	  if ($lib != '') {
	    // build new URL
	    $script->src = "http://ajax.googleapis.com/ajax/libs/$lib/$ver/$js.js";
            // test for SSL
            // thanks to suggestion from Peter Wilson (http://peterwilson.cc/)
            if ((isset($_SERVER['HTTPS'])) AND ($_SERVER['HTTPS'] != ”) AND ($_SERVER['HTTPS'] != ‘off’)) {
              //use ssl
              $script->src = preg_replace('/^http:/', 'https:', $script->src);
            }
	  } else {
	    $script->src = "";
	  }
	  $newscripts[] = $script;
	}
      }

      foreach ($newscripts as $script) {
        $olddata = $this->WP_Dependency_get_data($scripts, $script->handle);
	$scripts->remove( $script->handle );
	// re-register with original ver
	$scripts->add($script->handle, $script->src, $script->deps, 
                      $script->ver);
        if ($olddata)
          foreach ($olddata as $data_name => $data) {
            $scripts->add_data($script->handle,$data_name,$data);
          }
      }
      $scripts->add( 'jquery-noconflict', $this->url . '/js/jQnc.js',
                     array('jquery-core'));
      $jqueryGroup = $this->WP_Dependency_get_data($scripts,'jquery','group');
      if ($jqueryGroup) {
        $scripts->add_data('jquery-noconflict','group',$jqueryGroup);;
      }
    }


    /** 
     * Ensure jQuery is loaded in noConflict mode
     *
     * @param array $js_array JavaScript scripts array
     * @return array Updated scripts array, if needed
     */
    function jquery_noconflict( $js_array ) {
      if ( false === $jquery = array_search( 'jquery', $js_array ) ) {
        return $js_array;
      }
      array_splice( $js_array, $jquery, 1, array('jquery','jquery-noconflict'));
      return $js_array;
    }

    function WP_Dependency_get_data( $dep_obj, $handle, $data_name = false) {
      
      if ( !method_exists($dep_obj,'add_data') )
        return false;

      if ( !isset($dep_obj->registered[$handle]) )
        return false;

      if (!$data_name)
        return $dep_obj->registered[$handle]->extra;

      return $dep_obj->registered[$handle]->extra[$data_name];
    }

    function action_links( $links ) {
      $settings_link = '<a href="options-general.php?page='.__FILE__.
        '">Settings</a>';
      array_unshift($links,$settings_link);
      return $links;
    }

    /** 
     * Remove 'ver' from query string for scripts loaded from Google's
     * CDN
     *
     * @param string $src src attribute of script tag
     * @return string Updated src attribute
     */
    function remove_ver_query ($src) {
      if ( preg_match( '/ajax\.googleapis\.com\//', $src ) ) {
	$src = remove_query_arg('ver',$src);
      }
      return $src;
    }

    function admin_init() {
      register_setting($this->optionsName, 'settings_saved');
      register_setting($this->optionsName, 'safe_versions');
      register_setting($this->optionsName, 'last_tested');
      register_setting($this->optionsName, 'options_version');
    }

    var $_options = null;
    function get_options() {
      if ($this->_options == null) {
        $options = array('settings_saved' => false,
                         'safe_versions' => Array(),
                         'last_tested' => 0,
                         'options_version' => $this->version
                         );
        $savedOptions = get_option($this->optionsName);
        if (!empty($savedOptions)) {
          foreach($savedOptions as $key => $value) {
            $options[$key] = $value;
          }
          $this->_options =$options;
          $this->saveOptions();
        }
      }
      return $this->_options;
    }
    
    function save_options() {
      if ($this->_options != null) {
        update_option($this->optionsName,$this->_options);
      }
    }

    function get_option($key) {
      $this->get_options();
      return $this->_options[$key];
    }
  }
}

//instantiate the class
if (class_exists('JCP_UseGoogleLibraries')) {
  $JCP_UseGoogleLibraries = new JCP_UseGoogleLibraries();
}
