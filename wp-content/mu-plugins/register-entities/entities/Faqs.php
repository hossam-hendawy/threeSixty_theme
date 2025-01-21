<?php

namespace Register_Entities;

class Faqs extends Entity
{
  public static function init()
  {
    // to copy
    register_post_type('faqs', [
        'label' => __('faqs', 'tes_theme'),
        'description' => __('Faqs news and reviews', 'tes_theme'),
        'labels' => array(
            'name' => _x('Faqs', 'Post Type General Name', 'tes_theme'),
            'singular_name' => _x('Faqs', 'Post Type Singular Name', 'tes_theme'),
            'menu_name' => __('Faqs', 'tes_theme'),
            'parent_item_colon' => __('Parent Faqs', 'tes_theme'),
            'all_items' => __('All Faqs', 'tes_theme'),
            'view_item' => __('View Faqs', 'tes_theme'),
            'add_new_item' => __('Add New Faqs', 'tes_theme'),
            'add_new' => __('Add New', 'tes_theme'),
            'edit_item' => __('Edit Faqs', 'tes_theme'),
            'update_item' => __('Update Faqs', 'tes_theme'),
            'search_items' => __('Search Faqs', 'tes_theme'),
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
        'menu_icon' => 'dashicons-format-status',
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


