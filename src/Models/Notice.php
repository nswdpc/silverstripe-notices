<?php

namespace NSWDPC\Notices;

use gorriecoe\LinkField\LinkField;
use gorriecoe\Link\Models\Link;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\DataList;
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
 */
class Notice extends DataObject implements PermissionProvider, TemplateGlobalProvider
{

    /**
     * @var string
     */
    private static $table_name = "SiteNotice";

    /**
     * @var array
     */
    private static $db = [
        'Title' => 'Varchar(255)',
        'Description' => 'Text',
        'IsGlobal' => 'Boolean',
        'IsActive' => 'Boolean',
        'IsDismissible' => 'Boolean',
        'AutoCloseAfter' => 'Int',// seconds
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Title' => 'Title',
        'IsActive.Nice' => 'Active?',
        'IsGlobal.Nice' => 'Global?',
        'IsDismissible.Nice' => 'Global?',
        'Description' => 'Description',
        'AutoCloseAfter' => 'Auto-close (seconds)',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'Link' => Link::class
    ];

    /**
     * @var array
     */
    private static $owns = [
        'Link'
    ];

    /**
     * @var array
     */
    private static $indexes = [
        'IsGlobal' => true,
        'IsActive' => true
    ];

    /**
     * Post-write operations
     */
    public function onAfterWrite()
    {
        parent::onAfterWrite();
        if ($this->IsGlobal == 1) {
            DB::query("UPDATE `SiteNotice` SET IsGlobal = 0 WHERE ID <> '" . Convert::raw2sql($this->ID) . "'");
        }
    }

    /**
     * @return string
     */
    public function TitleWithGlobalStatus()
    {
        return $this->Title . ($this->IsGlobal == 1 ? " (" . _t('sitenotice.GLOBAL', 'global') . ")" : "");
    }

    /**
     * @return Fieldlist
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

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
                    _t("sitenotice.TITLE", "Site-wide notice")
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
                ->setHTML5(true)
            ]
        );

        return $fields;
    }

    /**
     * @return bool
     */
    public function canView($member = null)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function canEdit($member = null)
    {
        return Permission::check('SITENOTICE_EDIT');
    }

    /**
     * @return bool
     */
    public function canDelete($member = null)
    {
        return Permission::check('SITENOTICE_DELETE');
    }

    /**
     * @return bool
     */
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
    public function forTemplate()
    {
        return $this->renderWith(Notice::class);
    }

    /**
     * Return the site-wide notice
     * @return Notice|null
     */
    public static function get_sitewide_notice() : ?Notice
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
