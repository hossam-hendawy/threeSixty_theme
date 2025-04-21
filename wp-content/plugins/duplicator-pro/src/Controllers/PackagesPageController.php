<?php

/**
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace Duplicator\Controllers;

use DUP_PRO_Archive;
use DUP_PRO_Global_Entity;
use DUP_PRO_Log;
use DUP_PRO_Package;
use DUP_PRO_Package_Template_Entity;
use DUP_PRO_Server;
use DUP_PRO_U;
use Duplicator\Addons\ProBase\License\License;
use Duplicator\Ajax\ServicesPackage;
use Duplicator\Ajax\ServicesRecovery;
use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Controllers\AbstractMenuPageController;
use Duplicator\Core\Controllers\PageAction;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Views\PackageScreen;
use Error;
use Exception;

/**
 * Packages page controller
 */
class PackagesPageController extends AbstractMenuPageController
{
    const L2_SLUG_PACKAGE_LIST = 'packages';

    const LIST_INNER_PAGE_LIST      = 'list';
    const LIST_INNER_PAGE_NEW_STEP1 = 'new1';
    const LIST_INNER_PAGE_NEW_STEP2 = 'new2';
    const LIST_INNER_PAGE_DETAILS   = 'detail';
    const LIST_INNER_PAGE_TRANSFER  = 'transfer';

    /*
     * action types
     */
    const ACTION_SET_RECOVERY_POINT = 'set_recovery_point';
    const ACTION_START_DOWNLOAD     = 'start_package_download';
    const ACTION_START_RESTORE      = 'start_package_restore';
    const ACTION_STOP_BUILD         = 'stop_package_build';
    const ACTION_UPDATE_TEMPLATE    = 'update_template';
    const ACTION_CREATE_FROM_TEMP   = 'create-from-temp';

    /**
     * Class constructor
     */
    protected function __construct()
    {
        $this->parentSlug   = ControllersManager::MAIN_MENU_SLUG;
        $this->pageSlug     = ControllersManager::PACKAGES_SUBMENU_SLUG;
        $this->pageTitle    = __('Backups', 'duplicator-pro');
        $this->menuLabel    = __('Backups', 'duplicator-pro');
        $this->capatibility = CapMng::CAP_BASIC;
        $this->menuPos      = 10;

        add_action('duplicator_before_render_page_' . $this->pageSlug, [$this, 'beforeRenderPage'], 10, 2);
        add_action('duplicator_before_render_page_' . $this->pageSlug, [$this, 'setPackagePageObject'], 10, 2);
        add_action('duplicator_render_page_content_' . $this->pageSlug, [$this, 'renderContent'], 10, 2);
        add_filter('duplicator_page_template_data_' . $this->pageSlug, [$this, 'updatePackagePageTitle']);
        add_filter('set_screen_option_package_screen_options', [PackageScreen::class, 'setScreenOptions'], 11, 3);
        add_filter('duplicator_page_actions_' . $this->pageSlug, [$this, 'pageActions']);
    }

    /**
     * Set Backup page title
     *
     * @param array<string, mixed> $tplData template global data
     *
     * @return array<string, mixed>
     */
    public function updatePackagePageTitle($tplData)
    {
        $innerPage = $this->getCurrentInnerPage();
        switch ($innerPage) {
            case self::LIST_INNER_PAGE_DETAILS:
            case self::LIST_INNER_PAGE_TRANSFER:
                $packageId            = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'id', 0);
                $tplData['pageTitle'] = $this->getPackageDetailTitle($packageId);
                break;
            case self::LIST_INNER_PAGE_NEW_STEP1:
                $tplData['pageTitle'] = __('New Backup', 'duplicator-pro');
                break;
            case self::LIST_INNER_PAGE_NEW_STEP2:
                $tplData['pageTitle'] = __('New Backup - Scan', 'duplicator-pro');
                break;
            case self::LIST_INNER_PAGE_LIST:
            default:
                $tplData['pageTitle']             =  __('Backups', 'duplicator-pro');
                $tplData['templateSecondaryPart'] = 'admin_pages/packages/package_create_button';
                break;
        }
        return $tplData;
    }

    /**
     * Return body header template. Can be overriden by child classes for custom header.
     *
     * @param string[] $currentLevelSlugs current menu slugs
     * @param string   $innerPage         current inner page, empty if not set
     *
     * @return string
     */
    protected function getBodyHeaderTpl($currentLevelSlugs, $innerPage)
    {
        switch ($innerPage) {
            case self::LIST_INNER_PAGE_DETAILS:
            case self::LIST_INNER_PAGE_TRANSFER:
                return 'admin_pages/packages/details/details_wpbody_header';
            case self::LIST_INNER_PAGE_NEW_STEP1:
            case self::LIST_INNER_PAGE_NEW_STEP2:
            case self::LIST_INNER_PAGE_LIST:
            default:
                return parent::getBodyHeaderTpl($currentLevelSlugs, $innerPage);
        }
    }

    /**
     * Set Backup object before render pages
     *
     * @param string[] $currentLevelSlugs current menu slugs
     * @param string   $innerPage         current inner page, empty if not set
     *
     * @return void
     */
    public function setPackagePageObject($currentLevelSlugs, $innerPage)
    {
        switch ($innerPage) {
            case self::LIST_INNER_PAGE_DETAILS:
            case self::LIST_INNER_PAGE_TRANSFER:
                $packageId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'id', 0);
                if ($packageId == 0 || ($package = DUP_PRO_Package::get_by_id($packageId)) == false) {
                    TplMng::getInstance()->setGlobalValue('package', null);
                } else {
                    TplMng::getInstance()->setGlobalValue('package', $package);
                }
                break;
            case self::LIST_INNER_PAGE_NEW_STEP1:
            case self::LIST_INNER_PAGE_NEW_STEP2:
            case self::LIST_INNER_PAGE_LIST:
            default:
                break;
        }
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
        switch ($innerPage) {
            case self::LIST_INNER_PAGE_DETAILS:
                $blur = false;
                break;
            case self::LIST_INNER_PAGE_TRANSFER:
            case self::LIST_INNER_PAGE_NEW_STEP1:
            case self::LIST_INNER_PAGE_NEW_STEP2:
            case self::LIST_INNER_PAGE_LIST:
            default:
                $blur = is_multisite() && !License::can(License::CAPABILITY_MULTISITE);
                break;
        }
        TplMng::getInstance()->setGlobalValue('blur', $blur);
    }

    /**
     * Capability check
     *
     * @return void
     */
    protected function capabilityCheck()
    {
        parent::capabilityCheck();

        $capOk     = true;
        $innerPage = $this->getCurrentInnerPage();
        switch ($innerPage) {
            case self::LIST_INNER_PAGE_DETAILS:
                break;
            case self::LIST_INNER_PAGE_TRANSFER:
                $capOk = CapMng::can(CapMng::CAP_CREATE, false);
                break;
            case self::LIST_INNER_PAGE_NEW_STEP1:
                $nonce = SnapUtil::sanitizeTextInput(INPUT_GET, '_wpnonce', '');
                $capOk = ($nonce !== '' && CapMng::can(CapMng::CAP_CREATE, false) && wp_verify_nonce($nonce, 'new1-package'));
                break;
            case self::LIST_INNER_PAGE_NEW_STEP2:
                $nonce = SnapUtil::sanitizeTextInput(INPUT_GET, '_wpnonce', '');
                $capOk = ($nonce !== '' && CapMng::can(CapMng::CAP_CREATE, false) && wp_verify_nonce($nonce, 'new2-package'));
                break;
            case self::LIST_INNER_PAGE_LIST:
            default:
                break;
        }

        if (!$capOk) {
            self::notPermsDie();
        }
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
            self::ACTION_SET_RECOVERY_POINT,
            [
                $this,
                'setRecoveryPoint',
            ],
            [$this->pageSlug]
        );

        $actions[] = new PageAction(
            self::ACTION_START_DOWNLOAD,
            [
                $this,
                'startPackageDownload',
            ],
            [$this->pageSlug]
        );

        $actions[] = new PageAction(
            self::ACTION_START_RESTORE,
            [
                $this,
                'startPackageRestore',
            ],
            [$this->pageSlug]
        );

        $actions[] = new PageAction(
            self::ACTION_STOP_BUILD,
            [
                $this,
                'stopPackageBuild',
            ],
            [$this->pageSlug]
        );

        $actions[] = new PageAction(
            self::ACTION_UPDATE_TEMPLATE,
            [
                $this,
                'updateTemplate',
            ],
            [$this->pageSlug],
            self::LIST_INNER_PAGE_NEW_STEP2
        );

        $actions[] = new PageAction(
            self::ACTION_CREATE_FROM_TEMP,
            [
                $this,
                'createFromTemp',
            ],
            [$this->pageSlug]
        );

        return $actions;
    }

    /**
     * Save general settings
     *
     * @return array<string, mixed>
     */
    public function setRecoveryPoint(): array
    {
        $result = ['recoverySet' => false];

        try {
            $recoveryData             = ServicesRecovery::setRecoveryCallback();
            $result['recoverySet']    = true;
            $result['successMessage'] = $recoveryData['adminMessage'];
        } catch (Exception | Error $e) {
            $result['recoverySet']  = false;
            $result['errorMessage'] = $e->getMessage();
            return $result;
        }

        return $result;
    }

    /**
     * Start the Backup download from remote
     *
     * @return array<string, mixed>
     */
    public function startPackageDownload()
    {
        try {
            ServicesPackage::manualTransferStorageCallback();
            return [
                'remoteDownloadPackageId' => SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'package_id', -1),
                'afterDownloadAction'     => SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'afterDownloadAction', ''),
            ];
        } catch (Exception | Error $e) {
            return [
                'remoteDownloadPackageId' => -1,
                'errorMessage'            => $e->getMessage(),
            ];
        }
    }

    /**
     * Start the Backup download from remote
     *
     * @return array<string, mixed>
     */
    public function startPackageRestore()
    {
        if (($packageId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'packageId', -1)) === -1) {
            return [
                'errorMessage' => __('Backup ID not found', 'duplicator-pro'),
            ];
        }

        return ['triggerRestore' => $packageId];
    }

    /**
     * Stop the Backup build
     *
     * @return array<string, mixed>
     */
    public function stopPackageBuild()
    {
        if (!CapMng::can(CapMng::CAP_CREATE, false)) {
            return ['errorMessage' => __('You don\'t have permissions to stop a backup build.', 'duplicator-pro')];
        }

        $packageId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'stop-backup-id', -1);
        if ($packageId < 0) {
            return ['errorMessage' => __('Invalid backup id', 'duplicator-pro')];
        }

        DUP_PRO_Log::trace("Trying to stop build of $packageId");
        $success = false;
        $backup  = DUP_PRO_Package::get_by_id($packageId);
        if ($backup != null) {
            DUP_PRO_Log::trace("set $backup->ID for cancel");
            $backup->set_for_cancel();
            $success = true;
        } else {
            DUP_PRO_Log::trace("Could not find Backup so attempting hard delete.");
            $success = DUP_PRO_Package::force_delete($packageId);
            $success ? DUP_PRO_Log::trace("Hard delete success") : DUP_PRO_Log::trace("Hard delete failure");
        }

        if ($success) {
            return ['successMessage' => __('Backup set for cancelling.', 'duplicator-pro')];
        } else {
            return ['errorMessage' => __('Couldn\'t set backup for cancelling.', 'duplicator-pro')];
        }
    }

    /**
     * Update the template
     *
     * @return array<string, mixed>
     */
    public function updateTemplate()
    {
        $templateId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'template_id', -1);
        if (
            $templateId < 0 ||
            DUP_PRO_Package_Template_Entity::getById($templateId) === false
        ) {
            DUP_PRO_Log::trace("Couldn't update manual template. Invalid template id.");
        }

        $storageIds = filter_input_array(INPUT_POST, [
            '_storage_ids' => [
                'filter'  => FILTER_VALIDATE_INT,
                'flags'   => FILTER_REQUIRE_ARRAY,
                'options' => ['default' => []],
            ],
        ])['_storage_ids'];

        DUP_PRO_Package::set_manual_template_from_post($_REQUEST);
        DUP_PRO_Package::set_temporary_package_from_template_and_storages($templateId, $storageIds);

        DUP_PRO_Global_Entity::getInstance()->setManualModeStorageIds($storageIds);
        if (DUP_PRO_Global_Entity::getInstance()->save()) {
            DUP_PRO_Log::trace("Manual template updated");
        } else {
            DUP_PRO_Log::trace("Could not update manual template.");
        }

        return [];
    }

    /**
     * Create From Temp
     *
     * @return array<string, mixed>
     */
    public function createFromTemp()
    {
        if (($package = DUP_PRO_Package::get_temporary_package(false)) === null) {
            return ['errorMessage' => __('Temporary package not found', 'duplicator-pro')];
        }

        if (!$package->save()) {
            return ['errorMessage' => __('Could not save package', 'duplicator-pro')];
        }

        return [];
    }

    /**
     * Return create Backup link
     *
     * @return string
     */
    public function getPackageBuildS1Url()
    {
        return $this->getMenuLink(
            null,
            null,
            [
                ControllersManager::QUERY_STRING_INNER_PAGE => self::LIST_INNER_PAGE_NEW_STEP1,
                '_wpnonce'                                  => wp_create_nonce('new1-package'),
            ]
        );
    }

    /**
     * Return create Backup link step2
     *
     * @return string
     */
    public function getPackageBuildS2Url()
    {
        return $this->getMenuLink(
            null,
            null,
            [
                ControllersManager::QUERY_STRING_INNER_PAGE => self::LIST_INNER_PAGE_NEW_STEP2,
                '_wpnonce'                                  => wp_create_nonce('new2-package'),
            ]
        );
    }

    /**
     * called on admin_print_styles-[page] hook
     *
     * @return void
     */
    public function pageStyles()
    {
        // wp_enqueue_style('dup-pro-packages');
    }

    /**
     * Get Backup detail title page
     *
     * @param int<0,max> $packageId Backup id
     *
     * @return string
     */
    protected function getPackageDetailTitle($packageId = 0)
    {
        if ($packageId === 0 || ($package = DUP_PRO_Package::get_by_id($packageId)) === false) {
            return __('Backup: Not Found', 'duplicator-pro');
        } else {
            return sprintf(__('Backup: %1$s', 'duplicator-pro'), $package->getName());
        }
    }

    /**
     * Get Backup list title page
     *
     * @return string
     */
    protected function getPackageListTitle()
    {
        $postfix = '';
        switch ($this->getCurrentInnerPage()) {
            case self::LIST_INNER_PAGE_NEW_STEP1:
            case self::LIST_INNER_PAGE_NEW_STEP2:
                $postfix = __('New', 'duplicator-pro');
                break;
            case self::LIST_INNER_PAGE_LIST:
            default:
                $postfix = __('All', 'duplicator-pro');
                break;
        }
        return __('Backups', 'duplicator-pro') . " » " . $postfix;
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
        switch ($innerPage) {
            case self::LIST_INNER_PAGE_DETAILS:
                $packageId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'id', 0);
                if ($packageId == 0 || ($package = DUP_PRO_Package::get_by_id($packageId)) == false) {
                    TplMng::getInstance()->render(
                        'admin_pages/packages/details/no_package_found',
                        ['packageId' => $packageId]
                    );
                } else {
                    TplMng::getInstance()->render('admin_pages/packages/details/detail');
                }
                break;
            case self::LIST_INNER_PAGE_TRANSFER:
                $packageId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'id', 0);
                if ($packageId == 0 || ($package = DUP_PRO_Package::get_by_id($packageId)) == false) {
                    TplMng::getInstance()->render(
                        'admin_pages/packages/details/no_package_found',
                        ['packageId' => $packageId]
                    );
                } else {
                    TplMng::getInstance()->render('admin_pages/packages/details/transfer');
                }
                break;
            case self::LIST_INNER_PAGE_NEW_STEP1:
                $requirements = DUP_PRO_Server::getRequirments();
                if ($requirements['Success'] != true) {
                    DUP_PRO_Log::traceObject('Requirements', $requirements);
                }
                TplMng::getInstance()->render(
                    'admin_pages/packages/setup/setup_page',
                    ['requirements' => $requirements]
                );
                break;
            case self::LIST_INNER_PAGE_NEW_STEP2:
                wp_enqueue_script('dup-pro-handlebars');
                $package   = DUP_PRO_Package::get_temporary_package();
                $validator = $package->validateInputs();
                if (!$validator->isSuccess()) {
                    TplMng::getInstance()->render(
                        'admin_pages/packages/scan/validation_error',
                        ['validator' => $validator]
                    );
                    return;
                }

                TplMng::getInstance()->render('admin_pages/packages/scan/main', self::getScanTplData($package));
                break;
            case self::LIST_INNER_PAGE_LIST:
            default:
                if (DUP_PRO_Package::getNumPackages() > 0) {
                    /**
                     * Don't blur the page if there are backups, for legacy reasons
                     *
                     * @todo remove this logic in the future
                     */
                    TplMng::getInstance()->setGlobalValue('blur', false);
                }

                TplMng::getInstance()->render(
                    'admin_pages/packages/main',
                    [
                        'pending_cancelled_package_ids' => DUP_PRO_Package::get_pending_cancellations(),
                    ]
                );
                break;
        }
    }

    /**
     * Set global template data for scan step
     *
     * @param DUP_PRO_Package $package The temporary package
     *
     * @return array<string, mixed> The render data
     */
    private static function getScanTplData($package): array
    {
        global $wpdb;

        $procQuery = $wpdb->prepare("SHOW PROCEDURE STATUS WHERE `Db` = %s", $wpdb->dbname);
        $funcQuery = $wpdb->prepare("SHOW FUNCTION STATUS WHERE `Db`  = %s", $wpdb->dbname);
        $data      = [
            'package'           => $package,
            'filteredCoreDirs'  => [],
            'filteredCoreFiles' => [],
            'prefix'            => $wpdb->prefix,
            'prefixFilter'      => $package->Database->prefixFilter,
            'prefixSubFilter'   => $package->Database->prefixSubFilter,
            'procedures'        => $wpdb->get_col($procQuery, 1),
            'functions'         => $wpdb->get_col($funcQuery, 1),
            'triggers'          => $wpdb->get_col("SHOW TRIGGERS", 1),
        ];

        if (!$package->isDbOnly() && $package->Archive->FilterOn) {
            $filteredDirs             = DUP_PRO_Archive::parseDirectoryFilter($package->Archive->FilterDirs, true);
            $data['filteredCoreDirs'] = array_intersect($filteredDirs, DUP_PRO_U::getWPCoreDirs());

            $filteredFiles             = DUP_PRO_Archive::parseFileFilter($package->Archive->FilterFiles, true);
            $data['filteredCoreFiles'] = array_intersect($filteredFiles, DUP_PRO_U::getWPCoreFiles());
        }

        return $data;
    }

    /**
     * Get Backup detail url
     *
     * @param false|int $package_id Backup id, if false return base url without id
     *
     * @return string
     */
    public function getPackageDetailsUrl($package_id = false)
    {
        $data = [
            ControllersManager::QUERY_STRING_INNER_PAGE => self::LIST_INNER_PAGE_DETAILS,
        ];
        if ($package_id !== false) {
            $data['id'] = $package_id;
        }
        return $this->getMenuLink(null, null, $data);
    }

    /**
     * Get Backup detail url
     *
     * @param false|int $package_id Backup id, if false return base url without id
     *
     * @return string
     */
    public function getPackageTransferUrl($package_id = false)
    {
        $data = [
            ControllersManager::QUERY_STRING_INNER_PAGE => self::LIST_INNER_PAGE_TRANSFER,
        ];
        if ($package_id !== false) {
            $data['id'] = $package_id;
        }
        return $this->getMenuLink(null, null, $data);
    }

    /**
     * Get Backups inner page
     *
     * @return string
     */
    public function getPackagesInnerPage()
    {
        return $this->getCurrentInnerPage();
    }
}
