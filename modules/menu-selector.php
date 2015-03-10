<?php

/**
 * @package     Extra Classes
 * @subpackage  Menu Selector
 *
 * This class allows you to control which menu items should be selected (or deselected) when viewing
 * certain pages of your site. You need to add classes to the menu items for this to work.
 *
 * - ecms-archive-{$post_type}                 : Select menu item when viewing a post type archive page.
 * - ecms-single-{$post_type}                  : Select menu item when viewing a single post type page.
 * - ecms-taxonomy-{$taxonomy}                 : Select menu item when viewing a taxonomy archive.
 * - ecms-taxonomy-{$taxonomy}-term-{$term}    : Select menu item when viewing a taxonomy term archive.
 * - ecms-404                                  : Select menu item when viewing a 404 page.
 * 
 * - ecms-no-archive-{$post_type}              : Deselect menu item when viewing a post type archive page.
 * - ecms-no-single-{$post_type}               : Deselect menu item when viewing a single post type page.
 * - ecms-no-taxonomy-{$taxonomy}              : Deselect menu item when viewing a taxonomy archive.
 * - ecms-no-taxonomy-{$taxonomy}-term-{$term} : Deselect menu item when viewing a taxonomy term archive.
 * - ecms-no-404                               : Deselect menu item when viewing a 404 page.
 */

add_action( 'wp', array( 'ExtraClasses_Menu_Selector', 'setup_inbuilt_states' ) );
add_filter( 'wp_nav_menu_objects', array( 'ExtraClasses_Menu_Selector', 'apply_menu_class_filters' ), 10, 2 );

/**
 * Menu Selector Class
 */
class ExtraClasses_Menu_Selector {

	/**
	 * Selected States
	 *
	 * @var  array
	 */
	protected static $selected_states = array();

	/**
	 * Deselected States
	 * 
	 * @var  array
	 */
	protected static $deselected_states = array();

	/**
	 * Setup Inbuilt States
	 */
	public static function setup_inbuilt_states() {

		// Selected States
		self::register_selected_state( 'ecms-archive-%%$post_type%%' );
		self::register_selected_state( 'ecms-single-%%$post_type%%' );
		self::register_selected_state( 'ecms-taxonomy-%%$taxonomy%%' );
		self::register_selected_state( 'ecms-taxonomy-%%$taxonomy%%-term-%%term%%' );
		self::register_selected_state( 'ecms-404' );

		// Deselected States
		self::register_deselected_state( 'ecms-no-archive-%%$post_type%%' );
		self::register_deselected_state( 'ecms-no-single-%%$post_type%%' );
		self::register_deselected_state( 'ecms-no-taxonomy-%%$taxonomy%%' );
		self::register_deselected_state( 'ecms-no-taxonomy-%%$taxonomy%%-term-%%term%%' );
		self::register_deselected_state( 'ecms-no-404' );

	}

	/**
	 * Register Selected State
	 *
	 * States can include formatted placeholders.
	 * See self::format_current_state().
	 *
	 * @param  string  $class  Selected state formatted class.
	 */
	public static function register_selected_state( $class ) {

		$class = self::sanitize_state( $class );

		// Only add if not already added
		if ( ! in_array( $class, self::$selected_states ) ) {
			self::$selected_states[] = $class;
		}

	}

	/**
	 * Register Deselected State
	 *
	 * States can include formatted placeholders.
	 * See self::format_current_state().
	 *
	 * @param  string  $class  Deselected state formatted class.
	 */
	public static function register_deselected_state( $class ) {

		$class = self::sanitize_state( $class );

		// Only add if not already added
		if ( ! in_array( $class, self::$deselected_states ) ) {
			self::$deselected_states[] = $class;
		}

	}

	/**
	 * Sanitize State
	 *
	 * States are forced to be lowercase with alpahanumeric characters,
	 * underscores, hyphens and percent signs.
	 *
	 * @param   string  $state  State class.
	 * @return  string          Sanitized state class.
	 */
	public static function sanitize_state( $state ) {

		$state = strtolower( $state );
		$state = preg_replace( '/[^a-z0-9_\-\%]/', '', $state );

		return $state;

	}

	/**
	 * Format Current State
	 *
	 * Substitutes placeholder strings with appropriate values.
	 * May return multiple states a term has ancestors.
	 *
	 * - %%$post_type%% : Post type slug
	 * - %%$taxonomy%%  : Taxonomy slug
	 * - %%$term%%      : Term slug
	 *
	 * @param   state  $state  State class.
	 * @return  array          Formatted states.
	 */
	public static function format_current_state( $state ) {

		// Get the main queried object
		$qo = get_queried_object();

		// If state contains a %%post_type%% placeholder...
		if ( strpos( $state, '%%post_type%%' ) !== false ) {

			// If queried object is a post or this is a post type archive page...
			if ( is_a( $qo, 'WP_Post' ) || is_post_type_archive() ) {

				// Find and replace post type
				$post_type = is_post_type_archive() ? get_post_type() : get_post_type( $qo );
				$state = str_replace( '%%post_type%%', $post_type, $state );

			} else {

				// ... otherwise remove placeholder
				$state = '';

			}

		}

		// If state contains a %%taxonomy%% placeholder...
		if ( isset( $qo->taxonomy ) && ( strpos( $state, '%%taxonomy%%' ) !== false || strpos( $state, '%%taxonomy%%' ) !== false ) ) {

			// Allow current taxonomy to be filtered
			$taxonomy = apply_filters( 'extraclasses_menu_selector_current_taxonomy', $qo->taxonomy );

			// Find and replace taxonomy
			if ( ! empty( $taxonomy ) ) {
				$state = str_replace( '%%taxonomy%%', $taxonomy, $state );

				// If state contains a %%term%% placeholder...
				if ( strpos( $state, '%%term%%' ) !== false ) {

					// Allow current term to be filtered
					$term_object = apply_filters( 'extraclasses_menu_selector_current_term', $qo );

					if ( isset( $term_object->term_id ) && isset( $term_object->slug ) ) {
						$term_format = $state;

						// Find and replace term
						$states = array( str_replace( '%%term%%', $term_object->slug, $term_format ) );

						// If term has ancetsors, create additional states using those terms
						$anc = get_ancestors( $term_object->term_id, $term_object->taxonomy );
						if ( count( $anc ) > 0 ) {
							foreach ( $anc as $a ) {
								$a = get_term( $a, $term_object->taxonomy );
								$states[] = str_replace( '%%term%%', $a->slug, $term_format );
							}
						}
						$state = $states;

					}

				}

			} else {

				// ... otherwise remove placeholder
				$state = '';

			}

		}

		// 404
		if ( ! is_404() && in_array( $state, array( 'ecms-404', 'ecms-no-404' ) ) ) {
			$state = '';
		}

		// If only one state, needs to return an array
		if ( ! is_array( $state ) ) {
			$state = array( $state );
		}

		return $state;

	}

	/**
	 * Get Current Selected States
	 *
	 * @return  array  Current selected states.
	 */
	public static function get_current_selected_states() {

		$states = array();

		// Replace post/taxonomy placeholder strings
		foreach ( self::$selected_states as $state ) {
			$states = array_merge( $states, self::format_current_state( $state ) );
		}

		// Remove empty states
		$states = array_filter( $states );

		return $states;

	}

	/**
	 * Get Current Deselected States
	 *
	 * @return  array  Current deselected states.
	 */
	public static function get_current_deselected_states() {

		$states = array();

		// Replace post/taxonomy placeholder strings
		foreach ( self::$deselected_states as $state ) {
			$states = array_merge( $states, self::format_current_state( $state ) );
		}

		// Remove empty states
		$states = array_filter( $states );

		return $states;

	}

	/**
	 * Apply Menu Class Filters
	 *
	 * Filters the nav menu objects and adjusts their classes.
	 *
	 * @param   array  $sorted_menu_items  Menu items.
	 * @param   array  $args               Menu item arags.
	 * @return  array                      Filtered menu items.
	 */
	public static function apply_menu_class_filters( $sorted_menu_items, $args ) {

		// Get current states
		$states = self::get_current_selected_states();
		$deselected_states = self::get_current_deselected_states();

		// Loop through menu items
		foreach ( $sorted_menu_items as $key => $val ) {
			$classes = $sorted_menu_items[ $key ]->classes;

			// Remove deselected states
			$found_states = array_intersect( $deselected_states, $classes );
			if ( count( $found_states ) > 0 ) {
				$sorted_menu_items[ $key ]->classes = array_diff( $sorted_menu_items[ $key ]->classes, self::get_selected_classes() );
			}

			// Add selected states
			$found_states = array_intersect( $states, $classes );
			if ( count( $found_states ) > 0 ) {
				$sorted_menu_items[ $key ]->classes[] = 'current-menu-ancestor';
			}

		}

		return $sorted_menu_items;

	}

	/**
	 * Get Selected Classes
	 *
	 * @return  array  Menu selected states.
	 */
	public static function get_selected_classes() {

		return array(
			'current-menu-item',
			'current-menu-parent',
			'current-menu-ancestor',

			// Back compat
			'current_page_item',
			'current_page_parent',
			'current_page_ancestor'

		);

	}

}
