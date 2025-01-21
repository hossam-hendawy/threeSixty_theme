/**
 * Form Refresher for Gravity Forms Backend JS
 */
(function($){
    // # UI EVENTS

    $( '#_enable' ).click(function(){
        toggleSettings( $(this).is(':checked') );
    });

    $( '#_refresh_time' ).keypress(function(e) {
        if (this.value.length == 0 && e.which == 48){
             return false;
        }
    });

    // # HELPERS
    function toggleSettings( isChecked ) {

        var enableCheckbox = jQuery( '#_enable' );
        var settingsContainer = jQuery( '#_settings' );

        if( isChecked ) {
            enableCheckbox.prop( 'checked', true );
            settingsContainer.slideDown('slow');
        } else {
            enableCheckbox.prop( 'checked', false );
            settingsContainer.slideUp('slow');
        }
    }

})(jQuery);