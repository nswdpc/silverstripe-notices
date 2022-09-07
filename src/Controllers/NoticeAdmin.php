<?php

namespace NSWDPC\Notices;

use SilverStripe\Admin\ModelAdmin;

/**
 * Provides a  model admin for notice records
 * @author James
 */
class NoticesAdmin extends ModelAdmin
{

    /**
     * @var string
     */
    private static $url_segment = 'site-notices';

    /**
     * @var string
     */
    private static $menu_title = 'Notices';

    /**
     * @var string
     */
    private static $menu_icon_class = 'font-icon-comment';

    /**
     * @var array
     */
    private static $managed_models = [
        Notice::class
    ];

}
