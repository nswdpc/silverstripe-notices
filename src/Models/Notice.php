<?php

namespace NSWDPC\Notices;

use gorriecoe\LinkField\LinkField;
use gorriecoe\Link\Models\Link;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\View\TemplateGlobalProvider;

/**
 * Provides a Notice model
 * @author James
 * @property string $Title
 * @property bool $ShowTitle
 * @property ?string $Description
 * @property bool $IsGlobal
 * @property bool $IsActive
 * @property int $AutoCloseAfter
 * @property int $LinkID
 * @method \gorriecoe\Link\Models\Link Link()
 */
class Notice extends DataObject implements PermissionProvider, TemplateGlobalProvider
{
    private static string $singular_name = "Notice";

    private static string $plural_name = "Notices";

    private static string $table_name = "SiteNotice";

    private static array $db = [
        'Title' => 'Varchar(255)',
        'ShowTitle' => 'Boolean',
        'Description' => 'Text',
        'IsGlobal' => 'Boolean',
        'IsActive' => 'Boolean',
        'AutoCloseAfter' => 'Int',// seconds
    ];

    private static array $summary_fields = [
        'Title' => 'Title',
        'IsActive.Nice' => 'Active?',
        'IsGlobal.Nice' => 'Global?',
        'Description' => 'Description',
        'AutoCloseAfter' => 'Auto-close (seconds)',
    ];

    private static array $has_one = [
        'Link' => Link::class
    ];

    private static array $owns = [
        'Link'
    ];

    private static array $indexes = [
        'IsGlobal' => true,
        'IsActive' => true
    ];

    /**
     * Post-write operations
     */
    #[\Override]
    public function onAfterWrite()
    {
        parent::onAfterWrite();
        if ($this->IsGlobal == 1) {
            DB::prepared_query('UPDATE "SiteNotice" SET IsGlobal = 0 WHERE ID <> ?', [$this->ID]);
        }
    }

    public function TitleWithGlobalStatus(): string
    {
        return $this->Title . ($this->IsGlobal == 1 ? " (" . _t('sitenotice.GLOBAL', 'global') . ")" : "");
    }

    /**
     * Return value for use as unique modalId in DOM
     */
    public function getModalId(): string
    {
        return Convert::raw2url("notice-{$this->ID}");
    }

    /**
     * Return value for use as unique modalId in DOM
     */
    public function getExtraClass(): string
    {
        $extraClasses = [];
        $this->extend('addExtraClass', $extraClasses);
        return implode(" ", array_unique($extraClasses));
    }

    #[\Override]
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('LinkID');
        $fields->addFieldsToTab(
            'Root.Main',
            [
                TextField::create(
                    'Title',
                    _t('sitenotice.TITLE', 'Title')
                )->setDescription(
                    _t(
                        'sitenotice.TITLE_DESCRIPTION',
                        'Provide a short, one-line, description of the notice.'
                    )
                ),
                CheckboxField::create(
                    'IsGlobal',
                    _t("sitenotice.SITE_WIDE_NOTICE_TITLE", "Site-wide notice")
                )->setDescription(
                    _t(
                        'sitenotice.ISGLOBAL_DESCRIPTION',
                        'When checked, this notice will be displayed on every page on the website, where supported'
                    )
                ),
                TextareaField::create(
                    'Description',
                    _t("sitenotice.DESCRIPTION_TITLE", "Content of the notice")
                )
                ->setRows(5)
                ->setDescription(
                    _t(
                        'sitenotice.DESCRIPTION_DESCRIPTION',
                        'Notices should be readable within a defined time-frame, prior to when a notice might dismiss itself'
                    )
                ),
                NumericField::create(
                    'AutoCloseAfter',
                    _t("sitenotice.AUTOCLOSEAFTER_TITLE", "The notice will disappear after this amount of seconds")
                )
                ->setHTML5(true),
                LinkField::create(
                    'Link',
                    _t("sitenotice.LINK", "Link"),
                    $this
                )->setDescription(
                    _t("sitenotice.LINK_DESCRIPTION", "Adds a link to a notice"),
                )
            ]
        );

        return $fields;
    }

    /**
     * @return bool
     */
    #[\Override]
    public function canView($member = null)
    {
        return true;
    }

    /**
     * @return bool
     */
    #[\Override]
    public function canEdit($member = null)
    {
        return Permission::check('SITENOTICE_EDIT');
    }

    /**
     * @return bool
     */
    #[\Override]
    public function canDelete($member = null)
    {
        return Permission::check('SITENOTICE_DELETE');
    }

    /**
     * @return bool
     */
    #[\Override]
    public function canCreate($member = null, $context = [])
    {
        return Permission::check('SITENOTICE_CREATE');
    }

    /**
     * @return array
     */
    public function providePermissions()
    {
        return [
            'SITENOTICE_EDIT' => [
                'name' => _t(
                    'sitenotice.EditPermissionLabel',
                    'Edit a site notice'
                ),
                'category' => _t(
                    'sitenotice.Category',
                    'Site notices'
                ),
            ],
            'SITENOTICE_DELETE' => [
                'name' => _t(
                    'sitenotice.DeletePermissionLabel',
                    'Delete a site notice'
                ),
                'category' => _t(
                    'sitenotice.Category',
                    'Site notices'
                ),
            ],
            'SITENOTICE_CREATE' => [
                'name' => _t(
                    'sitenotice.CreatePermissionLabel',
                    'Create a site notice'
                ),
                'category' => _t(
                    'sitenotice.Category',
                    'Site notices'
                ),
            ]
        ];
    }

    /**
     * Render this object into this template
     */
    public function forTemplate(): string
    {
        return $this->renderWith(Notice::class);
    }

    /**
     * Return the site-wide notice
     */
    public static function get_sitewide_notice(): ?Notice
    {
        $notices = Notice::get();
        $notices = $notices->filter([
            'IsGlobal' => 1,
            'IsActive' => 1
        ]);
        $notices = $notices->sort('IsGlobal DESC');

        return $notices->first();
    }

    /**
     * Specify global template variables
     * @return array
     */
    public static function get_template_global_variables()
    {
        return [
            'SitewideNotice' => 'get_sitewide_notice'
        ];
    }
}
