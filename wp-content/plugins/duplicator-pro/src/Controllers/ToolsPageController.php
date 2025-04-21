<?php

/**
 * Tools page controller
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace Duplicator\Controllers;

use Duplicator\Core\MigrationMng;
use DUP_PRO_Package;
use DUP_PRO_Package_Template_Entity;
use DUP_PRO_Server;
use Duplicator\Addons\ProBase\License\License;
use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Controllers\AbstractMenuPageController;
use Duplicator\Core\Controllers\PageAction;
use Duplicator\Core\Controllers\SubMenuItem;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Views\AdminNotices;

class ToolsPageController extends AbstractMenuPageController
{
    const NONCE_ACTION = 'duppro-settings-package';

    /**
     * tabs menu
     */
    const L2_SLUG_GENERAL     = 'general';
    const L2_SLUG_SERVER_INFO = 'server-info';
    const L2_SLUG_LOGS        = 'logs';
    const L2_SLUG_PHP_LOGS    = 'php-logs';
    const L2_SLUG_TEMPLATE    = 'templates';
    const L2_SLUG_RECOVERY    = 'recovery';

    const TEMPLATE_INNER_PAGE_LIST = 'templates';
    const TEMPLATE_INNER_PAGE_EDIT = 'edit';

    const ACTION_PURGE_ORPHANS   = 'purge-orphans';
    const ACTION_CLEAN_CACHE     = 'tmp-cache';
    const ACTION_SAVE_TEMPLATE   = 'save-template';
    const ACTION_COPY_TEMPLATE   = 'copy-template';
    const ACTION_DELETE_TEMPLATE = 'delete-template';
    const ACTION_INSTALLER       = 'installer';

    /**
     * Class constructor
     */
    protected function __construct()
    {
        $this->parentSlug   = ControllersManager::MAIN_MENU_SLUG;
        $this->pageSlug     = ControllersManager::TOOLS_SUBMENU_SLUG;
        $this->pageTitle    = __('Tools', 'duplicator-pro');
        $this->menuLabel    = __('Tools', 'duplicator-pro');
        $this->capatibility = CapMng::CAP_BASIC;
        $this->menuPos      = 50;

        add_filter('duplicator_sub_menu_items_' . $this->pageSlug, [$this, 'getBasicSubMenus']);
        add_filter('duplicator_sub_level_default_tab_' . $this->pageSlug, [$this, 'getSubMenuDefaults'], 10, 2);
        add_action('duplicator_render_page_content_' . $this->pageSlug, [$this, 'renderContent'], 10, 2);
        add_filter('duplicator_page_actions_' . $this->pageSlug, [$this, 'pageActions']);
        add_action('duplicator_before_render_page_' . $this->pageSlug, [$this, 'beforeRenderPage'], 10, 2);
    }

    /**
     * Return actions for current page
     *
     * @param PageAction[] $actions actions lists
     *
     * @return PageAction[]
     */
    public function pageActions($actions)
    {
        $actions[] = new PageAction(
            self::ACTION_PURGE_ORPHANS,
            [
                $this,
                'actionPurgeOrphans',
            ],
            [$this->pageSlug]
        );
        $actions[] = new PageAction(
            self::ACTION_CLEAN_CACHE,
            [
                $this,
                'actionCleanCache',
            ],
            [$this->pageSlug]
        );
        $actions[] = new PageAction(
            self::ACTION_INSTALLER,
            [
                $this,
                'actionInstaller',
            ],
            [$this->pageSlug]
        );
        $actions[] = new PageAction(
            self::ACTION_SAVE_TEMPLATE,
            [
                $this,
                'actionSaveTemplate',
            ],
            [$this->pageSlug],
            self::TEMPLATE_INNER_PAGE_EDIT
        );
        $actions[] = new PageAction(
            self::ACTION_COPY_TEMPLATE,
            [
                $this,
                'actionCopyTemplate',
            ],
            [$this->pageSlug],
            self::TEMPLATE_INNER_PAGE_EDIT
        );
        $actions[] = new PageAction(
            self::ACTION_DELETE_TEMPLATE,
            [
                $this,
                'actionDeleteTemplate',
            ],
            [$this->pageSlug]
        );
        return $actions;
    }

    /**
     * Set Backup object before render pages
     *
     * @param string[] $currentLevelSlugs current menu slugs
     * @param string   $innerPage         current inner page, empty if not set
     *
     * @return void
     */
    public function beforeRenderPage($currentLevelSlugs, $innerPage)
    {
        switch ($currentLevelSlugs[1]) {
            case self::L2_SLUG_TEMPLATE:
                TplMng::getInstance()->setGlobalValue('blur', !License::can(License::CAPABILITY_TEMPLATE));
                break;
            case self::L2_SLUG_RECOVERY:
                TplMng::getInstance()->setGlobalValue('blur', !License::can(License::CAPABILITY_PRO_BASE));
                break;
            default:
        }
    }

    /**
     * Return sub menus for current page
     *
     * @param SubMenuItem[] $subMenus sub menus list
     *
     * @return SubMenuItem[]
     */
    public function getBasicSubMenus($subMenus)
    {
        $subMenus[] = new SubMenuItem(self::L2_SLUG_GENERAL, __('General', 'duplicator-pro'));
        $subMenus[] = new SubMenuItem(self::L2_SLUG_SERVER_INFO, __('Server Info', 'duplicator-pro'));
        $subMenus[] = new SubMenuItem(self::L2_SLUG_LOGS, __('Duplicator Logs', 'duplicator-pro'));
        $subMenus[] = new SubMenuItem(self::L2_SLUG_PHP_LOGS, __('PHP Logs', 'duplicator-pro'));
        $subMenus[] = new SubMenuItem(self::L2_SLUG_TEMPLATE, __('Templates', 'duplicator-pro'), '', CapMng::CAP_CREATE);
        $subMenus[] = new SubMenuItem(self::L2_SLUG_RECOVERY, __('Recovery', 'duplicator-pro'), '', CapMng::CAP_BACKUP_RESTORE);

        return $subMenus;
    }

    /**
     * Return slug default for parent menu slug
     *
     * @param string $slug   current default
     * @param string $parent parent for default
     *
     * @return string default slug
     */
    public function getSubMenuDefaults($slug, $parent)
    {
        switch ($parent) {
            case '':
                return self::L2_SLUG_GENERAL;
            default:
                return $slug;
        }
    }

    /**
     * Action purge orphans
     *
     * @return array{purgeOrphansSuccess: bool ,purgeOrphansFiles: array<string, bool>}
     */
    public function actionPurgeOrphans(): array
    {
        $orphaned_filepaths = DUP_PRO_Server::getOrphanedPackageFiles();

        $result = [
            'purgeOrphansFiles'   => [],
            'purgeOrphansSuccess' => true,
        ];

        foreach ($orphaned_filepaths as $filepath) {
            $result['purgeOrphansFiles'][$filepath] = (is_writable($filepath) && unlink($filepath));
            if ($result['purgeOrphansFiles'][$filepath] == false) {
                $result['purgeOrphansSuccess'] = false;
            }
        }

        return $result;
    }

    /**
     * Action clean cache
     *
     * @return array<string, mixed>
     */
    public function actionCleanCache()
    {
        return [
            'tmpCleanUpSuccess' => DUP_PRO_Package::tmp_cleanup(true),
        ];
    }

    /**
     * Action save template
     *
     * @return array<string, mixed>
     */
    public function actionSaveTemplate(): array
    {
        $templateId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'package_template_id', -1);
        if ($templateId == -1) {
            $template = new DUP_PRO_Package_Template_Entity();
        } else {
            $template = DUP_PRO_Package_Template_Entity::getById($templateId);
        }

        $result = [];
        if ($template->setFromInput(SnapUtil::INPUT_REQUEST) && $template->save()) {
            $result['successMessage'] = __("Template saved.", 'duplicator-pro');
        } else {
            $result['errorMessage'] = __("Couldn't save template.", 'duplicator-pro');
        }

        $result['actionTemplateId'] = $template->getId();

        return $result;
    }

    /**
     * Action copy template
     *
     * @return array<string, mixed>
     */
    public function actionCopyTemplate(): array
    {
        $sourceTemplateId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'duppro-source-template-id', -1);
        if ($sourceTemplateId < 0) {
            return [];
        }

        $templateId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'package_template_id', -1);
        if ($templateId == -1) {
            $template = new DUP_PRO_Package_Template_Entity();
        } else {
            $template = DUP_PRO_Package_Template_Entity::getById($templateId);
        }

        $template->copy_from_source_id($sourceTemplateId);

        $result = [];
        if ($template->save()) {
            $result['successMessage'] = __("Template saved.", 'duplicator-pro');
        } else {
            $result['errorMessage'] = __("Couldn't save template.", 'duplicator-pro');
        }

        $result['actionTemplateId'] = $template->getId();

        return $result;
    }

    /**
     * Action delete template
     *
     * @return array<string, mixed>
     */
    public function actionDeleteTemplate(): array
    {
        if (!isset($_REQUEST['selected_id'])) {
            return ['errorMessage' => __("No template selected.", 'duplicator-pro')];
        }

        $templateIds = filter_var_array($_REQUEST, [
            'selected_id' => [
                'filter'  => FILTER_VALIDATE_INT,
                'flags'   => FILTER_REQUIRE_ARRAY,
                'options' => ['default' => false],
            ],
        ]);

        if (empty($templateIds['selected_id'])) {
            return ['errorMessage' => __("No template selected.", 'duplicator-pro')];
        }

        foreach ($templateIds['selected_id'] as $id) {
            DUP_PRO_Package_Template_Entity::deleteById($id);
        }

        return ['successMessage' => __("Template(s) deleted.", 'duplicator-pro')];
    }

    /**
     * Action installer
     *
     * @return array<string, mixed>
     */
    public function actionInstaller(): array
    {
        $files       = MigrationMng::cleanMigrationFiles();
        $removeError = false;

        foreach ($files as $success) {
            if ($success ==  false) {
                $removeError = true;
            }
        }

        $result = [
            'isMigrationSuccessNotice' => get_option(AdminNotices::OPTION_KEY_MIGRATION_SUCCESS_NOTICE),
            'isInstallerCleanup'       => true,
            'installerCleanupFiles'    => $files,
            'installerCleanupError'    => $removeError,
            'installerCleanupPurge'    => MigrationMng::purgeCaches(),
        ];

        if ($removeError == false) {
            delete_option(AdminNotices::OPTION_KEY_MIGRATION_SUCCESS_NOTICE);
        }

        return $result;
    }

    /**
     * Render page content
     *
     * @param string[] $currentLevelSlugs current menu slugs
     * @param string   $innerPage         current inner page, empty if not set
     *
     * @return void
     */
    public function renderContent($currentLevelSlugs, $innerPage)
    {
        switch ($currentLevelSlugs[1]) {
            case self::L2_SLUG_GENERAL:
                wp_enqueue_script('dup-pro-handlebars');
                if (!TplMng::getInstance()->hasGlobalValue('isMigrationSuccessNotice')) {
                    TplMng::getInstance()->setGlobalValue(
                        'isMigrationSuccessNotice',
                        get_option(AdminNotices::OPTION_KEY_MIGRATION_SUCCESS_NOTICE)
                    );
                }
                TplMng::getInstance()->render(
                    'admin_pages/tools/general'
                );
                break;
            case self::L2_SLUG_SERVER_INFO:
                TplMng::getInstance()->render(
                    'admin_pages/tools/server_info'
                );
                break;
            case self::L2_SLUG_LOGS:
                TplMng::getInstance()->render(
                    'admin_pages/tools/duplicator_logs'
                );
                break;
            case self::L2_SLUG_PHP_LOGS:
                TplMng::getInstance()->render(
                    'admin_pages/tools/php_logs'
                );
                break;
            case self::L2_SLUG_TEMPLATE:
                switch ($innerPage) {
                    case self::TEMPLATE_INNER_PAGE_EDIT:
                        $templateId = TplMng::getInstance()->getGlobalValue(
                            'actionTemplateId',
                            SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'package_template_id', -1)
                        );
                        if ($templateId == -1) {
                            $template = new DUP_PRO_Package_Template_Entity();
                        } else {
                            $template = DUP_PRO_Package_Template_Entity::getById($templateId);
                        }
                        TplMng::getInstance()->render(
                            'admin_pages/templates/template_edit',
                            ['template' => $template]
                        );
                        break;
                    case self::TEMPLATE_INNER_PAGE_LIST:
                    default:
                        TplMng::getInstance()->render('admin_pages/templates/template_list');
                        break;
                }
                break;
            case self::L2_SLUG_RECOVERY:
                TplMng::getInstance()->render('admin_pages/tools/recovery/recovery');
                break;
        }
    }

    /**
     * Return template edit URL
     *
     * @param false|int $templateId template ID, if false return base template edit url
     *
     * @return string
     */
    public static function getTemplateEditURL($templateId = false)
    {
        $data = [ControllersManager::QUERY_STRING_INNER_PAGE => 'edit'];
        if ($templateId !== false) {
            $data['package_template_id'] = $templateId;
        }
        return ControllersManager::getMenuLink(
            ControllersManager::TOOLS_SUBMENU_SLUG,
            self::L2_SLUG_TEMPLATE,
            null,
            $data
        );
    }

    /**
     * Return clean installer files action URL
     *
     * @param bool $relative if true return relative URL else absolute
     *
     * @return string
     */
    public function getCleanFilesAcrtionUrl($relative = true)
    {
        if (($action = $this->getActionByKey(self::ACTION_INSTALLER)) === false) {
            return '';
        }

        return ControllersManager::getMenuLink(
            ControllersManager::TOOLS_SUBMENU_SLUG,
            self::L2_SLUG_GENERAL,
            null,
            [
                'action'   => $action->getKey(),
                '_wpnonce' => $action->getNonce(),
            ],
            $relative
        );
    }

    /**
     * Return Backups log list
     *
     * @return string[]
     */
    public static function getLogsList(): array
    {
        $result = SnapIO::regexGlob(DUPLICATOR_PRO_SSDIR_PATH, [
            'regexFile'   => '/(\.log|_log\.txt)$/',
            'regexFolder' => false,
        ]);
        usort($result, fn($a, $b): int => filemtime($b) - filemtime($a));
        return $result;
    }

    /**
     * Return remove cache action URL
     *
     * @return string
     */
    public function getRemoveCacheActionUrl()
    {
        if (($action = $this->getActionByKey(self::ACTION_CLEAN_CACHE)) === false) {
            return '';
        }

        return ControllersManager::getMenuLink(
            ControllersManager::TOOLS_SUBMENU_SLUG,
            self::L2_SLUG_GENERAL,
            null,
            [
                'action'   => $action->getKey(),
                '_wpnonce' => $action->getNonce(),
            ]
        );
    }

    /**
     * Return purge orphan Backups action URL
     *
     * @return string
     */
    public function getPurgeOrphanActionUrl()
    {
        if (($action = $this->getActionByKey(self::ACTION_PURGE_ORPHANS)) === false) {
            return '';
        }

        return ControllersManager::getMenuLink(
            ControllersManager::TOOLS_SUBMENU_SLUG,
            self::L2_SLUG_GENERAL,
            null,
            [
                'action'   => $action->getKey(),
                '_wpnonce' => $action->getNonce(),
            ]
        );
    }

    /**
     *
     * @return boolean
     */
    public static function isToolPage()
    {
        return ControllersManager::isCurrentPage(ControllersManager::TOOLS_SUBMENU_SLUG);
    }

    /**
     *
     * @return boolean
     */
    public static function isGeneralPage()
    {
        return ControllersManager::isCurrentPage(
            ControllersManager::TOOLS_SUBMENU_SLUG,
            ToolsPageController::L2_SLUG_GENERAL,
            null
        );
    }

    /**
     * Return true if current page is recovery page
     *
     * @return bool
     */
    public static function isRecoveryPage()
    {
        return ControllersManager::isCurrentPage(
            ControllersManager::TOOLS_SUBMENU_SLUG,
            self::L2_SLUG_RECOVERY
        );
    }
}
