<?php
// Here you can initialize variables that will be available to your tests

// workaround for an oddity when running tests on GH actions with php-timer 3.1.4+
if ( ! isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ) {
	$_SERVER['REQUEST_TIME_FLOAT'] = microtime( true );
} elseif ( ! \is_float( $_SERVER['REQUEST_TIME_FLOAT'] ) ) {
	$_SERVER['REQUEST_TIME_FLOAT'] = (float) $_SERVER['REQUEST_TIME_FLOAT'];
}
