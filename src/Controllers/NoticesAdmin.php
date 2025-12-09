<?php

namespace NSWDPC\Notices;

use SilverStripe\Admin\ModelAdmin;

/**
 * Provides a  model admin for notice records
 * @author James
 */
class NoticesAdmin extends ModelAdmin
{

    private static string $url_segment = 'site-notices';

    private static string $menu_title = 'Site notices';

    private static string $menu_icon_class = 'font-icon-comment';

    private static array $managed_models = [
        Notice::class
    ];
}
