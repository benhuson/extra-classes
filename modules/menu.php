<?php

add_filter( 'wp_nav_menu_objects', array( 'ExtraClasses_Menu', 'filter_wp_nav_menu_objects' ), 20, 2 );
add_filter( 'extraclasses_menu_item_class', array( 'ExtraClasses_Menu', 'extraclasses_menu_item_class' ), 20, 3 );

class ExtraClasses_Menu {

	/**
	 * Filter Nav Menu Objects
	 *
	 * Implements a 'extraclasses_menu_item_class' filter to make filtering menu item classes easier.
	 *
	 * @param   array  $sorted_menu_items  Sorted menu items.
	 * @param   array  $args               Array of arguments.
	 * @return  array                      Sorted menu items.
	 */
	static function filter_wp_nav_menu_objects( $sorted_menu_items, $args ) {
		foreach ( $sorted_menu_items as $key => $val ) {
			$sorted_menu_items[ $key ]->classes = apply_filters( 'extraclasses_menu_item_class', $sorted_menu_items[ $key ]->classes, $sorted_menu_items[ $key ], $args );
		}
		return $sorted_menu_items;
	}

	/**
	 * Menu Item Classes
	 *
	 * This helps the primary menu decide what items should be selected
	 * when you get deeper into the navigation. You need to add classes
	 * to the menu items as follows for this to work:
	 *
	 * mc-post-type-{post-type}
	 * mc-taxonomy-{taxonomy}
	 *
	 * @param   array   $classes    Array of classes.
	 * @param   object  $menu_item  Menu item object.
	 * @param   array   $args       Menu args.
	 * @return  array               Classes.
	 */
	static function extraclasses_menu_item_class( $classes, $menu_item, $args ) {
		$post_types = ExtraClasses::get_post_types();
		$taxonomies = ExtraClasses::get_taxonomies();

		// Post Types
		foreach ( $post_types as $post_type ) {
			if ( is_post_type_archive( $post_type ) || ExtraClasses::is_post_type_single( $post_type ) ) {
				if ( in_array( 'mc-post-type-' . $post_type, $classes ) ) {
					$classes = array_merge( $classes, ExtraClasses_Menu::get_current_classes() );
				} else {
					$classes = array_diff( $classes, ExtraClasses_Menu::get_current_classes() );
				}
			}
		}

		// Taxonomies
		foreach ( $taxonomies as $taxonomy ) {
			if ( is_tax( $taxonomy ) || ( is_category() && 'category' == $taxonomy ) || ( is_tag() && 'post_tag' == $taxonomy ) ) {
				if ( in_array( 'mc-taxonomy-' . $taxonomy, $classes ) ) {
					$classes = array_merge( $classes, ExtraClasses_Menu::get_current_classes() );
				} else {
					$classes = array_diff( $classes, ExtraClasses_Menu::get_current_classes() );
				}
			}
		}

		return $classes;
	}

	/**
	 * Get 'Current' Classes
	 *
	 * @return  array  'Current' classes.
	 */
	static function get_current_classes() {
		return array(
			'current_page_item',
			'current_page_item',
			'current-menu-item',
			'current-menu-item',
			'current_page_ancestor',
			'current_page_ancestor',
			'current_page_parent',
			'current-page-ancestor'
		);
	}

	/**
	 * Remove 'Current' Classes
	 *
	 * @param   array  $classes  Menu item classes.
	 * @return  array            Classes with 'current' classes removed.
	 */
	static function remove_current_classes( $classes ) {
		return $classes;
	}

}
