<?php
/**
 * Includes plugin functions.
 *
 * @link https://www.galaxyweblinks.com/
 * @since 1.0
 *
 * @package FormRefresherGravityForm
 */

/**
 * The class for the admin-specific functionality of the plugin.
 */
class FormRefresherGravityForm {

    /**
	 * Initialize the class sets its properties.
	 *
	 * @since 1.0
	 * @param string $root_file
	 */
    public function __construct() {
        if (class_exists('GFForms')) {
            $this->init();
        }
    }

    /**
	 * Initialize the all filter/action.
	 *
	 * @since  1.0
	 * @return null
	 */
    public function init(){
        add_filter( 'gform_enqueue_scripts', array( $this, 'gwrf_enqueue_form_scripts' ), 10, 2 );
        add_filter( 'gform_pre_render', array( $this, 'gwrf_register_init_scripts' ), 1, 2 );
        add_filter( 'gform_admin_pre_render', array( $this, 'gwrf_add_merge_tag_support' ) );
        add_filter( 'gform_replace_merge_tags', array( $this, 'gwrf_reload_form_replace_merge_tag' ), 10, 2 );
        add_filter( 'gform_form_settings', array( $this, 'gwrf_form_settings_ui' ), 10, 2 );
        add_filter( 'gform_pre_form_settings_save', array( $this, 'gwrf_save_form_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'gwrf_admin_enqueue' ) );
    }

    /**
     * enqueue_form_scripts function
     * @param [array] $form
     */
    public function gwrf_enqueue_form_scripts( $form ) {
        if( $this->gwrf_is_applicable_form( $form ) ) {
            wp_enqueue_script( 'gpreloadform', FRFGF_URL . 'assets/js/gform-reload.js', array( 'jquery' ), '1.0', true );
        }
    }

    /**
     * gwrf_register_init_scripts function
     *
     * @param [array] $form
     * @return $form
     */
    public function gwrf_register_init_scripts( $form ) {

        if( ! $this->gwrf_is_applicable_form( $form ) ) {
            return $form;
        }   

        $spinner_url  = apply_filters( "gform_ajax_spinner_url_{$form['id']}", apply_filters( 'gform_ajax_spinner_url', GFCommon::get_base_url() . '/images/spinner.svg', $form ), $form );
        $refresh_time = rgar( $form, '_refresh_time' );

        $args = array(
            'formId'      => $form['id'],
            'spinnerUrl'  => $spinner_url,
            'refreshTime' => $refresh_time ? $refresh_time : 0
        );

        $script = 'window.gwrf_' . $form['id'] . ' = new gwrf( ' . wp_json_encode( $args ) . ' );';
        $slug   = sprintf( 'gpreloadform_%d', $form['id'] );
        GFFormDisplay::add_init_script( $form['id'], $slug, GFFormDisplay::ON_PAGE_RENDER, $script );
        return $form;
    }

     /**
     * gwrf_reload_form_replace_merge_tag function
     *
     * @param $text 
     * @param $form
     * @return $text
     */
    public function gwrf_reload_form_replace_merge_tag( $text, $form ) {
        
        preg_match_all( '/{(reset_form):?([\p{L}\s\w.,!?\'"]*)}/miu', $text, $matches, PREG_SET_ORDER );
        
        if(empty($matches))
			return $text;

		foreach( $matches as $match ) {
			$link_text   = rgar( $match, 2 ) ? rgar( $match, 2 ) : esc_html( 'Reset Form', 'form-refresher-for-gravity-forms' );
			$reload_link = '<a href="" class="gws-reload-form gprl-reload-link">' . $link_text . '</a>';
			$text        = str_replace( rgar( $match, 0 ), $reload_link, $text );
		}

		return $text;
	}

    /**
     * gwrf_form_settings_ui function
     *
     * @param $form_settings
     * @param $form
     * @return $form_settings
     */
    public function gwrf_form_settings_ui( $form_settings, $form ) {
        
        $keys = array(
            'enable' => '_enable',
            'refresh_time' => '_refresh_time',
            'settings' => '_settings'
        );

        $display = ! rgar( $form, $keys['enable'] ) ? 'display:none;' : '';

        ob_start();
        ?>
        <tr class="gp-form-setting">
            <th>
                <label class="gform-settings-label" for="<?php echo esc_attr( $keys['enable'] ); ?>">
                    <?php esc_html_e( 'Automatically Reset Form', 'form-refresher-for-gravity-forms' ); ?>
                </label>
            </th>
            <td>

                <input type="checkbox" id="<?php echo esc_attr($keys['enable']); ?>" name="<?php echo esc_attr($keys['enable']); ?>" value="1" <?php checked( rgar( $form, $keys['enable'] ), true ); ?> />
                
                <label class="reset-form-label" for="<?php echo esc_attr($keys['enable']); ?>">
                    <span><?php esc_html_e( 'Automatically Reset Form', 'form-refresher-for-gravity-forms' ); ?></span>
                </label>
        

                <div id="<?php echo esc_attr($keys['settings']); ?>" style="<?php echo esc_attr($display); ?>">
                    <label class="label-refresh_time reset-form-label" for="<?php echo esc_attr($keys['refresh_time']); ?>">
                        <span><?php esc_html_e( 'Reset time after form submission (in seconds)', 'form-refresher-for-gravity-forms' ); ?></span>
                    </label>
                    <input type="number" id="<?php echo esc_attr($keys['refresh_time']); ?>" name="<?php echo esc_attr($keys['refresh_time']); ?>" value="<?php echo esc_attr(rgar( $form, $keys['refresh_time'] )); ?>">
                </div>

            </td>
        </tr>

        <?php
        $section_label = esc_html_e( 'Reloading the form without refreshing the page after the ajax submission.', 'form-refresher-for-gravity-forms' );
        $form_settings[$section_label] = array( '' => ob_get_clean() );

        return $form_settings;
    }

    /**
     * Enqueue JS/CSS scripts
     *
     */
    public function gwrf_admin_enqueue(){
        wp_enqueue_style( 'gwrf-admin-style', FRFGF_URL . 'assets/css/custom-style.css', array(), '1.0', 'all' );
        wp_enqueue_script( 'gwrf-admin-script', FRFGF_URL . 'assets/js/custom-script.js', array( 'jquery' ), '1.0', true );
    }

    /**
     * gwrf_save_form_settings function
     *
     * @param $form
     */
    public function gwrf_save_form_settings( $form ) {
        $form['_enable'] = rgpost( '_enable' );
        $form['_refresh_time'] = $form['_enable'] ? rgpost( '_refresh_time' ) : '';
        return $form;
    }

    /**
     * gwrf_add_merge_tag_support function
     * 
     * Adds field merge tags to the merge tag drop downs.
     * @param $form
     */
    function gwrf_add_merge_tag_support( $form ) {
        ?>
        <script type="text/javascript">
            if (typeof gform !== 'undefined') {
                gform.addFilter('gform_merge_tags', 'gprfMergeTags');
                function gprfMergeTags(mergeTags, elementId, hideAllFields, excludeFieldTypes, isPrepop, option) {
                    if (elementId === '_gform_setting_message') {
                        mergeTags.ungrouped.tags.push({
                            label: 'Reset Form Link',
                            tag: '{reset_form}'
                        });
                    }
                    return mergeTags;
                }
            }
        </script>
        <?php
        return $form;
    }

    /**
     * gwrf_is_applicable_form function
     * 
     * @param $form
     * @return true|false
     */
    public function gwrf_is_applicable_form( $form ) {

        // 3rd-party error can sometimes result in an invalid $form object
        if( ! rgar( $form, 'id' ) ) {
            return false;
        }

        if( rgar( $form, '_enable' ) ) {
            return true;
        }

        foreach( $form['confirmations'] as $confirmation ) {
            if( $this->gwrf_has_merge_tag( 'reset_form', rgar( $confirmation, 'message' ) ) ) {
                return true;
            }
        }

        return false;
    }

    /** gwrf_has_merge_tag function
    * 
    * @param $merge_tag 
    * @param $text
    */
    public function gwrf_has_merge_tag($merge_tag, $text) {
        preg_match('{' . $merge_tag . '([:]+)?([\w\s!?,\'"]+)?}', $text, $matches);
        return !empty($matches);
    }
}