<?php

namespace Register_Entities;

class Services extends Entity
{
  public static function init()
  {
    // Register Custom Post Type
    register_post_type('services', [
        'label' => __('Services', 'tes_theme'),
        'description' => __('Services provided by the company', 'tes_theme'),
        'labels' => array(
            'name' => _x('Services', 'Post Type General Name', 'tes_theme'),
            'singular_name' => _x('Service', 'Post Type Singular Name', 'tes_theme'),
            'menu_name' => __('Services', 'tes_theme'),
            'parent_item_colon' => __('Parent Service', 'tes_theme'),
            'all_items' => __('All Services', 'tes_theme'),
            'view_item' => __('View Service', 'tes_theme'),
            'add_new_item' => __('Add New Service', 'tes_theme'),
            'add_new' => __('Add New', 'tes_theme'),
            'edit_item' => __('Edit Service', 'tes_theme'),
            'update_item' => __('Update Service', 'tes_theme'),
            'search_items' => __('Search Services', 'tes_theme'),
            'not_found' => __('Not Found', 'tes_theme'),
            'not_found_in_trash' => __('Not found in Trash', 'tes_theme'),
        ),
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'revisions'
        ),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'menu_icon' => 'dashicons-hammer',
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 10,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
        'show_in_rest' => true,
    ]);
  }
}
