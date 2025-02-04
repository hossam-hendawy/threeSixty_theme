<?php

namespace Register_Entities;

class Faqs extends Entity
{
  public static function init()
  {
    // Register Custom Post Type
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
        'supports' => array(
            'title',
            'editor',
            'revisions'
        ),
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
    
    // Register Taxonomy
    self::register_faq_taxonomy();
  }
  
  private static function register_faq_taxonomy()
  {
    register_taxonomy('faq_category', ['faqs'], [
        'hierarchical' => true,
        'labels' => array(
            'name' => _x('FAQ Categories', 'taxonomy general name', 'tes_theme'),
            'singular_name' => _x('FAQ Category', 'taxonomy singular name', 'tes_theme'),
            'search_items' => __('Search FAQ Categories', 'tes_theme'),
            'all_items' => __('All FAQ Categories', 'tes_theme'),
            'parent_item' => __('Parent FAQ Category', 'tes_theme'),
            'parent_item_colon' => __('Parent FAQ Category:', 'tes_theme'),
            'edit_item' => __('Edit FAQ Category', 'tes_theme'),
            'update_item' => __('Update FAQ Category', 'tes_theme'),
            'add_new_item' => __('Add New FAQ Category', 'tes_theme'),
            'new_item_name' => __('New FAQ Category Name', 'tes_theme'),
            'menu_name' => __('FAQ Categories', 'tes_theme'),
        ),
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'faq-category'],
    ]);
  }
}
