<?php
/*
  Plugin Name: Use Google Libraries
  Plugin URI: http://jasonpenney.net/wordpress-plugins/use-google-libraries/
  Description:Allows your site to use common javascript libraries from Google's AJAX Libraries CDN, rather than from Wordpress's own copies.
  Version: 1.2.1
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

if ( !class_exists( 'JCP_UseGoogleLibraries' ) ) {

	if ( ! defined( 'WP_CONTENT_URL' ) )
		define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
	if ( ! defined( 'WP_CONTENT_DIR' ) )
		define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
	if ( ! defined( 'WP_PLUGIN_URL' ) )
		define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
	if ( ! defined( 'WP_PLUGIN_DIR' ) )
		define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

	class JCP_UseGoogleLibraries {

		private static $instance;

		public static function get_instance() {
			if ( !isset( self::$instance ) ) {
				self::$instance =  new JCP_UseGoogleLibraries();
			}
			return self::$instance;
		}

		protected $google_scripts;
		protected $noconflict_url;
		protected $noconflict_next;
		protected $is_ssl;
		protected static $script_before_init_notice =
			'<strong>Use Google Libraries</strong>: Another plugin has registered or enqued a script before the "init" action.  Attempting to work around it.';
		/**
		 * PHP 4 Compatible Constructor
		 */
		function JCP_UseGoogleLibraries() {$this->__construct();}

		/**
		 * PHP 5 Constructor
		 */
		function __construct() {
			$this->google_scripts =
				array(
				// any extra scripts listed here not provided by WordPress
				// or another plugin will not be registered.  This liste
				// is just used to chancge where things load from.

				// 'script-handle' => ( 'google-lib-path', 'google-file-name', 'google-combined-into')
				/* jQuery */
				'jquery' => array( 'jquery', 'jquery.min', '' ),

				/* jQuery UI */
				'jquery-ui-core' => array( 'jqueryui', 'jquery-ui.min', '' ),
				'jquery-ui-accordion' => array( '', '', 'jquery-ui-core' ),
				'jquery-ui-autocomplete' => array( '', '', 'jquery-ui-core' ), /* jQueri UI 1.8 */
				'jquery-ui-button' => array( '', '', 'jquery-ui-core' ), /* jQuery UI 1.8 */
				'jquery-ui-datepicker' => array( '', '', 'jquery-ui-core' ),
				'jquery-ui-dialog' => array( '', '', 'jquery-ui-core' ),
				'jquery-ui-draggable' => array( '', '', 'jquery-ui-core' ),
				'jquery-ui-droppable' => array( '', '', 'jquery-ui-core' ),
				'jquery-ui-menu' => array( '', '', 'jquery-ui-core' ),
				'jquery-ui-mouse' => array( '', '', 'jquery-ui-core' ),  /* jQuery UI 1.8 */
				'jquery-ui-position' => array( '', '', 'jquery-ui-core' ),  /* jQuery UI 1.8 */
				'jquery-ui-progressbar' => array( '', '', 'jquery-ui-core' ),
				'jquery-ui-resizable' => array( '', '', 'jquery-ui-core' ),
				'jquery-ui-selectable' => array( '', '', 'jquery-ui-core' ),
				'jquery-ui-slider' => array( '', '', 'jquery-ui-core' ),
				'jquery-ui-sortable' => array( '', '', 'jquery-ui-core' ),
				'jquery-ui-tabs' => array( '', '', 'jquery-ui-core' ),
				'jquery-ui-widget' => array( '', '', 'jquery-ui-core' ),  /* jQuery UI 1.8 */

				/* jQuery Effects */
				'jquery-effects-core' => array( '', '', 'jquery-ui-core' ),
				'jquery-effects-blind' => array( '', '', 'jquery-ui-core' ),
				'jquery-effects-bounce' => array( '', '', 'jquery-ui-core' ),
				'jquery-effects-clip' => array( '', '', 'jquery-ui-core' ),
				'jquery-effects-drop' => array( '', '', 'jquery-ui-core' ),
				'jquery-effects-explode' => array( '', '', 'jquery-ui-core' ),
				'jquery-effects-fade' => array( '', '', 'jquery-ui-core' ),  /* jQuery UI 1.8 */
				'jquery-effects-fold' => array( '', '', 'jquery-ui-core' ),
				'jquery-effects-highlight' => array( '', '', 'jquery-ui-core' ),
				'jquery-effects-pulsate' => array( '', '', 'jquery-ui-core' ),
				'jquery-effects-scale' => array( '', '', 'jquery-ui-core' ),
				'jquery-effects-shake' => array( '', '', 'jquery-ui-core' ),
				'jquery-effects-slide' => array( '', '', 'jquery-ui-core' ),
				'jquery-effects-transfer' => array( '', '', 'jquery-ui-core' ),

				/* prototype */
				'prototype' => array( 'prototype', 'prototype', '' ),

				/* scriptaculous */
				'scriptaculous-root' => array( 'scriptaculous', 'scriptaculous', '' ),
				'scriptaculous-builder' => array( '', '', 'scriptaculous-root' ),
				'scriptaculous-effects' => array( '', '', 'scriptaculous-root' ),
				'scriptaculous-dragdrop' => array( '', '', 'scriptaculous-root' ),
				'scriptaculous-controls' => array( '', '', 'scriptaculous-root' ),
				'scriptaculous-slider' => array( '', '', 'scriptaculous-root' ),
				'scriptaculous-sound' => array( '', '', 'scriptaculous-root' ),

				/* moo tools */
				'mootools' => array( 'mootools', 'mootools-yui-compressed', '' ),

				/* Dojo */
				'dojo' => array( 'dojo', 'dojo.xd', '' ),

				/* swfobject */
				'swfobject' => array( 'swfobject', 'swfobject', '' ),

				/* YUI */
				'yui' => array( 'yui', 'build/yuiloader/yuiloader-min', '' ),

				/* Ext Core */
				'ext-core' => array( 'ext-core', 'ext-core', '' )

			);
			$this->noconflict_url = WP_PLUGIN_URL . '/use-google-libraries/js/jQnc.js';

			$this->noconflict_next = FALSE;
			// test for SSL
			// thanks to suggestions from Peter Wilson (http://peterwilson.cc/)
			// and Richard Hearne
			$is_ssl = false;
			if ( ( function_exists( 'getenv' ) and
					( ( getenv( 'HTTPS' ) != '' and getenv( 'HTTPS' ) != 'off' )
						or
						( getenv( 'SERVER_PORT' ) == '433' ) ) )
				or
				( isset( $_SERVER ) and
					( ( isset( $_SERVER['HTTPS'] ) and $_SERVER['HTTPS'] !='' and $_SERVER['HTTPS'] != 'off' )
						or
						( isset( $_SERVER['SERVER_PORT'] ) and $_SERVER['SERVER_PORT'] == '443' ) ) ) ) {
				$is_ssl = true;
			}
			$this->is_ssl = $is_ssl;
		}

		static function configure_plugin() {
			add_action( 'wp_default_scripts',
				array( 'JCP_UseGoogleLibraries',
					'replace_default_scripts_action' ),
				1000 );
			add_filter( 'script_loader_src',
				array( "JCP_UseGoogleLibraries", "remove_ver_query_filter" ),
				1000 );
			add_filter( 'init', array( "JCP_UseGoogleLibraries", "setup_filter" ) );

			// There's a chance some plugin has called wp_enqueue_script outside
			// of any hooks, which means that this plugin's 'wp_default_scripts'
			// hook will never get a chance to fire.  This tries to work around
			// that.
			global $wp_scripts;
			if ( is_a( $wp_scripts, 'WP_Scripts' ) ) {
				if ( WP_DEBUG !== false ) {
					error_log( self::$script_before_init_notice );
				}

				$ugl = self::get_instance();
				$ugl->replace_default_scripts( $wp_scripts );
			}
		}


		static function script_before_init_admin_notice() {
			echo '<div class="error fade"><p>' . self::$script_before_init_notice . '</p></div>';
		}

		static function setup_filter() {
			$ugl = self::get_instance();
			$ugl->setup();
		}

		/**
		 * Disables script concatination, which breaks when dependencies are not
		 * all loaded locally.
		 */
		function setup() {
			global $concatenate_scripts;
			$concatenate_scripts = false;

		}


		static function replace_default_scripts_action( &$scripts ) {
			$ugl = self::get_instance();
			$ugl->replace_default_scripts( $scripts );
		}

		/**
		 * Replace as many of the wordpress default script registrations as possible
		 * with ones from google
		 *
		 * @param object  $scripts WP_Scripts object.
		 */
		function replace_default_scripts( &$scripts ) {
			$newscripts = array();
			foreach ( $this->google_scripts as $name => $values ) {
				if ( $script = $scripts->query( $name ) ) {
					$lib = $values[0];
					$js = $values[1];
					$combined = $values[2];

					// default to requested ver
					$ver = $script->ver;
					
					if ( strpos( $ver, '-' ) !== false ) {
						if ( WP_DEBUG !== false ) {
							error_log("WordPress appears to be using a non-standard version of $name (version $ver). Use Google Libraries not enabled for $name.");
						}
						continue;
					}

					// TODO: replace with more flexible option
					// quick and dirty work around for scriptaculous 1.8.0
					if ( $name == 'scriptaculous-root' && $ver == '1.8.0' ) {
						$ver = '1.8';
					}

					if ( ( $combined !== '' ) && ( ! in_array( $combined, $script->deps ) ) ) {
						// if this script has been combined into another script
						// ensure this handle depends on the combined handle
						$script->deps[] = $combined;
					}

					// if $lib is empty, then this script does not need to be
					// exlicitly loaded when using googleapis.com, but we need to keep
					// it around for dependencies
					if ( $lib != '' ) {
						// build new URL
						$script->src = "http://ajax.googleapis.com/ajax/libs/$lib/$ver/$js.js";

						if ( $this->is_ssl ) {
							//use ssl
							$script->src = preg_replace( '/^http:/', 'https:', $script->src );
						}
					} else {
						$script->src = "";
					}
					$newscripts[] = $script;
				}
			}

			foreach ( $newscripts as $script ) {
				$olddata = $this->WP_Dependency_get_data( $scripts, $script->handle );
				$scripts->remove( $script->handle );
				// re-register with original ver
				$scripts->add( $script->handle, $script->src, $script->deps, $script->ver );
				if ( $olddata )
					foreach ( $olddata as $data_name => $data ) {
						$scripts->add_data( $script->handle, $data_name, $data );
					}
			}

		}


		function WP_Dependency_get_data( $dep_obj, $handle, $data_name = false ) {

			if ( !method_exists( $dep_obj, 'add_data' ) )
				return false;

			if ( !isset( $dep_obj->registered[$handle] ) )
				return false;

			if ( !$data_name )
				return $dep_obj->registered[$handle]->extra;

			return $dep_obj->registered[$handle]->extra[$data_name];
		}


		/**
		 * Remove 'ver' from query string for scripts loaded from Google's
		 * CDN
		 *
		 * @param string  $src src attribute of script tag
		 * @return string Updated src attribute
		 */
		function remove_ver_query( $src ) {
			if ( $this->noconflict_next ) {
				$this->noconflict_next = FALSE;
				echo "<script type='text/javascript'>try{jQuery.noConflict();}catch(e){};</script>\n";
			}
			if ( preg_match( '/ajax\.googleapis\.com\//', $src ) ) {
				$src = remove_query_arg( 'ver', $src );
				if ( strpos( $src, $this->google_scripts['jquery'][1] . ".js" ) ) {
					$this->noconflict_next = TRUE;
				}
			}
			return $src;
		}

		static function remove_ver_query_filter( $src ) {
			$ugl =  self::get_instance();
			return $ugl->remove_ver_query( $src );
		}
	}
}

//instantiate the class
if ( class_exists( 'JCP_UseGoogleLibraries' ) ) {
	JCP_UseGoogleLibraries::configure_plugin();
}
