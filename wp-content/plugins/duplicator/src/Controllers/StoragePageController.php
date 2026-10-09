<?php

/**
 * Storage page controller
 */

namespace Duplicator\Controllers;

use Duplicator\Utils\Logging\DupLog;
use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Controllers\AbstractMenuPageController;
use Duplicator\Core\Controllers\PageAction;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Models\Storages\StoragesUtil;
use Duplicator\Models\Storages\UnknownStorage;
use Exception;

class StoragePageController extends AbstractMenuPageController
{
    const INNER_PAGE_LIST = 'storage';
    const INNER_PAGE_EDIT = 'edit';
    const ACTION_SAVE     = 'save';
    const ACTION_COPY     = 'copy-storage';

    /**
     * Class constructor
     */
    protected function __construct()
    {
        $this->parentSlug   = ControllersManager::MAIN_MENU_SLUG;
        $this->pageSlug     = ControllersManager::STORAGE_SUBMENU_SLUG;
        $this->pageTitle    = __('Storage', 'duplicator');
        $this->menuLabel    = __('Storage', 'duplicator');
        $this->capatibility = CapMng::CAP_STORAGE;
        $this->menuPos      = 40;

        add_filter('duplicator_page_actions_' . $this->pageSlug, [$this, 'pageActions']);
        add_action('duplicator_after_run_actions_' . $this->pageSlug, [$this, 'pageAfterActions']);
        add_filter('duplicator_page_template_data_' . $this->pageSlug, [$this, 'updatePackagePageTitle']);
        add_action('duplicator_render_page_content_' . $this->pageSlug, [$this, 'renderContent'], 10, 2);
        add_action('duplicator_before_render_page_' . $this->pageSlug, [$this, 'beforeRenderPage'], 10, 2);
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
        switch ($this->getCurrentInnerPage()) {
            case self::INNER_PAGE_EDIT:
                break;
            case self::INNER_PAGE_LIST:
            default:
                $tplData['pageTitle']             =  __('Storage', 'duplicator');
                $tplData['templateSecondaryPart'] = 'admin_pages/storages/storage_create_button';
                break;
        }
        return $tplData;
    }

    /**
     * Set Backup object before render pages
     *
     * @param string[] $currentLevelSlugs current menu slugs
     * @param string   $innerPage         current inner page, empty if not set
     *
     * @return void
     */
    public function beforeRenderPage($currentLevelSlugs, $innerPage): void
    {
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
            self::ACTION_SAVE,
            [
                $this,
                'actionEditSave',
            ],
            [$this->pageSlug],
            'edit'
        );
        $actions[] = new PageAction(
            self::ACTION_COPY,
            [
                $this,
                'actionEditCopyStorage',
            ],
            [$this->pageSlug],
            'edit'
        );
        return $actions;
    }

    /**
     * Return storage edit url
     *
     * @param AbstractStorageEntity $storage storage entity, if is null get new storage URL
     *
     * @return string
     */
    public static function getEditUrl(?AbstractStorageEntity $storage = null): string
    {
        $data = [ControllersManager::QUERY_STRING_INNER_PAGE => 'edit'];
        if ($storage !== null) {
            $data['storage_id'] = $storage->getId();
        }
        return ControllersManager::getMenuLink(ControllersManager::STORAGE_SUBMENU_SLUG, null, null, $data);
    }

    /**
     * Return storage defualt edit URL
     *
     * @return string
     */
    public static function getEditDefaultUrl(): string
    {
        return ControllersManager::getMenuLink(
            ControllersManager::STORAGE_SUBMENU_SLUG,
            null,
            null,
            [
                ControllersManager::QUERY_STRING_INNER_PAGE => 'edit',
                'storage_id'                                => StoragesUtil::getDefaultStorageId(),
            ]
        );
    }

    /**
     * Page after actions hook
     *
     * @param bool $isActionCalled true if one actions is called,false if no actions
     *
     * @return void
     */
    public function pageAfterActions($isActionCalled): void
    {
        $tplMng = TplMng::getInstance();
        if ($this->getCurrentInnerPage() == 'edit' && $tplMng->hasGlobalValue('storage_id') == false) {
            $storageId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'storage_id', -1);
            $storage   = ($storageId == -1 ? StoragesUtil::getDefaultNewStorage() : AbstractStorageEntity::getById($storageId));
            if ($storage === false) {
                $storageId = -1;
                $storage   = StoragesUtil::getDefaultNewStorage();
            }

            $tplMng->setGlobalValue('storage_id', $storageId);
            $tplMng->setGlobalValue('storage', $storage);
            $tplMng->setGlobalValue('error_message', null);
            $tplMng->setGlobalValue('success_message', null);
        }
    }

    /**
     * Render page content
     *
     * @param string[] $currentLevelSlugs current menu slugs
     * @param string   $innerPage         current inner page, empty if not set
     *
     * @return void
     */
    public function renderContent($currentLevelSlugs, $innerPage): void
    {
        try {
            switch ($this->getCurrentInnerPage()) {
                case self::INNER_PAGE_EDIT:
                    TplMng::getInstance()->render(
                        'admin_pages/storages/storage_edit',
                        self::getStorageTypeSelectData()
                    );
                    break;
                case self::INNER_PAGE_LIST:
                default:
                    // I left the global try catch for security but the exceptions should be managed inside the list.
                    TplMng::getInstance()->render('admin_pages/storages/storage_list');
                    break;
            }
        } catch (Exception $e) {
            DupLog::trace("Error while rendering storage: " . $e->getMessage());
            TplMng::getInstance()->render(
                'admin_pages/storages/parts/storage_error',
                ['exception' => $e]
            );
        }
    }

    /**
     * Build storage type selector data for the edit page.
     *
     * @return array<string, mixed>
     */
    private static function getStorageTypeSelectData(): array
    {
        $tplMng  = TplMng::getInstance();
        $storage = $tplMng->getGlobalValue('storage');

        $sTypeSelected = ($storage->isSelectable() ? $storage->getSType() : -1);
        $isEditMode    = ($storage->getId() < 0);
        $storageTypes  = [];

        foreach (AbstractStorageEntity::getResisteredTypesByPriority() as $type) {
            $class = AbstractStorageEntity::getSTypePHPClass($type);
            if (!$class::isSelectable()) {
                continue;
            }

            $disabledReason = '';
            $isDisabled     = StoragesUtil::isSelectDisabled($class, $disabledReason);

            $storageTypes[] = [
                'type'      => $type,
                'class'     => $class,
                'name'      => $class::getStypeName(),
                'icon'      => $class::getStypeIcon(),
                'disabled'  => $isDisabled,
                'reason'    => $disabledReason,
                'gridBreak' => $class::isGridBreakAfter(),
            ];
        }

        return [
            'storageTypes'  => $storageTypes,
            'sTypeSelected' => $sTypeSelected,
            'isEditMode'    => $isEditMode,
        ];
    }

    /**
     * Save storage
     *
     * @return array{storage_id:int,storage:AbstractStorageEntity,error_message:?string,success_message:?string}
     */
    public function actionEditSave(): array
    {
        $error_message = null;

        $storageId   = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'storage_id', -1);
        $storageType = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'storage_type', UnknownStorage::getSType());
        $storage     = ($storageId == -1 ? AbstractStorageEntity::getNewStorageByType($storageType) : AbstractStorageEntity::getById($storageId));
        if ($storage === false) {
            return [
                "storage_id"      => $storageId,
                "storage"         => StoragesUtil::getDefaultNewStorage(),
                "error_message"   => __('Unable to load storage item', 'duplicator'),
                "success_message" => null,
            ];
        }
        $message = '';

        if ($storage->updateFromHttpRequest($message) === false) {
            $error_message = $message;
            DupLog::trace('Storage update failed ID:' . $storage->getId() . ' Type:' . $storage->getStypeName() . ' Message:' . $message);
        } elseif ($storage->save() === false) {
            $error_message = __('Unable to save storage item', 'duplicator');
            DupLog::trace('Storage save failed ID:' . $storage->getId() . ' Type:' . $storage->getStypeName());
        } else {
            DupLog::trace('Storage updated successfully ID:' . $storage->getId() . ' Type:' . $storage->getStypeName());
            self::redirectAfterSave($storage, $message);
        }

        return [
            "storage_id"      => $storageId,
            "storage"         => $storage,
            "error_message"   => $error_message,
            "success_message" => null,
        ];
    }

    /**
     * Save storage
     *
     * @return array{storage_id:int,storage:AbstractStorageEntity,error_message:?string,success_message:?string}
     */
    public function actionEditCopyStorage(): array
    {
        $error_message = null;
        $sourceId      = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'dupli-source-storage-id', -1);
        $targetId      = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'storage_id', -1);

        if (($storage = AbstractStorageEntity::getCopyStorage($sourceId, $targetId)) === false) {
            $error_message = __('Unable to copy storage item', 'duplicator');
            $storage       = AbstractStorageEntity::getById($targetId);
            if ($storage === false) {
                $storage = StoragesUtil::getDefaultNewStorage();
            }
        } elseif ($storage->save() === false) {
            $error_message = __('Unable to copy storage item', 'duplicator');
            DupLog::trace('Storage save failed ID:' . $storage->getId() . ' Type:' . $storage->getStypeName());
        } else {
            self::redirectAfterSave($storage, __('Storage Copied Successfully.', 'duplicator'));
        }

        return [
            "storage_id"      => $targetId,
            "storage"         => $storage,
            "error_message"   => $error_message,
            "success_message" => null,
        ];
    }

    /**
     * Redirect to the canonical edit URL so a reload preserves storage_id.
     * Success notice is passed via URL param and rendered client-side.
     *
     * @param AbstractStorageEntity $storage saved storage
     * @param string                $message success notice (empty to skip)
     *
     * @return void
     */
    private static function redirectAfterSave(AbstractStorageEntity $storage, string $message): void
    {
        $query = [
            ControllersManager::QUERY_STRING_INNER_PAGE => self::INNER_PAGE_EDIT,
            'storage_id'                                => $storage->getId(),
        ];
        if ($message !== '') {
            $query['dup-save-message'] = $message;
        }
        SnapUtil::obCleanAll(false);
        wp_safe_redirect(
            ControllersManager::getMenuLink(
                ControllersManager::STORAGE_SUBMENU_SLUG,
                null,
                null,
                $query,
                false
            )
        );
        exit;
    }
}
