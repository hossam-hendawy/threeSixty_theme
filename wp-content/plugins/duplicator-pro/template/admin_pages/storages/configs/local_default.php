<?php

/**
 * Duplicator messages sections
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

use Duplicator\Models\Storages\Local\LocalStorage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var LocalStorage $storage
 */
$storage = $tplData["storage"];
/** @var int */
$maxPackages =  $tplData["maxPackages"];
/** @var bool  */
$purgePackages = $tplData["purgePackages"];
/** @var string */
$storageFolder = $tplData["storageFolder"];

$tplMng->render('admin_pages/storages/parts/provider_head');
?>
<tr valign="top">
    <th scope="row"><label><?php esc_html_e("Location", 'duplicator-pro'); ?></label></th>
    <td><?php echo esc_html($storageFolder); ?></td>
</tr>  
<tr>
<th scope="row"><label for=""><?php esc_html_e("Max Backups", 'duplicator-pro'); ?></label></th>
    <td>
        <div class="horizontal-input-row">
            <input 
                data-parsley-errors-container="#max_default_store_files_error_container" 
                id="max_default_store_files" 
                name="max_default_store_files" 
                type="text" 
                data-parsley-type="number" 
                data-parsley-min="0" 
                data-parsley-required="true" 
                value="<?php echo intval($maxPackages); ?>" 
                maxlength="4"
            >
            <label for="max_default_store_files">
                <?php esc_html_e("Number of Backups to keep in folder. ", 'duplicator-pro'); ?><br/>
            </label>
        </div>
        <?php $tplMng->render('admin_pages/storages/parts/max_backups_description'); ?>
        <div id="max_default_store_files_error_container" class="duplicator-error-container"></div>
    </td>
</tr>
<?php $tplMng->render('admin_pages/storages/parts/provider_foot'); ?>
