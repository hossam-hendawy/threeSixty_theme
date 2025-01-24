<?php

namespace Register_Entities;

class Testimonials extends Entity
{
  public static function init()
  {
    // to copy
    register_post_type('Testimonials', [
        'label' => __('Testimonials', 'tes_theme'),
        'description' => __('Testimonials news and reviews', 'tes_theme'),
        'labels' => array(
            'name' => _x('Testimonials', 'Post Type General Name', 'tes_theme'),
            'singular_name' => _x('Testimonials', 'Post Type Singular Name', 'tes_theme'),
            'menu_name' => __('Testimonials', 'tes_theme'),
            'parent_item_colon' => __('Parent Testimonials', 'tes_theme'),
            'all_items' => __('All Testimonials', 'tes_theme'),
            'view_item' => __('View Testimonials', 'tes_theme'),
            'add_new_item' => __('Add New Testimonials', 'tes_theme'),
            'add_new' => __('Add New', 'tes_theme'),
            'edit_item' => __('Edit Testimonials', 'tes_theme'),
            'update_item' => __('Update Testimonials', 'tes_theme'),
            'search_items' => __('Search Testimonials', 'tes_theme'),
            'not_found' => __('Not Found', 'tes_theme'),
            'not_found_in_trash' => __('Not found in Trash', 'tes_theme'),
        ),
      // Features this CPT supports in Post Editor
        'supports' => array(
            'title',
            'revisions'
        ),
      // You can associate this CPT with a taxonomy or custom taxonomy.
      /* A hierarchical CPT is like Pages and can have
      * Parent and child items. A non-hierarchical CPT
      * is like Posts.
      */
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'menu_icon' => 'dashicons-format-quote',
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 9,
        'can_export' => true,
        'has_archive' => false,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
        'show_in_rest' => true,
    ]);
  }
}


