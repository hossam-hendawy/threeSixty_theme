<?php

namespace Register_Entities;

class Packages extends Entity
{
  public static function init()
  {
    // Register Packages Custom Post Type
    register_post_type('packages', [
        'label' => __('Packages', 'tes_theme'),
        'description' => __('Packages available for selection', 'tes_theme'),
        'labels' => array(
            'name' => _x('Packages', 'Post Type General Name', 'tes_theme'),
            'singular_name' => _x('Package', 'Post Type Singular Name', 'tes_theme'),
            'menu_name' => __('Packages', 'tes_theme'),
            'all_items' => __('All Packages', 'tes_theme'),
            'view_item' => __('View Package', 'tes_theme'),
            'add_new_item' => __('Add New Package', 'tes_theme'),
            'add_new' => __('Add New', 'tes_theme'),
            'edit_item' => __('Edit Package', 'tes_theme'),
            'update_item' => __('Update Package', 'tes_theme'),
            'search_items' => __('Search Packages', 'tes_theme'),
            'not_found' => __('No Packages Found', 'tes_theme'),
            'not_found_in_trash' => __('No Packages Found in Trash', 'tes_theme'),
        ),
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'excerpt',
            'revisions',
        ),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'menu_icon' => 'dashicons-admin-generic',
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
