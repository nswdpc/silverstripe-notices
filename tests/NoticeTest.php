<?php

namespace NSWDPC\Notices\Tests;

use gorriecoe\LinkField\LinkField;
use gorriecoe\Link\Models\Link;
use NSWDPC\Notices\Notice;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\View\SSViewer;

/**
 * Test the Notice model
 * @author James
 */
class NoticeTest extends SapphireTest
{
    protected $usesDatabase = true;

    protected static $fixture_file = 'NoticeTest.yml';

    protected function setUp() : void
    {
        parent::setUp();
        SSViewer::set_themes(['$public', '$default']);
    }

    public function testIsGlobal()
    {
        $notice = $this->objFromFixture(Notice::class, 'globalnotice');
        $this->assertInstanceOf(Notice::class, $notice);
        $this->assertEquals(1, $notice->IsGlobal);
    }

    public function testIsActive()
    {
        $notice = $this->objFromFixture(Notice::class, 'inactive');
        $this->assertInstanceOf(Notice::class, $notice);
        $this->assertEquals(0, $notice->IsActive);
    }

    public function testSiteWideNotice()
    {
        $sitewideNotice = Notice::get_sitewide_notice();
        $notice = $this->objFromFixture(Notice::class, 'globalnotice');
        $this->assertEquals($notice->ID, $sitewideNotice->ID);
    }

    /**
     * Test template for notice with a link
     */
    public function testTemplate()
    {
        $notice = $this->objFromFixture(Notice::class, 'withlink');
        $template = $notice->forTemplate();
        $this->assertNotEmpty($template);
        $xml = simplexml_load_string($template);
        $this->assertInstanceof(\SimpleXMLElement::class, $xml);
        $this->assertEquals('meta', $xml->getName());

        $linkURL = "";
        if ($link = $notice->Link()) {
            $linkURL = $link->getLinkURL();
        }
        $this->assertNotEmpty($linkURL);
        $attributes = [
            "data-title" => $notice->Title,
            "data-show-title" => strval($notice->ShowTitle == 1 ? 1 : 0),
            "data-description" => $notice->Description,
            "data-autoclose-after" => $notice->AutoCloseAfter,
            "data-is-global" => strval($notice->IsGlobal == 1 ? 1 : 0),
            "data-is-active" => strval($notice->IsActive == 1 ? 1 : 0),
            "data-link" => $linkURL
        ];

        $xmlAttributes = $xml->attributes();
        foreach ($xmlAttributes as $k=>$v) {
            $this->assertEquals($v->__toString(), $attributes[$k], "Attribute '{$k}' does not match expected value '{$v}'");
        }
    }
}
