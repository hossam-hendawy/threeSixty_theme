<?php
/**
 * Plugin Name: Register new post types
 * Version: 0.0.1
 * Description: Register new post types
 * Author: DIGIHIVE
 * Author URI: https://digihive.dev/
 * Text Domain: register entities
 **/

namespace Register_Entities;
require_once 'entities/Entity.php';

global $entities;
$entities = [
    'Testimonials',
    'Packages',
    'Services',
    ];

array_map(function ($entityName) {
  require dirname(__FILE__) . '/entities/' . $entityName . '.php';
}, $entities);

add_action('init', function () {
  global $entities;
  
  array_map(function ($entityName) {
    $className = __NAMESPACE__ . '\\' . $entityName;
    $className::init();
  }, $entities);
  
  
});

add_action("admin_init", function () {
  global $entities;
  
  array_map(function ($entityName) {
    $className = __NAMESPACE__ . '\\' . $entityName;
    $className::init_admin();
    $className::add_meta_box();
  }, $entities);
});

add_action('add_meta_boxes_page', function () {
  global $entities;
  
  array_map(function ($entityName) {
    $className = __NAMESPACE__ . '\\' . $entityName;
    $className::add_page_meta_box();
  }, $entities);
});

add_action('save_post', function () {
  global $entities;
  
  array_map(function ($entityName) {
    $className = __NAMESPACE__ . '\\' . $entityName;
    $className::save_post();
  }, $entities);
});