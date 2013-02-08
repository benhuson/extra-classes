<?php 

/*
Plugin Name: Extra Classes
Plugin URI: https://github.com/benhuson/extra-classes
Description: Adds missing classes for selected menu states such as highlighting categories when viewing a blog post or parent page when you're on an attachment page.
Version: 0.1
Author: Ben Huson
Author URI: https://github.com/benhuson
License: GPL2
*/

/*
Copyright 2013 Ben Huson

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

class ExtraClasses {

	/**
	 * Constructor
	 */
    function __construct() {
		//add_filter( 'body_class', array( $this, 'body_classes' ) );
		add_filter( 'wp_nav_menu_objects', array( $this, 'wp_nav_menu_objects' ), 5, 2 );
	}

	/**
	 * Body Classes
	 */
	function body_classes( $classes ) {
		return $classes;
	}

	/**
	 * Menu Classes
	 * This helps menus decide what items should be selected when you get deeper into
	 * the navigation. For example, show a page a selected if view an attachment page
	 * of an image attached to that page.
	 *
	 * @param array $sorted_menu_items Sorted menu items.
	 * @param array $args Array of arguments.
	 * @return array Sorted menu items.
	 */
	function wp_nav_menu_objects( $sorted_menu_items, $args ) {
		global $post;

		$sorted_menu_items = $this->_single_post_menu_item_filters( $sorted_menu_items );
		$sorted_menu_items = $this->_attachment_page_menu_item_filters( $sorted_menu_items );

		return $sorted_menu_items;
	}

	/**
	 * Single Post Menu Item Filters
	 *
	 * @param array $sorted_menu_items Menu items.
	 * @return array Filtered menu items.
	 */
	function _single_post_menu_item_filters( $sorted_menu_items ) {
		global $post;

		if ( is_single() && ! is_attachment() ) {
			$term_ancestors = array();
			$menu_item_ancestors = array();
			foreach ( $sorted_menu_items as $key => $val ) {
				if ( $val->type == 'taxonomy' && has_term( $val->object_id, $val->object, $post->ID ) ) {
					$classes = array( 'current-page-ancestor', 'current_page_ancestor', 'current-page-parent', 'current_page_parent', 'current-menu-ancestor', 'current-menu-parent' );
					$sorted_menu_items[$key] = $this->_add_classes_to_menu_item( $classes, $sorted_menu_items[$key] );

					// Term Ancestors
					$term_ancestors[$val->object] = get_ancestors( $val->object_id, $val->object );

					// Menu Item Ancestors
					$menu_item_ancestors = $this->_add_menu_item_ancestors_to_array( $menu_item_ancestors, $val->ID );
				}
			}
			foreach ( $sorted_menu_items as $key => $val ) {
				if ( $val->type == 'taxonomy' ) {
					foreach ( $term_ancestors as $tax => $t ) {
						if ( in_array( $val->object_id, $t ) && $val->object == $tax ) {
							$classes = array( 'current-page-ancestor', 'current_page_ancestor' );
							$sorted_menu_items[$key] = $this->_add_classes_to_menu_item( $classes, $sorted_menu_items[$key] );

							// Menu Item Ancestors
							$menu_item_ancestors = $this->_add_menu_item_ancestors_to_array( $menu_item_ancestors, $val->ID );
						}
					}
				
				}
			}
			$sorted_menu_items = $this->_add_classes_to_menu_items( 'current-menu-ancestor', $sorted_menu_items, $menu_item_ancestors );
		}
		return $sorted_menu_items;
	}

	/**
	 * Attachment Page Menu Item Filters
	 *
	 * @param array $sorted_menu_items Menu items.
	 * @return array Filtered menu items.
	 */
	function _attachment_page_menu_item_filters( $sorted_menu_items ) {
		global $post;

		if ( is_attachment() && $post->post_parent > 0 ) {
			$post_ancestors = get_post_ancestors( $post->ID );
			$menu_item_ancestors = array();
			foreach ( $sorted_menu_items as $key => $val ) {
				if ( $val->type == 'post_type' && in_array( $val->object_id, $post_ancestors ) ) {
					$classes = array( 'current-page-ancestor', 'current_page_ancestor' );
					if ( $post->post_parent == $val->object_id ) {
						$classes = array_merge( $classes, array( 'current-page-parent', 'current_page_parent', 'current-menu-ancestor', 'current-menu-parent' ) );
					}
					$sorted_menu_items[$key] = $this->_add_classes_to_menu_item( $classes, $sorted_menu_items[$key] );

					// Menu Item Ancestors
					$menu_item_ancestors = $this->_add_menu_item_ancestors_to_array( $menu_item_ancestors, $val->ID );
				}
			}
			$sorted_menu_items = $this->_add_classes_to_menu_items( 'current-menu-ancestor', $sorted_menu_items, $menu_item_ancestors );
		}
		return $sorted_menu_items;
	}

	/**
	 * Add Classes To Menu Item
	 *
	 * @param string|array $classes Class name(s).
	 * @param object $menu_item Menu item object.
	 * @return object Menu item.
	 */
	function _add_classes_to_menu_item( $classes, $menu_item ) {
		if ( is_array( $classes ) ) {
			$menu_item->classes = array_merge( $menu_item->classes, $classes );
		} else {
			$menu_item->classes[] = $classes;
		}
		$menu_item->classes = array_unique( $menu_item->classes );
		return $menu_item;
	}

	/**
	 * Add Classes To Menu Items
	 *
	 * @param string|array $classes Class name(s).
	 * @param array $menu_items Menu item objects.
	 * @param array $menu_ids Menu item ids to which the class will be added.
	 * @return array Menu items.
	 */
	function _add_classes_to_menu_items( $classes, $menu_items, $menu_ids ) {
		foreach ( $menu_items as $key => $val ) {
			if ( in_array( $val->ID, $menu_ids ) ) {
				$menu_items[$key] = $this->_add_classes_to_menu_item( $classes, $menu_items[$key] );
			}
		}
		return $menu_items;
	}

	/**
	 * Add Menu Item Ancestors To Array
	 *
	 * @param array $ancestors Array of ancestors to add to.
	 * @param int $item_id Menu item ID.
	 * @return array Menu item ancestor IDs.
	 */
	function _add_menu_item_ancestors_to_array( $ancestors, $item_id ) {
		return array_merge( $ancestors, $this->_get_menu_item_ancestor_ids( $item_id ) );
	}

	/**
	 * Get Menu Item Ancestor IDs
	 *
	 * @param int $item_id Menu item ID.
	 * @param array $ancestors Array of ancestors to populate (add to).
	 * @return array Menu item ancestor IDs.
	 */
	function _get_menu_item_ancestor_ids( $item_id, $ancestors = null ) {
		if ( ! is_array( $ancestors ) )
			$ancestors = array();

		$parent = get_post_meta( $item_id, '_menu_item_menu_item_parent', true );
		while ( $parent != 0 ) {
			$ancestors[] = $parent;
			$parent = get_post_meta( $parent, '_menu_item_menu_item_parent', true );
		}
		return $ancestors;
	}

}

global $extra_classes;
$extra_classes = new ExtraClasses();
