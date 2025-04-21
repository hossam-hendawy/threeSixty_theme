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
<div class="scan-item ">
    <div class='title' onclick="DupPro.Pack.toggleScanItem(this);">
        <div class="text"><i class="fa fa-caret-right"></i> <?php esc_html_e('Database only', 'duplicator-pro'); ?></div>
        <div id="only-db-scan-status"><div class="badge badge-warn"><?php esc_html_e("Notice", 'duplicator-pro'); ?></div></div>
    </div>
    <div class="info">
        <?php esc_html_e("Only the database and a copy of the installer.php will be included in the archive.zip file.", 'duplicator-pro'); ?>
    </div>
</div>
