<?php
/**
 * Validates a value as a positive integer
 *
 * Zero is not negative nor positive.
 *
 * @param $value
 *
 * @return bool
 */
function tribe_cmb2_sanitize_positive_int( $value ) {
	return is_numeric( $value ) && intval( $value ) > 0 && intval( $value ) == $value ? $value : '';
}
