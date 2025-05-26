jQuery(function ($) {
    function saveFormData() {
        var $form = $(this).closest('.quform-form'),
            id = $form.data('quform').options.id,
            cookieDays = 2,
            now = new Date(),
            expires = new Date(now.getTime() + cookieDays * 24 * 3600 * 1000);
 
        document.cookie = 'quform_' + id + '=' + encodeURIComponent($form.formSerialize()) + ';expires=' + expires.toUTCString();
    }
 
    $('.quform input[type="text"], .quform input[type="email"], .quform textarea, .quform input[type="password"]').on('blur', saveFormData);
    $('.quform select, .quform input[type="checkbox"], .quform input[type="radio"]').on('change', saveFormData);
    $('.quform-field-date-enhanced, .quform-field-time-enhanced').on('change', function (e) {
        setTimeout(function () {
            saveFormData.call(e.target);
        }, 4);
    });
});



