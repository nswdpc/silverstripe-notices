<?php

namespace NSWDPC\Notices\Tests;

use gorriecoe\LinkField\LinkField;
use gorriecoe\Link\Models\Link;
use NSWDPC\Notices\Notice;
use SilverStripe\Dev\SapphireTest;

/**
 * Test the Notice model
 * @author James
 */
class NoticeTest extends SapphireTest
{

    protected $usesDatabase = true;

    protected static $fixure_file = 'NoticeTest.yml';

    public function testIsGlobal() {
        $notice = $this->objFromFixture(Notice::class, 'globalnotice');
        $this->assertInstanceOf(Notice::class, $notice);
        $this->assertEquals(1, $notice->IsGlobal);
    }

    public function testIsActive() {
        $notice = $this->objFromFixture(Notice::class, 'inactive');
        $this->assertInstanceOf(Notice::class, $notice);
        $this->assertEquals(0, $notice->IsActive);
    }

    public function testSiteWideNotice() {
        $sitewideNotice = Notice::get_sitewide_notice();
        $notice = $this->objFromFixture(Notice::class, 'globalnotice');
        $this->assertEquals($notice->ID, $sitewideNotice->ID);
    }
}
