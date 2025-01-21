<?php
/**
 * Theme Helper
 *
 */

namespace Theme;

class Helpers
{

  /**
   * Get terms by post types
   *
   * @return array
   */

  public static function get_terms_by_post_types($taxonomies, $args = array())
  {

    //Parse $args in case its a query string.
    $args = wp_parse_args($args);

    if (!empty($args['post_types'])) {
      $args['post_types'] = (array)$args['post_types'];
      add_filter('terms_clauses', function ($pieces, $tax, $args) {
        global $wpdb;

        // Don't use db count
        $pieces['fields'] .= ", COUNT(*) ";

        //Join extra tables to restrict by post type.
        $pieces['join'] .= " INNER JOIN $wpdb->term_relationships AS r ON r.term_taxonomy_id = tt.term_taxonomy_id
                                INNER JOIN $wpdb->posts AS p ON p.ID = r.object_id ";

        // Restrict by post type and Group by term_id for COUNTing.
        $post_types_str = is_array($args['post_types']) ? implode(',', $args['post_types']) : $args['post_types'];
        $pieces['where'] .= $wpdb->prepare(" AND p.post_type IN(%s) GROUP BY t.term_id", $post_types_str);

        remove_filter(current_filter(), __FUNCTION__);

        return $pieces;
      }, 10, 3);

    } // endif post_types set

    return get_terms($taxonomies, $args);
  }

  /**
   * Function to retrieve Read Time for any Post
   *
   * @return string
   */

  public static function show_read_time($post_id)
  {
    $post_content = get_post($post_id);
    $content = $post_content->post_content;
    $mycontent = do_blocks($content);
    $word = str_word_count(strip_tags($mycontent));
    $m = floor($word / 150);
    $est = $m . ' min' . ($m == 1 ? '' : 's');

    return $est . ' read';
  }

  /**
   * Function to retrieve Categories for any Post
   *
   * @return string
   */
  public static function get_post_terms($post_id, $taxonomy, $attributes = array())
  {
    if (!$post_id) {
      return false;
    }
    $terms = get_the_terms($post_id, $taxonomy);
    $attributes = (is_array($attributes) && !empty($attributes)) ? implode(' ', $attributes) : '';
    if (is_array($terms) && !empty($terms)) {
      foreach ($terms as $term) {
        $term_id = $term?->term_id;
        $term_link = get_term_link($term_id);
        if ($taxonomy === 'category' && $term_id == 1) {
          continue;
        } ?>
        <a class="wp-post-term"
           href="<?php echo esc_html($term_link); ?>" <?= $attributes ?>>
          <?= esc_html($term->name); ?>
        </a>
        <?php
      }
    }
  }

  /**
   * get_page_url_by_template_name
   *
   * @return string
   */
  public static function get_page_url_by_template_name($template_name)
  {
    $pages = get_posts([
      'post_type' => 'page',
      'post_status' => 'publish',
      'meta_query' => [
        [
          'key' => '_wp_page_template',
          'value' => $template_name . '.php',
          'compare' => '='
        ]
      ]
    ]);
    if (!empty($pages)) {
      foreach ($pages as $pages__value) {
        return get_permalink($pages__value->ID);
      }
    }

    return get_bloginfo('url');
  }

  /**
   *  get small_content
   *
   * @return string
   */
  public static function small_content($num = 11)
  {
    if (has_excerpt()) {
      the_excerpt();
    } else {
      echo wp_trim_words(get_the_content(), $num);
    }
  }

  /**
   *  Check Rgb Color That's Return number from 0 to 255
   *
   * @return string
   */
  public static function get_brightness($hex)
  {
    $hex = str_replace('#', '', $hex);
    $c_r = hexdec(substr($hex, 0, 2));
    $c_g = hexdec(substr($hex, 2, 2));
    $c_b = hexdec(substr($hex, 4, 2));

    return (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
  }

  /**
   *  get_post_image
   *
   * @return string
   */
  public static function get_post_thumbnail($post_ID, $size = 'medium', $attachment_attributes = array())
  {
    $post_thumbnail_id = $post_ID ? get_post_thumbnail_id($post_ID) : '';
    return Helpers::get_image($post_thumbnail_id, $size, $attachment_attributes) ?: '<img class="no-image" src="' . DH_IMAGE_DIR . '/no-image.jpg" alt="No Image">';

  }

  /**
   *  get_acf_image
   *
   * @return string
   */
  public static function get_image($attachment_id, $size = 'medium', $attachment_attributes = array())
  {
    if (!$attachment_id) {
      return false;
    }
    $attachment_mime_type = get_post_mime_type($attachment_id);
    if ($attachment_mime_type !== 'image/svg+xml') {
      return wp_get_attachment_image($attachment_id, $size, false, $attachment_attributes);
    } else {
      $attachment_url = wp_get_attachment_url($attachment_id);

      return $attachment_url ? file_get_contents($attachment_url) : null;
    }
  }

  /**
   *  get_paginate_links
   *
   * @return string
   */
  public static function get_paginate_links($the_query, $paged)
  {
    $left_pagination = '<svg   style="red" width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg" class="arrow-pagination">
    <path  d="M18.75 9C19.0664 9 19.3477 9.10547 19.5586 9.31641C20.0156 9.73828 20.0156 10.4766 19.5586 10.8984L14.7422 15.75L19.5586 20.5664C20.0156 20.9883 20.0156 21.7266 19.5586 22.1484C19.1367 22.6055 18.3984 22.6055 17.9766 22.1484L12.3516 16.5234C11.8945 16.1016 11.8945 15.3633 12.3516 14.9414L17.9766 9.31641C18.1875 9.10547 18.4688 9 18.75 9Z" fill="currentColor"/>
    <circle cx="16.5" cy="16.5" r="15.5" stroke="currentColor" stroke-width="2"/>
  </svg>';
    $right_pagination = '<svg  style="red" width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg" class="arrow-pagination">
    <path d="M14.25 23C13.9336 23 13.6523 22.8945 13.4414 22.6836C12.9844 22.2617 12.9844 21.5234 13.4414 21.1016L18.2578 16.25L13.4414 11.4336C12.9844 11.0117 12.9844 10.2734 13.4414 9.85156C13.8633 9.39453 14.6016 9.39453 15.0234 9.85156L20.6484 15.4766C21.1055 15.8984 21.1055 16.6367 20.6484 17.0586L15.0234 22.6836C14.8125 22.8945 14.5312 23 14.25 23Z" fill="currentColor"/>
    <circle cx="16.5" cy="16.5" r="15.5" stroke="currentColor" stroke-width="2"/>
</svg>';
    ?>
    <div class='wp-pagination'>
      <?php $big = 999999999;
      echo paginate_links(array(
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '/page/%#%',
        'current' => max(1, $paged),
        'prev_text' => $left_pagination,
        'next_text' => $right_pagination,
        'show_all' => false,

        'total' => $the_query->max_num_pages
      )); ?>
    </div>
    <?php
  }

  /**
   *  function for generating an embed link of an FB/Vimeo/Youtube Video
   * to get a valid url, we need to use the below code,
   * so the user can add a full video link
   * and we will generate the embed url
   *
   * @return string
   */
  public static function generate_video_embed_url($url)
  {
    $finalUrl = '';
    if (strpos($url, 'vimeo.com/') !== false) {
      //it is Vimeo video
      $videoId = explode("vimeo.com/", $url)[1];
      if (strpos($videoId, '&') !== false) {
        $videoId = explode("&", $videoId)[0];
      }
      $finalUrl .= 'https://player.vimeo.com/video/' . $videoId;
    } else {
      if (strpos($url, 'youtube.com/') !== false) {
        //it is Youtube video
        $videoId = explode("v=", $url)[1];
        if (strpos($videoId, '&') !== false) {
          $videoId = explode("&", $videoId)[0];
        }
        $finalUrl .= 'https://www.youtube.com/embed/' . $videoId;
      } else {
        if (strpos($url, 'youtu.be/') !== false) {
          //it is Youtube video
          $videoId = explode("youtu.be/", $url)[1];
          if (strpos($videoId, '&') !== false) {
            $videoId = explode("&", $videoId)[0];
          }
          $finalUrl .= 'https://www.youtube.com/embed/' . $videoId;
        } else {
          //Enter valid video URL
        }
      }
    }

    return $finalUrl;
  }

  /**
   * @param string $key
   * @param mixed $arr
   * @param bool $return_value
   *
   * @return mixed
   */
  public static function get_key_from_array(string $key, mixed $arr, bool $return_value = true): mixed
  {
    if (is_array($arr) && array_key_exists($key, $arr)) {
      return $return_value ? $arr[$key] : true;
    }

    return false;
  }

  public static function pretty_print($data)
  {
    echo "<pre style='font-size: 20px'>";
    print_r($data);
    echo "</pre>";
  }

  /**
   * add a custom image size to WordPress
   *
   * @param string $name Image size identifier.
   * @param int $width Optional. Image width in pixels. Default 0.
   * @param int $height Optional. Image height in pixels. Default 0.
   * @param bool|array $crop Optional. Image cropping behavior. If false, the image will be scaled (default),
   *                           If true, image will be cropped to the specified dimensions using center positions.
   *                           If an array, the image will be cropped using the array to specify the crop location.
   *                           Array values must be in the format: array( x_crop_position, y_crop_position ) where:
   *                               - x_crop_position accepts: 'left', 'center', or 'right'.
   *                               - y_crop_position accepts: 'top', 'center', or 'bottom'.
   *
   * @param int $image_dimensions_increased Optional. A number to increase the image with. Default 100.
   */

  public static function add_image_size(string $name, int $width = 0, int $height = 0, bool|array $crop = false, int $image_dimensions_increased = 100): void
  {
    add_image_size($name, $width + $image_dimensions_increased, $height + $image_dimensions_increased * $height / $width, $crop);
  }

  /**
   * Get all the registered image sizes along with their dimensions
   *
   * @return array $image_sizes The image sizes
   * @global array $_wp_additional_image_sizes
   * @link http://core.trac.wordpress.org/ticket/18947 Reference ticket
   *
   */
  public static function get_all_image_sizes()
  {
    global $_wp_additional_image_sizes;
    $image_sizes = array();
    $default_image_sizes = get_intermediate_image_sizes();

    foreach ($default_image_sizes as $size) {
      $image_sizes[$size]['width'] = intval(get_option("{$size}_size_w"));
      $image_sizes[$size]['height'] = intval(get_option("{$size}_size_h"));
      $image_sizes[$size]['crop'] = get_option("{$size}_crop") ? get_option("{$size}_crop") : false;
    }

    if (isset($_wp_additional_image_sizes) && count($_wp_additional_image_sizes)) {
      $image_sizes = array_merge($image_sizes, $_wp_additional_image_sizes);
    }

    return $image_sizes;
  }

  /**
   * Get short HTML, keeping specific tags.
   *
   * @param string $string The initial string to be truncated.
   * @param integer $max_len The maximum number of chars for the returned string.
   * @param string $end_string Trailing string.
   * @param string $allow_tags Preserve HTML tags.
   * @param bool $break Break the last word to a fixed length (defaults to false).
   *
   * @return string
   */
  public static function get_short_html(string $string, int $max_len = 80, string $end_string = '...', string $allow_tags = '<sup><sub><a><b><strong>', bool $break = false): string
  {
    if (empty($string) || mb_strlen($string) <= $max_len) {
      return $string;
    }

// Prepare the string for the match.
    $string = strip_shortcodes($string);
    $string = str_replace(array(
      "\r\n",
      "\r",
      "\n",
      "\t"
    ), ' ', $string); // phpcs:ignore
    $string = preg_replace('/\>/i', '> ', $string);
    $string = preg_replace('/\</i', ' <', $string);
    $string = preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
    $string = str_replace(' ', ' ', $string);
    $string = preg_replace('/\s+/', ' ', $string);
    $string = preg_replace('/\s\s+/', ' ', trim(strip_tags($string, $allow_tags)));
    $string = html_entity_decode($string);

// Check for HTML tags and plain text.
    $words_tags = preg_split('/(<[^>]*[^\/]>)/i', $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    $current_len = 0;
    $collection = [];
    $opened_tags = [];
    if (!empty($words_tags)) {
      foreach ($words_tags as $item) {
        if ($current_len >= $max_len) {
          // No need to continue.
          break;
        }
        if (substr_count($item, '<') && substr_count($item, '>')) {
          // This is a tag, let's collect it.
          $collection[] = $item;
          if (substr_count($item, '</')) {
            // This is an ending tag, let's remove the opened one.
            array_pop($opened_tags);
          } elseif (substr_count($item, '/>')) {
            // This is a self-closed tag, nothing to do.
            continue;
          } else {
            // This is an opening tag, let's add it to the opened list.
            $t = explode(' ', $item);
            array_push($opened_tags, substr($t[0], 1));
          }
        } else {
          // This is a plain text, let's assess the length and maybe collect it.
          $words = preg_split('/\s/i', $item, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
          if (!empty($words)) {
            foreach ($words as $word) {
              // Add + 1 as spaces count too.
              $new_lenght = $current_len + mb_strlen($word) + 1;
              if ($new_lenght <= $max_len) {
                $collection[] = $word . ' ';
              } else {
                if (true === $break) {
                  $diff = $max_len - $new_lenght - 1;
                  $collection[] = substr($word, 0, $diff) . ' ';
                }
              }
              $current_len = $new_lenght;
              if ($current_len >= $max_len) {
                break;
              }
            }
          }
        }
      }
    }

    $string = implode('', $collection);
    if (!empty($opened_tags)) {
      // There were some HTML tags opened that need to be closed.
      array_reverse($opened_tags);
      foreach ($opened_tags as $tag) {
        $string .= '</' . $tag;
      }
    }

// One final round of preparing the returned string.
    $string = trim($string);
    $string = preg_replace('/<[^\/>][^>]*><\/[^>]+>/', '', $string);
    $string = preg_replace('/(\s+\<\/+)+/', '</', $string);
    $string = preg_replace('/(\s+\,+)+/', ',', $string);
    $string = preg_replace('/(\s+\.+)+/', '.', $string);

// Maybe append the custom ending to the trimmed string.
    $string .= (!empty($end_string)) ? ' ' . $end_string : '';

    return $string;
  }
}
