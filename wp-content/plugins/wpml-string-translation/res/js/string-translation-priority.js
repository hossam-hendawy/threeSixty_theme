/*jshint devel:true */
/*global jQuery, ajaxurl, get_checked_cbs */
var WPML_String_Translation = WPML_String_Translation || {};

WPML_String_Translation.ChangeTranslationPriority = function () {
    "use strict";

    var privateData = {};

    var init = function () {
        jQuery(function () {
            privateData.translation_priority_select = jQuery('#icl-st-change-translation-priority-selected');
            privateData.translation_priority_select.on('change', applyChanges);

            privateData.spinner = jQuery('.icl-st-change-spinner');
            privateData.spinner.detach().insertAfter(privateData.translation_priority_select);

            initializeSelect2();
        });
    };

    var applyChanges = function () {
        if(WPML_String_Translation.ExecBatchAction.isApplyBulkActionSelected()) {
            WPML_String_Translation.ExecBatchAction.run(
                wpml_st_exec_batch_action_data.countStringsInDomainWithDifferentPriority,
                wpml_st_exec_batch_action_data.changeTranslationPriorityBatchOfStringsInDomain,
                {
                    domain: jQuery('select[name="icl_st_filter_context"] option:selected').val(),
                    priority: privateData.translation_priority_select.val(),
                },
                {
                    beforeStart: function() {
                        jQuery('#icl-st-change-translation-priority-selected').attr('disabled', 'disabled');
                    },
                    onComplete: function(data) {
                        jQuery('#icl-st-change-translation-priority-selected').removeAttr('disabled');
                        window.location.reload();
                    },
                }
            );
            return;
        }

        var checkBoxValue;
        var data;
        var i;
        var checkboxes;
        var strings;

        privateData.spinner.addClass('is-active');

        strings = [];
        checkboxes = get_checked_cbs();
        for (i = 0; i < checkboxes.length; i++) {
            checkBoxValue = jQuery(checkboxes[i]).val();
            strings.push(checkBoxValue);
        }

        data = {
            action: 'wpml_change_string_translation_priority',
            wpnonce: jQuery('#wpml_change_string_translation_priority_nonce').val(),
            strings: strings,
            priority: privateData.translation_priority_select.val()
        };

        jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            data: data,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    window.location.reload(true);
                }
            }
        });
    };

    var initializeSelect2 = function () {
        privateData.translation_priority_select.wpml_select2({

            width:              'auto',
            dropdownCss:        {'z-index': parseInt(jQuery('.ui-dialog').css('z-index'), 10) + 100},
            dropdownAutoWidth:  true
        });
        jQuery('.js-change-translation-priority .wpml_select2-choice').addClass('button button-secondary').attr('disabled', 'true');
    };

    init();
};

WPML_String_Translation.change_translation_priority = new WPML_String_Translation.ChangeTranslationPriority();
