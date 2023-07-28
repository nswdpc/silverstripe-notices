<?php

namespace NSWDPC\Notices;

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
        'PageNotice' => Notice::class
    ];

    /**
     * @return Fieldlist
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab('Root.Notice', [
            DropdownField::create(
                'PageNoticeID',
                _t('nswds.NOTICE','Page notice'),
                $this->getPageNotices()->map('ID', 'Title')
            )
            ->setEmptyString('Choose an existing notice')
        ]);
    }

    /**
     * Return available notices
     * @return Notice|null
     */
    public function getPageNotices()
    {
        $notices = Notice::get()->filter([
            'IsGlobal' => 0,
            'IsActive' => 1
        ]);

        return $notices->sort('Title');
    }

}
