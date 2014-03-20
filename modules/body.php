<?php

add_filter( 'body_class', array( 'ExtraClasses_Body', 'body_class' ) );

class ExtraClasses_Body {

	/**
	 * Body Class
	 *
	 * @param   array  $classes  Body classes.
	 * @return  array            Classes.
	 */
	static function body_class( $classes ) {
		return $classes;
	}

}
