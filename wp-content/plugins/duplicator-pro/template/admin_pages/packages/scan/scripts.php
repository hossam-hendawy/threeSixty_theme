<?php

/**
 * @package Duplicator
 */

use Duplicator\Ajax\ServicesPackage;
use Duplicator\Controllers\PackagesPageController;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$alert2           = new DUP_PRO_UI_Dialog();
$alert2->height   = 485;
$alert2->width    = 650;
$alert2->title    = __('Copy Quick Filter Paths', 'duplicator-pro');
$alert2->boxClass = 'arc-paths-dlg';
$alert2->message  = "";
$alert2->initAlert();

$alert3          = new DUP_PRO_UI_Dialog();
$alert3->title   = __('WARNING!', 'duplicator-pro');
$alert3->message = __('Manual copy of selected text required on this browser.', 'duplicator-pro');
$alert3->initAlert();

$alert4          = new DUP_PRO_UI_Dialog();
$alert4->title   = $alert3->title;
$alert4->message = __('Error applying filters.  Please go back to Step 1 to add filter manually!', 'duplicator-pro');
$alert4->initAlert();

$messageText = $tplMng->render('admin_pages/packages/scan/error_message', [], false);
?>
<script>
    jQuery(document).ready(function($)
    {
        var large_tree = $('#hb-files-large-jstree').length ? $('#hb-files-large-jstree') : null;

        Handlebars.registerHelper('stripWPRoot', function(path) {
            return  path.replace('<?php echo esc_js(duplicator_pro_get_home_path()) ?>', '');
        });

        Handlebars.registerHelper('ifAllOr', function (v1, v2, v3, options) {
            if (v1 || v2 || v3) {
                return options.fn(this);
            }

            return options.inverse(this);
        });

        Handlebars.registerHelper('compare', function (v1, operator, v2, options) {
            'use strict';
            var operators = {
                '==': v1 == v2 ? true : false,
                '===': v1 === v2 ? true : false,
                '!=': v1 != v2 ? true : false,
                '!==': v1 !== v2 ? true : false,
                '>': v1 > v2 ? true : false,
                '>=': v1 >= v2 ? true : false,
                '<': v1 < v2 ? true : false,
                '<=': v1 <= v2 ? true : false,
                '||': v1 || v2 ? true : false,
                '&&': v1 && v2 ? true : false
            }
            if (operators.hasOwnProperty(operator)) {
                if (operators[operator]) {
                    return options.fn(this);
                }
                return options.inverse(this);
            }
            return console.error('Error: Expression "' + operator + '" not found');
        });

        //Opens a dialog to show scan details
        DupPro.Pack.filesOff = function (dir)
        {
            var $checks = $(dir).parent('div.directory').find('div.files input[type="checkbox"]');
            $(dir).is(':checked')
                ? $.each($checks, function() {
                    $(this).attr(
                        {
                            disabled : true,
                            checked : false, 
                            title : "<?php esc_html_e('Directory applied filter set.', 'duplicator-pro');?>"
                        }
                    );
                })
                : $.each($checks, function() {$(this).removeAttr('disabled checked title');});
        }

        DupPro.Pack.FilterButton = {
            loading : function (btn) {
                $(btn).html('<i class="fas fa-circle-notch fa-spin"></i> <?php esc_html_e('Initializing Please Wait...', 'duplicator-pro');?>');
                $(btn).prop('disabled' , true);
                $('#dup-build-button').prop('disable' , true);
            },
            reset : function (btn) {
                $(btn).html('<i class="fa fa-filter fa-sm"></i> <?php esc_html_e("Add Filters &amp; Rescan", "duplicator-pro");?>');
                $(btn).prop('disabled' , true);
                $('#dup-build-button').prop('disable' , false);
            }
        };

        //Opens a dialog to show scan details
        DupPro.Pack.showPathsDlg = function (type)
        {
            var filters = DupPro.Pack.getFiltersLists(type);
            var dirFilters  = filters.dir;
            var fileFilters = filters.file;

            var $dirs  = $('#dup-archive-paths textarea.path-dirs');
            var $files = $('#dup-archive-paths textarea.path-files');
            (dirFilters.length > 0)
               ? $dirs.text(dirFilters.join(";\n"))
               : $dirs.text("<?php esc_html_e('No directories have been selected!', 'duplicator-pro');?>");

            (fileFilters.length > 0)
               ? $files.text(fileFilters.join(";\n"))
               : $files.text("<?php esc_html_e('No files have been selected!', 'duplicator-pro');?>");

            $('.arc-paths-dlg').html($('#dup-archive-paths').html());
            <?php $alert2->showAlert(); ?>

            return;
        };

        //Toggles a directory path to show files
        DupPro.Pack.toggleDirPath = function(item)
        {
            var $dir   = $(item).parents('div.directory');
            var $files = $dir.find('div.files');
            var $arrow = $dir.find('i.dup-nav');
            if ($files.is(":hidden")) {
                $arrow.addClass('fa-caret-down').removeClass('fa-caret-right');
                $files.show();
            } else {
                $arrow.addClass('fa-caret-right').removeClass('fa-caret-down');
                $files.hide(250);
            }
        }

        //Toggles a directory path to show files
        DupPro.Pack.toggleAllDirPath = function(chkBox, toggle)
        {
             (toggle == 'hide')
                ? $('#hb-files-large-jstree').jstree().close_all()
                : $('#hb-files-large-jstree').jstree().open_all();
        }

        DupPro.Pack.copyText = function(btn, query)
        {
            $(query).select();
             try {
               document.execCommand('copy');
               $(btn).css({color: '#fff', backgroundColor: 'green'});
               $(btn).text("<?php esc_html_e('Copied to Clipboard!', 'duplicator-pro');?>");
             } catch(err) {
               <?php $alert3->showAlert(); ?>
             }
        }

        DupPro.Pack.getFiltersLists = function(type) {
            var result = {
                'dir' : [],
                'file' : []
            };

            switch(type){
                case 'large':
                    console.log(large_tree);
                    if (large_tree) {
                        $.each(large_tree.jstree("get_checked",null,true), function(index, value){
                            var original = large_tree.jstree(true).get_node(value).original;
                            if (original.type.startsWith('folder')) {
                                result.dir.push(original.fullPath);
                            } else {
                                result.file.push(original.fullPath);
                            }
                        });
                    }
                    break;
                case 'addon':
                    var id = '#hb-addon-sites-result';
                    if ($(id).length) {
                        $(id + " input[name='dir_paths[]']:checked").each(function()  {result.dir.push($(this).val());});
                        $(id + " input[name='file_paths[]']:checked").each(function() {result.file.push($(this).val());});
                    }
                    break;
            }
            return result;
        };

        DupPro.Pack.applyFilters = function(btn, type)
        {
            var filterButton = btn;
            var filters = DupPro.Pack.getFiltersLists(type);
            var dirFilters  = filters.dir;
            var fileFilters = filters.file;

            if (dirFilters.length === 0 && fileFilters.length === 0) {
                alert('No filter selected');
                return false;
            }

            dirFilters = dirFilters.map( function (path) {
                return path.slice(-1) !== '\/' ? path + '\/' : path;
            });

            DupPro.Pack.FilterButton.loading(filterButton);
        
            var data = {
                action: 'duplicator_add_quick_filters',
                nonce: <?php echo json_encode(wp_create_nonce('duplicator_add_quick_filters')); ?>,
                dir_paths : dirFilters.join(";"),
                file_paths : fileFilters.join(";")
            };

            $.ajax({
                type: "POST",
                cache: false,
                dataType: 'json',
                url: ajaxurl,
                timeout: 100000,
                data: data,
                complete: function() { },
                success:  function(data) {
                    DupPro.Pack.reRunScanner(function () {
                        DupPro.Pack.FilterButton.reset(filterButton);
                        DupPro.Pack.fullLoadButtonInit();
                    });
                },
                error: function(data) {
                    console.log(data);
                    <?php $alert4->showAlert(); ?>
                }
            });

            return false;
        };
        
        DupPro.Pack.treeContextMenu = function (node) {
            var items = {};
            if (node.type.startsWith('folder')) {
                items = {
                    selectAll: { 
                        label: "<?php esc_html_e('Select all childs files and folders', 'duplicator-pro'); ?>",
                        action: function (obj) {
                            $(obj.reference).parent().find('> .jstree-children .warning-node > .jstree-anchor:not(.jstree-checked) .jstree-checkbox')
                                .each(function ()  {
                                    var _this = $(this);
                                    if (_this.parents('.selected-node').length === 0) {
                                        _this.trigger('click');
                                    }
                                });
                        }
                    },
                    selectAllFiles: { 
                        label: "<?php esc_html_e('Select only all childs files', 'duplicator-pro'); ?>",
                        action: function (obj) {
                            $(obj.reference).parent().find('> .jstree-children .file-node.warning-node > .jstree-anchor:not(.jstree-checked) .jstree-checkbox')
                                .each(function ()  {
                                    var _this = $(this);
                                    if (_this.parents('.selected-node').length === 0) {
                                        _this.trigger('click');
                                    }
                                });
                        }
                    },
                    unselectAll: { 
                        label: "<?php esc_html_e('Unselect all childs elements', 'duplicator-pro'); ?>",
                        action: function (obj) {
                            $(obj.reference).parent().find('> .jstree-children .jstree-node > .jstree-anchor.jstree-checked .jstree-checkbox').trigger('click');
                        }
                    }
                };
            }
            return items;
        };
        
        DupPro.Pack.getTreeFolderUrlData =  function(folder, excludeList) {
            if (excludeList === undefined) {
                excludeList = [];
            }
            
            return {
                'nonce' : <?php echo json_encode(wp_create_nonce('duplicator_pro_get_folder_children')); ?>,
                'action' : 'duplicator_pro_get_folder_children',
                'folder' : folder,
                'exclude' : excludeList
            };
        };
        
        DupPro.Pack.getTreeFolderUrl = function(folder, excludeList) {
            return ajaxurl + '?' + $.param(DupPro.Pack.getTreeFolderUrlData(folder, excludeList));
        };
        
        DupPro.Pack.fullLoadNodes = null;

        DupPro.Pack.fullLoadFolder = function(tree, index, sectionContainer) {
            if (Array.isArray(DupPro.Pack.fullLoadNodes) && index < DupPro.Pack.fullLoadNodes.length) {
                var parent = DupPro.Pack.fullLoadNodes[index];
                if (index === 0 && sectionContainer) {
                    sectionContainer.append('<div class="tree-loader" >' + 
                            '<div class="container-wrapper" >' + 
                            '<i class="fa fa-cog fa-lg fa-spin"></i> <span></span>' +
                            '</div>' + 
                            '</div>');
                }
                sectionContainer.find('.tree-loader span').text("<?php echo esc_js(__('Loading ', 'duplicator-pro')) ?>" + parent.original.fullPath);
            } else {
                DupPro.Pack.fullLoadNodes = null;
                if (sectionContainer) {
                    sectionContainer.find('.tree-loader').remove();
                }
                return;
            }
            var excludeList = [];
            
            var parentClass = parent.li_attr.class;        
            if (parentClass.indexOf('root-node') !== -1 && parentClass.indexOf('no-warnings') !== -1) {
                tree.delete_node(parent.children[0]);
            } else {
                for (i = 0; i < parent.children.length; i++) { 
                    excludeList.push(tree.get_node(parent.children[i]).original.fullPath.replace(/^.*[\\\/]/, ''));
                }
            }
            var data = DupPro.Pack.getTreeFolderUrlData(parent.original.fullPath, excludeList);
            $.ajax({
                type: "GET",
                cache: false,
                data: data,
                dataType: "json",
                url: ajaxurl,
                timeout: 100000,
                //data: data,
                complete: function() { },
                success:  function(data) {
                    try {
                        for (i = 0; i < data.length; i++) {
                            tree.create_node(parent, data[i]);
                        }
                        DupPro.Pack.fullLoadFolder(tree, index + 1, sectionContainer);
                    } catch(err) {
                        console.error(err);
                        console.error('JSON parse failed for response data: ' + respData);
                        console.log(respData);
                        <?php $alert4->showAlert(); ?>
                        return false;
                    }
                },
                error: function(data) {
                    console.log(data);
                    <?php $alert4->showAlert(); ?>
                }
            });
        };
        
        DupPro.Pack.fullLoadButtonInit = function () {
            $('.tree-full-load-button')
                    .removeClass('isLoaded')
                    .removeClass('dup-tree-hide-all')
                    .addClass('dup-tree-show-all')
                    .text("<?php echo esc_js(__('show all', 'duplicator-pro')); ?>")
                    .off()
                    .click(function () {   
                        var sectionContainer = $(this).closest('.dup-tree-section').find('> .container');
                        var cObj = $(this);
                        var domTree = sectionContainer.find(".dup-tree-main-wrapper");
                        var tree = domTree.jstree(true);

                        if (cObj.hasClass('dup-tree-show-all')) {
                            cObj.removeClass('dup-tree-show-all')
                                .addClass('dup-tree-hide-all')
                                .text("<?php echo esc_js(__('show warning only', 'duplicator-pro')) ?>");
                            if (!cObj.hasClass('isLoaded')) {
                                cObj.addClass('isLoaded');
                                DupPro.Pack.fullLoadNodes = [];
                                domTree.find(".folder-node[data-full-loaded=false]").each(function () {
                                    var parent = tree.get_node($(this));
                                    if (parent.state.loaded === false ) {
                                        // If loaded it is false the folder has never been opened then it will be loaded by jstree if it is opened.
                                        return;
                                    }
                                    DupPro.Pack.fullLoadNodes.push(parent);
                                });

                                if (DupPro.Pack.fullLoadNodes.length) {
                                    DupPro.Pack.fullLoadFolder(tree, 0, sectionContainer);
                                } else {
                                    DupPro.Pack.fullLoadNodes = null;
                                }
                            } else {
                                domTree.find(".root-node .jstree-node:not(.warning-childs):not(.warning-node)").each(function () {
                                    // don't use the tree functions show_node and hide_node are too slow.
                                    $(this).removeClass('jstree-hidden');
                                });   
                            }
                        } else {
                            cObj.removeClass('dup-tree-hide-all').addClass('dup-tree-show-all')
                                .text("<?php echo esc_js(__('show all', 'duplicator-pro')); ?>");
                            domTree.find(".root-node .jstree-node:not(.warning-node):not(.warning-childs)").each(function () {
                                // don't use the tree functions show_node and hide_node are too slow.
                                $(this).addClass('jstree-hidden');
                            });
                        }

                        // recalculate the last child manually
                        domTree.find(".jstree-children").each(function () {
                            $(this).find('> li:not(.jstree-hidden)').removeClass('jstree-last').last().addClass('jstree-last');
                        });

                    });
        };
        
        DupPro.Pack.initTree = function(tree , data , filterBtn) {
            var treeObj = tree;
            var nameData =  data;
            console.log('nameData' ,nameData);

            treeObj.jstree('destroy');
            treeObj.jstree({
                'core' : {
                    "check_callback": true,
                    'cache' : false,
                    //'data' : nameData,
                    "themes": {
                        "name": "snap",
                        "dots": true,
                        "icons": true,
                        "stripes" : true,
                    },
                    'data' : {
                        'url' : function (node) {                        
                            var folder = (node.id === '#') ? '' : node.original.fullPath;
                            return DupPro.Pack.getTreeFolderUrl(folder);
                        },
                        'data' : function (node) {
                            return { 'id' : node.id };
                        }
                    }
                },
                'types': {
                    "folder": {
                        "icon": "jstree-icon jstree-folder",
                        "li_attr" : {
                          "class" : 'folder-node'
                      }
                  },
                  "file": {
                      "icon": "jstree-icon jstree-file",
                      "li_attr" : {
                          "class" : 'file-node'
                      }
                  },
                  "info-text": {
                      "icon": "jstree-noicon",
                      "li_attr" : {
                          "class" : 'info-node'
                      }
                  }
              },
              "checkbox" : {
                  // a boolean indicating if checkboxes should be visible (can be changed at a later time using 
                  // `show_checkboxes()` and `hide_checkboxes`). Defaults to `true`.
                  visible               : true,
                  // a boolean indicating if clicking anywhere on the node should act as clicking on the checkbox. Defaults to `true`.
                  three_state           : false,
                  // a boolean indicating if clicking anywhere on the node should act as clicking on the checkbox. Defaults to `true`.
                  whole_node            : false,
                  keep_selected_style   : false, // a boolean indicating if the selected style of a node should be kept, or removed. Defaults to `true`.
                  // This setting controls how cascading and undetermined nodes are applied.
                  // If 'up' is in the string - cascading up is enabled, if 'down' is in the string - cascading down is enabled,
                  // if 'undetermined' is in the string - undetermined nodes will be used.
                  // If `three_state` is set to `true` this setting is automatically set to 'up+down+undetermined'. Defaults to ''.
                  cascade               : '',
                  // This setting controls if checkbox are bound to the general tree selection or 
                  // to an internal array maintained by the checkbox plugin. Defaults to `true`, only set to `false` if you know exactly what you are doing.
                  tie_selection     : false,
                  cascade_to_disabled : false, // This setting controls if cascading down affects disabled checkboxes
                  cascade_to_hidden   : false   //This setting controls if cascading down affects hidden checkboxes
              },
              "contextmenu" : {
                  "items" : DupPro.Pack.treeContextMenu
              },
              "plugins" : [
                  "checkbox",
                  "contextmenu",
                  "types",
                  //"dnd",
                  //"massload",
                  //"search",
                  //"sort",
                  //"state",
                  //"types",
                  //"unique",
                  //"wholerow",
                  "changed",
                  //"conditionalselect"
              ]
          }).on('check_node.jstree', function (e, data) {
                treeObj.find('#' + data.node.id).addClass('selected-node');
                filterBtn.prop("disabled", false);
          }).on('uncheck_node.jstree' , function (e, data) {
                treeObj.find('#' + data.node.id).removeClass('selected-node');
                if (treeObj.jstree("get_selected").length === 0) {
                    filterBtn.prop("disabled", true);
                }
          }).on('ready.jstree', function () {
                // insert data
                tree.jstree(true).create_node(null, nameData);
          });

        };

        DupPro.Pack.initArchiveFilesData = function(data)
        {
            //TOTAL SIZE
            $('#data-arc-size1').text(data.ARC.Size || errMsg);
            $('#data-arc-size2').text(data.ARC.Size || errMsg);
            $('#data-arc-files').text(data.ARC.FileCount || errMsg);
            $('#data-arc-dirs').text(data.ARC.DirCount || errMsg);
            $('#data-arc-fullcount').text(data.ARC.FullCount || errMsg);

            //LARGE FILES
            if ($("#hb-files-large-result").length) {
                DupPro.Pack.initTree(
                        large_tree ,
                        data.ARC.FilterInfo.TreeSize ,
                        $("#hb-files-large-result .duplicator-pro-quick-filter-btn")
                        );
            }

            //ADDON SITES
            if ($("#hb-addon-sites").length) {
                var template = $('#hb-addon-sites').html();
                var templateScript = Handlebars.compile(template);
                var html = templateScript(data);
                $('#hb-addon-sites-result').html(html);
            }

            //UNREADABLE FILES
            if ($("#unreadable-files").length) {
                var template = $('#unreadable-files').html();
                var templateScript = Handlebars.compile(template);
                var html = templateScript(data);
                $('#unreadable-files-result').html(html);
            }


            //SCANNER DETAILS: Dirs
            if ($("#hb-filter-file-list").length) {
                var template = $('#hb-filter-file-list').html();
                var templateScript = Handlebars.compile(template);
                var html = templateScript(data);
                $('div.hb-filter-file-list-result').html(html);
            }
            
            //NETWORK SITES
            if ($("#hb-filter-network-sites").length) {
                var template = $('#hb-filter-network-sites').html();
                var templateScript = Handlebars.compile(template);
                var html = templateScript(data);
                $('#hb-filter-network-sites-result').html(html);
            }
          
            //MIGRATE PACKAGE
            if ($("#hb-migrate-package-result").length) {
                var template = $('#hb-migrate-package-result').html();
                var templateScript = Handlebars.compile(template);
                var html = templateScript(data);
                $('#migrate-package-result').html(html);
            }

            //Security Plugins
            if ($("#hb-dup-security-plugins").length) {
                var template = $('#hb-dup-security-plugins').html();
                var templateScript = Handlebars.compile(template);
                var html = templateScript(data);
                $('#dup-security-plugins').html(html);
            }

            //SHOW CREATE
            if ($("#hb-showcreatefunc-result").length) {
                var template = $('#hb-showcreatefunc-result').html();
                var templateScript = Handlebars.compile(template);
                var html = templateScript(data);
                $('#showcreatefunc-package-result').html(html);
            }

            //TRIGGERS
            if ($("#hb-triggers-result").length) {
                var template = $('#hb-triggers-result').html();
                var templateScript = Handlebars.compile(template);
                var html = templateScript(data);
                $('#triggers-result').html(html);
            }

            //MYSQLDUMP LIMIT
            if ($("#hb-mysqldump-limit-result").length) {
            var template = $('#hb-mysqldump-limit-result').html();
            var templateScript = Handlebars.compile(template);
            var html = templateScript(data);
            $('#mysqldump-limit-result').html(html);
            }

            DuplicatorTooltip.reload();
        };
        
        DupPro.Pack.fullLoadButtonInit();

        $("#form-duplicator").on('change', "#hb-files-large-result input[type='checkbox'], #hb-addon-sites-result input[type='checkbox']", function() {       
            if ($("#hb-addon-sites-result input[type='checkbox']:checked").length) {
                var addon_disabled_prop = false;
            } else {
                var addon_disabled_prop = true;
            }
            $("#hb-addon-sites-result .duplicator-pro-quick-filter-btn").prop("disabled", addon_disabled_prop);         
        });
    });

    jQuery(document).ready(function ($)
    {
        DupPro.Pack.WebServiceStatus = {
            Pass: <?php echo json_encode(ServicesPackage::EXEC_STATUS_PASS); ?>,
            Warn: <?php echo json_encode(ServicesPackage::EXEC_STATUS_WARN); ?>, //deprecated
            Error: <?php echo json_encode(ServicesPackage::EXEC_STATUS_FAIL); ?>,
            MoreToScan: <?php echo json_encode(ServicesPackage::EXEC_STATUS_MORE_TO_SCAN); ?>,
            ScheduleRunning: <?php echo json_encode(ServicesPackage::EXEC_STATUS_SCHEDULE_RUNNING); ?>
        }

        let errorMessage     = <?php echo json_encode($messageText); ?>;
        let scanTimeoutInSec = <?php echo json_encode(DUP_PRO_Global_Entity::getInstance()->php_max_worker_time_in_sec); ?>;
        let scanTimeout      = (scanTimeoutInSec + 10) * 1000; // Add 10 seconds to the backend timeout

        DupPro.Pack.runScanner = function (callbackOnSuccess, firstChunk = false) {
            Duplicator.Util.ajaxWrapper(
                {
                    action: 'duplicator_pro_package_scan',
                    firstChunk: firstChunk,
                    nonce: <?php echo json_encode(wp_create_nonce('duplicator_pro_package_scan')); ?>
                },
                function (result, data, funcData, textStatus, jqXHR) {
                    var status = funcData.Status || 3;
                    var message = funcData.Message ||
                        "Unable to read JSON from service. <br/> See: /wp-admin/admin-ajax.php?action=duplicator_pro_package_scan";

                    if (status == DupPro.Pack.WebServiceStatus.MoreToScan) {
                        console.log('Continue with next scan chunk...');
                        DupPro.Pack.runScanner(callbackOnSuccess);
                        return;
                    } 
                    
                    // Scan finished, parse results
                    if (status == DupPro.Pack.WebServiceStatus.Pass) {
                        DupPro.Pack.loadScanData(funcData);
                        if (typeof callbackOnSuccess === "function") {
                            callbackOnSuccess(funcData);
                        }
                        $('.dup-button-footer').show();
                    } else if (status == DupPro.Pack.WebServiceStatus.ScheduleRunning) {
                        console.log('Scan is already running...');
                        window.location.href = <?php echo json_encode(PackagesPageController::getInstance()->getPackageBuildS1Url()); ?>;
                    } else {
                        $('.dup-progress-bar-area, #dup-build-button').hide();
                        $('#dup-msg-error-response-status').html(status);
                        $('#dup-msg-error-response-text').html(message + errorMessage);
                        $('#dup-msg-error').show();
                        $('.dup-button-footer').show();
                    }
                },
                function (result, data, funcData, textStatus, jqXHR) {
                    var status = data.status + ' -' + data.statusText;
                    $('.dup-progress-bar-area, #dup-build-button').hide();
                    $('#dup-msg-error-response-status').html(status)
                    $('#dup-msg-error-response-text').html(data.message + errorMessage);
                    $('#dup-msg-error, .dup-button-footer').show();
                    console.log(data);
                },
                {
                    showProgress: false,
                    timeout: scanTimeout
                }
            );
        }

        DupPro.Pack.reRunScanner = function (callbackOnSuccess)
        {
            $('#dup-msg-success,#dup-msg-error,.dup-button-footer,#dpro-confirm-area').hide();
            $('#dpro-confirm-check').prop('checked', false);
            $('.dup-progress-bar-area').show();
            $('#dpro-scan-warning-continue').hide();
            DupPro.Pack.runScanner(callbackOnSuccess, true);
        }

        DupPro.Pack.loadScanData = function (data)
        {
            try {
                var errMsg = "unable to read";
                $('.dup-progress-bar-area').hide();
                //****************
                // BRAND
                // #data-srv-brand-check
                // #data-srv-brand-name
                // #data-srv-brand-note

                $("#data-srv-brand-name").text(data.SRV.Brand.Name);
                if (data.SRV.Brand.LogoImageExists) {
                    $("#data-srv-brand-note").html(data.SRV.Brand.Notes);
                } else {
                    $("#data-srv-brand-note")
                        .html(`<?php
                            esc_html_e(
                                "WARNING! Logo images no longer can be found inside brand. Please edit this brand and place new images.
                                After that you can build your Backup with this brand.",
                                "duplicator-pro"
                            ); ?>`
                        );
                }

                //****************
                //REPORT
                var base = $('#data-rpt-scanfile').attr('href');
                $('#data-rpt-scanfile').attr('href', base + '&scanfile=' + data.RPT.ScanFile);
                $('#data-rpt-scantime').text(data.RPT.ScanTime || 0);

                DupPro.Pack.initArchiveFilesData(data);
                DupPro.Pack.setScanStatus(data);

                //Addon Sites
                if (data.ARC.FilterInfo.Dirs.AddonSites !== undefined && data.ARC.FilterInfo.Dirs.AddonSites.length > 0) {
                    $("#addonsites-block").show();
                }
                $('#dup-msg-success').show();

                //****************
                //DATABASE
                var html = "";
                var DB_TableRowMax = <?php echo (int) DUPLICATOR_PRO_SCAN_DB_TBL_ROWS; ?>;
                var DB_TableSizeMax = <?php echo (int) DUPLICATOR_PRO_SCAN_DB_TBL_SIZE; ?>;
                if (data.DB.DBExcluded && data.DB.Status.Success) {
                    $('#data-db-size1').text(data.DB.Size || errMsg);
                } else if (data.DB.Status.Success) {
                    $('#data-db-size1').text(data.DB.Size || errMsg);
                    $('#data-db-size2').text(data.DB.Size || errMsg);
                    $('#data-db-rows').text(data.DB.Rows || errMsg);
                    $('#data-db-tablecount').text(data.DB.TableCount || errMsg);
                    //Table Details
                    if (data.DB.TableList == undefined || data.DB.TableList.length == 0) {
                        html = '<?php esc_html_e("Unable to report on any tables", 'duplicator-pro') ?>';
                    } else {
                        $.each(data.DB.TableList, function (i) {
                            html += '<b>' + i + '</b><br/>';
                            html += '<table><tr>';
                            $.each(data.DB.TableList[i], function (key, val) {
                                switch (key) {
                                    case 'Case':
                                        color = (val == 1) ? 'maroon' : 'black';
                                        html += '<td style="color:' + color + '"><?php echo esc_js(__('Uppercase:', 'duplicator-pro')) ?> ' + val + '</td>';
                                        break;
                                    case 'Rows':
                                        color = (val > DB_TableRowMax) ? 'red' : 'black';
                                        html += '<td style="color:' + color + '"><?php echo esc_js(__('Rows:', 'duplicator-pro')) ?> ' + val + '</td>';
                                        break;
                                    case 'USize':
                                        color = (parseInt(val) > DB_TableSizeMax) ? 'red' : 'black';
                                        html += '<td style="color:' + color + '">';
                                        html += '<?php echo esc_js(__('Size:', 'duplicator-pro')) ?> ' + data.DB.TableList[i]['Size'];
                                        html += '</td>';
                                        break;
                                }
                            });
                            html += '</tr></table>';
                        });
                    }
                    $('#data-db-tablelist').html(html);
                } else {
                    html = '<?php esc_html_e("Unable to report on database stats", 'duplicator-pro') ?>';
                    $('#dup-scan-db').html(html);
                }

                var isWarn = false;
                for (key in data.ARC.Status) {
                    if (!data.ARC.Status[key]) {
                        isWarn = true;
                    }
                }

                if (!isWarn) {
                    if (!data.DB.Status.Size) {
                        isWarn = true;
                    }
                }

                if (!isWarn && !data.DB.Status.Rows) {
                    isWarn = true;
                }

                if (!isWarn && !data.SRV.PHP.ALL) {
                    isWarn = true;
                }

                if (!isWarn && (data.SRV.WP.version == false || data.SRV.WP.core == false)) {
                    isWarn = true;
                }

                if (isWarn) {
                    $('#dpro-scan-warning-continue').show();
                } else {
                    $('#dpro-scan-warning-continue').hide();
                    $('#dup-build-button').prop("disabled", false);
                }
            } catch (err) {
                err += '<br/> Please try again!'
                $('#dup-msg-error-response-status').html("n/a")
                $('#dup-msg-error-response-text').html(err);
                $('#dup-msg-error, .dup-button-footer').show();
                $('#dup-build-button').hide();
            }
        }

        //Starts the build process
        DupPro.Pack.startBuild = function ()
        {
            // disable to prevent double click
            $('#dup-build-button').prop('disabled', true);

            if ($('#dpro-confirm-check').is(":checked")) {
                $('#form-duplicator').submit();
            }

            var sizeChecks = $('#hb-files-large-jstree').length ? $('#hb-files-large-jstree').jstree(true).get_checked() : 0;
            var addonChecks = $('#hb-addon-sites-result input:checked');
            var utf8Checks = $('#hb-files-utf8-jstree').length ? $('#hb-files-utf8-jstree').jstree(true).get_checked() : 0;
            if (sizeChecks.length > 0 || addonChecks.length > 0 || utf8Checks.length > 0) {
                $('#dpro-confirm-area').show();
            } else {
                $('#form-duplicator').submit();
            }
        }

        //Toggles each scan item to hide/show details
        DupPro.Pack.toggleScanItem = function (item)
        {
            var $info = $(item).parents('div.scan-item').children('div.info');
            var $text = $(item).find('div.text i.fa');
            if ($info.is(":hidden")) {
                $text.addClass('fa-caret-down').removeClass('fa-caret-right');
                $info.show();
            } else {
                $text.addClass('fa-caret-right').removeClass('fa-caret-down');
                $info.hide(250);
            }
        }

        //Set Good/Warn Badges and checkboxes
        DupPro.Pack.setScanStatus = function (data)
        {
            let subTestSelectorMappings = {
                '#data-srv-php-websrv': data.SRV.PHP.websrv,
                '#data-srv-php-openbase': data.SRV.PHP.openbase,
                '#data-srv-php-maxtime': data.SRV.PHP.maxtime,
                '#data-srv-php-minmemory': data.SRV.PHP.minMemory,
                '#data-srv-php-arch64bit': data.SRV.PHP.arch64bit,
                '#data-srv-php-mysqli': data.SRV.PHP.mysqli,
                '#data-srv-php-openssl': data.SRV.PHP.openssl,
                '#data-srv-php-allowurlfopen': data.SRV.PHP.allowurlfopen,
                '#data-srv-php-curlavailable': data.SRV.PHP.curlavailable,
                '#data-srv-php-version': data.SRV.PHP.version,
                '#data-srv-wp-version': data.SRV.WP.version,
                '#data-srv-brand-check': data.SRV.Brand.LogoImageExists,
                '#data-srv-wp-core': data.SRV.WP.core
            };

            for (let selector in subTestSelectorMappings) {
                if (subTestSelectorMappings[selector]) {
                    $(selector).html('<div class="scan-good"><i class="fa fa-check"></i></div>');
                } else {
                    $(selector).html('<div class="scan-warn"><i class="fa fa-exclamation-triangle fa-sm"></i></div>');
                }
            }

            let testSelectorMappings = {
                '#data-srv-php-all': data.SRV.PHP.ALL,
                '#data-srv-wp-all': data.SRV.WP.ALL,
                '#data-arc-status-size': data.ARC.Status.Size,
                '#data-arc-status-unreadablefiles': data.ARC.Status.UnreadableItems,
                '#data-arc-status-showcreatefunc': data.ARC.Status.showCreateFuncStatus,
                '#data-arc-status-network': data.ARC.Status.Network,
                '#data-arc-status-triggers': data.DB.Status.Triggers,
                '#data-arc-status-migratepackage': !data.ARC.Status.PackageIsNotImportable,
                '#data-arc-status-addonsites': data.ARC.Status.AddonSites,
                '#data-db-status-size1': data.DB.DBExcluded && data.DB.Status.Success ? data.DB.Status.Excluded : data.DB.Status.Size,
            }

            const GoodText = "<?php esc_html_e('Good', 'duplicator-pro'); ?>";
            const WarnText = "<?php esc_html_e('Notice', 'duplicator-pro'); ?>";

            for (let selector in testSelectorMappings) {
                if (testSelectorMappings[selector]) {
                    $(selector).html(`<div class="badge badge-pass">${GoodText}</div>`);
                } else {
                    $(selector).html(`<div class="badge badge-warn">${WarnText}</div>`);
                }
            }
        }

        //Allows user to continue with build if warnings found
        DupPro.Pack.warningContinue = function (checkbox)
        {
            ($(checkbox).is(':checked'))
                    ? $('#dup-build-button').prop('disabled', false)
                    : $('#dup-build-button').prop('disabled', true);
        }

        //Page Init:
        DupPro.Pack.runScanner(null, true);

        $('.dup-go-back-to-new1').click(function (event) {
            event.preventDefault();
            window.location.href = <?php echo json_encode(PackagesPageController::getInstance()->getPackageBuildS1Url()); ?>;
        });
    });
</script>
