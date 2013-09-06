<?php

class UGL_ScriptTests extends UGL_ScriptTestCase {

	function test_protocol_relative_url() {
		$jquery = $this->scripts->query( 'jquery-core' );
		if ( !$jquery ) {
			$jquery = $this->scripts->query( 'jquery' );
		}
		$prefix = '';
		if ( $this->ugl->get_protocol_relative_supported() ) {
			$prefix = '//';
		} else {
			if ( is_ssl() ) {
				$prefix = 'https://';
			} else {
				$prefix = 'http://';
			}
		}
		$this->assertStringStartsWith($prefix, $jquery->src);
	}
	
	function test_scripts_replaced() {
		$scripts = $this->ugl->get_google_scripts();
		foreach ( array_keys( $scripts ) as $handle) {
			if ( $script = $this->scripts->query( $handle ) ) {
				if ( $script->src && strpos( $script->ver, '-' ) === false) {
					$this->assertContains('//ajax.googleapis.com/ajax/libs', 
							$script->src, $handle + ' should be loading from google');
				}
			}
		}
	}
	
	function test_nonstandard_ver_not_replaced() {
		$scripts = $this->ugl->get_google_scripts();
		foreach ( array_keys( $scripts ) as $handle) {
			if ( $script = $this->scripts->query( $handle ) ) {
				if ( $script->src && strpos( $script->ver, '-' ) !== false) {
					$this->assertNotContains('//ajax.googleapis.com/ajax/libs',
							$script->src, $handle + ' should not be loading from google');
				}
			}
		}
	}
}
