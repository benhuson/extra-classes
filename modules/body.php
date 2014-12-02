<?php

/**
 * @package     Extra Classes
 * @subpackage  Body
 */

add_filter( 'body_class', array( 'ExtraClasses_Body', 'body_class' ) );

/**
 * Body Class
 */
class ExtraClasses_Body {

	/**
	 * Body Class
	 *
	 * @param   array  $classes  Body classes.
	 * @return  array            Classes.
	 */
	public static function body_class( $classes ) {

		return $classes;

	}

}
