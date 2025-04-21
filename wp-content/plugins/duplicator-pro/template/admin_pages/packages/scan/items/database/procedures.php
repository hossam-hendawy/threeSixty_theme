<?php

/**
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */
?>

<div class="scan-item">
    <div class='title' onclick="DupPro.Pack.toggleScanItem(this);">
        <div class="text"><i class="fa fa-caret-right"></i> <?php esc_html_e('Object Access', 'duplicator-pro');?></div>
        <div id="data-arc-status-showcreatefunc"></div>
    </div>
    <div class="info">
        <script id="hb-showcreatefunc-result" type="text/x-handlebars-template">
            <div class="container">
                <div class="data">
                    {{#if ARC.Status.showCreateFunc}}
                    <?php esc_html_e(
                        "The database user for this WordPress site has sufficient permissions to write stored procedures
                        and functions to the sql file of the archive. [The commands SHOW CREATE PROCEDURE/FUNCTION will work.]",
                        'duplicator-pro'
                    ); ?>
                    {{else}}
                    <span style="color: red;">
                        <?php
                        esc_html_e(
                            "The database user for this WordPress site does NOT have sufficient permissions to write stored
                            procedures to the sql file of the archive. [The command SHOW CREATE FUNCTION will NOT work.]",
                            'duplicator-pro'
                        );
                        ?>
                    </span>
                    {{/if}}
                </div>
            </div>
        </script>
        <div id="showcreatefunc-package-result"></div>
    </div>
</div>
