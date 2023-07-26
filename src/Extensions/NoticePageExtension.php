<?php

namespace NSWDPC\Notices\Extensions;

use Page;
use Silverstripe\ORM\DataExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Admin\AdminRootController;
use SilverStripe\Core\Config\Config;
use NSWDPC\Notices\Notice;
use SilverStripe\Forms\DropdownField;

/**
 * Provides a Notice page extension
 * @author Mark
 */
class NoticePageExtension extends DataExtension
{

    /**
     * @var array
     */
    private static $has_one = [
        'Notice' => Notice::class
    ];

    /**
     * @return Fieldlist
     */
    public function updateCMSFields(FieldList $fields)
    {
        $noticeAdminLink = AdminRootController::admin_url() . Config::inst()->get('NSWDPC\Notices\NoticesAdmin', 'url_segment');

        $fields->addFieldsToTab('Root.Notice', [
            DropdownField::create(
                'NoticeID',
                _t('nswds.NOTICE','Notice'),
                Notice::get()->map('ID', 'Title')
            )
            ->setEmptyString('Choose an existing notice')
            ->setDescription("<a href=\"$noticeAdminLink\">Manage notices</a>")
        ]);
    }

}
