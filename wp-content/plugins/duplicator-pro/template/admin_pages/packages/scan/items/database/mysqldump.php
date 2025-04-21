<?php

/**
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

use Duplicator\Controllers\SettingsPageController;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$settingsPackageUrl = SettingsPageController::getInstance()->getMenuLink(SettingsPageController::L2_SLUG_PACKAGE);
?>
<div class="scan-item" id="mysqldump-limit-result"></div>
<script id="hb-mysqldump-limit-result" type="text/x-handlebars-template">
    <div class="title" onclick="DupPro.Pack.toggleScanItem(this);">
        <div class="text">
            <i class="fa fa-caret-right"></i> <?php esc_html_e('Mysqldump memory check', 'duplicator-pro'); ?>
        </div>
        <div id="data-db-status-mysqldump-limit">
            {{#if DB.Status.mysqlDumpMemoryCheck}}
                <div class="badge badge-pass"><?php esc_html_e('Good', 'duplicator-pro'); ?></div>
            {{else}}
                <div class="badge badge-warn"><?php esc_html_e('Notice', 'duplicator-pro'); ?></div>
            {{/if}}
        </div>
    </div>
    {{#if DB.Status.mysqlDumpMemoryCheck}}
        <div class="info">
            <p class="green">
                <?php esc_html_e('The database size is within the allowed mysqldump size limit.', 'duplicator-pro'); ?>
            </p>
            <?php
            echo wp_kses(
                sprintf(
                    _x(
                        'If you encounter any issues with mysqldump please change the setting SQL Mode to PHP Code. 
                        You can do that by opening %1$sDuplicator Pro > Settings > Backups.%2$s',
                        '1$s and 2$s represent opening and closing anchor tags',
                        'duplicator-pro'
                    ),
                    '<a href="' . esc_url($settingsPackageUrl) . '" target="_blank">',
                    '</a>'
                ),
                [
                    'a' => [
                        'href'   => [],
                        'target' => [],
                    ],
                ]
            );
            ?>
        </div>
    {{else}}
        <div class="info" style="display:block;">
            <p class="red">
                <?php esc_html_e('The database size exceeds the allowed mysqldump size limit.', 'duplicator-pro'); ?>
            </p>
            <?php
            esc_html_e(
                'The database size is larger than the PHP memory_limit value.
                This can lead into issues when building a Backup, during which the system can run out of memory. 
                To fix this issue please consider doing one of the below mentioned recommendations.',
                'duplicator-pro'
            );
            ?>
            <hr size="1" />
            <p>
                <b><?php esc_html_e('RECOMMENDATIONS:', 'duplicator-pro'); ?></b>
            </p>
            <ul class="dup-pro-simple-style-disc" >
                <li>
                <?php echo wp_kses(
                    sprintf(
                        _x(
                            'Please change the setting SQL Mode to PHP Code.
                            You can do that by opening %1$sDuplicator Pro > Settings > Backups.%2$s',
                            '%1$s and %2$s represent opening and closing anchor tags',
                            'duplicator-pro'
                        ),
                        '<a href="' . esc_url($settingsPackageUrl) . '" target="_blank">',
                        '</a>'
                    ),
                    [
                        'a' => [
                            'href'   => [],
                            'target' => [],
                        ],
                    ]
                ); ?>
                </li>
                <li>
                <?php echo wp_kses(
                    sprintf(
                        _x(
                            'If you want to build the backup with mysqldump, increase the PHP <b>memory_limit</b> 
                            value in your php.ini file to at least %1$s.',
                            '%1$s represents the memory limit value (e.g. 256MB)',
                            'duplicator-pro'
                        ),
                        '<b><span id="data-db-size3">{{DB.Status.requiredMysqlDumpLimit}}</span></b>'
                    ),
                    [
                        'b'    => [],
                        'span' => ['id' => []],
                    ]
                ); ?>
                </li>
            </ul>
        </div>
    {{/if}}
</script>
