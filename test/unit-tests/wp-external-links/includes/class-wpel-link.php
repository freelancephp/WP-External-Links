<?php
include_once realpath(__DIR__ . '/../../../libs/class-wptest-unit-base.php');
include_once realpath(__DIR__ . '/../../../../wp-external-links/libs/fwp/class-fwp-html-element.php');
include_once realpath(__DIR__ . '/../../../../wp-external-links/includes/class-wpel-link.php');


class WPEL_LinkTest extends WPTest_Unit_Base
{

    protected function setUp()
    {
    }

    protected function tearDown()
    {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//            'This test has not been implemented yet.'
//        );
    }

    public function testSetExternal_NonExternal_Set()
    {
        $link = new WPEL_Link( 'a', 'Some Text' );

        $link->set_external();
        $attr_value = $link->get_attr('data-wpel-link');
        $this->assertSame('external', $attr_value);
    }

    public function testSetExternal_AlreadyExternal_Set()
    {
        $link = new WPEL_Link( 'a', 'Some Text' );
        $link->set_attr('data-wpel-link', 'external');

        $link->set_external();
        $attr_value = $link->get_attr('data-wpel-link');
        $this->assertSame('external', $attr_value);
    }

    public function testIsExternal_NonExternal_ReturnFalse()
    {
        $link = new WPEL_Link( 'a', 'Some Text' );
        $result = $link->is_external();
        $this->assertFalse($result);
    }

    public function testIsExternal_ExternalDataAttr_ReturnTrue()
    {
        $link = new WPEL_Link( 'a', 'Some Text' );
        $link->set_attr('data-wpel-link', 'external');

        $result = $link->is_external();
        $this->assertTrue($result);
    }

    public function testIsExternal_RelHasExternal_ReturnTrue()
    {
        $link = new WPEL_Link( 'a', 'Some Text' );
        $link->set_attr('rel', 'external');

        $result = $link->is_external();
        $this->assertTrue($result);
    }

    public function testSetInternal_NonInternal_Set()
    {
        $link = new WPEL_Link( 'a', 'Some Text' );

        $link->set_internal();
        $attr_value = $link->get_attr('data-wpel-link');
        $this->assertSame('internal', $attr_value);
    }

    public function testSetInternal_AlreadyInternal_Set()
    {
        $link = new WPEL_Link( 'a', 'Some Text' );
        $link->set_attr('data-wpel-link', 'internal');

        $link->set_internal();
        $attr_value = $link->get_attr('data-wpel-link');
        $this->assertSame('internal', $attr_value);
    }

    public function testIsInternal_NonInternal_ReturnFalse()
    {
        $link = new WPEL_Link( 'a', 'Some Text' );
        $result = $link->is_internal();
        $this->assertFalse($result);
    }

    public function testIsInternal_InternalDataAttr_ReturnTrue()
    {
        $link = new WPEL_Link( 'a', 'Some Text' );
        $link->set_attr('data-wpel-link', 'internal');

        $result = $link->is_internal();
        $this->assertTrue($result);
    }

    public function testSetExclude_NonExclude_Set()
    {
        $link = new WPEL_Link( 'a', 'Some Text' );

        $link->set_exclude();
        $attr_value = $link->get_attr('data-wpel-link');
        $this->assertSame('exclude', $attr_value);
    }

    public function testSetExclude_AlreadyExclude_Set()
    {
        $link = new WPEL_Link( 'a', 'Some Text' );
        $link->set_attr('data-wpel-link', 'exclude');

        $link->set_exclude();
        $attr_value = $link->get_attr('data-wpel-link');
        $this->assertSame('exclude', $attr_value);
    }

    public function testIsExclude_NonExclude_ReturnFalse()
    {
        $link = new WPEL_Link( 'a', 'Some Text' );
        $result = $link->is_exclude();
        $this->assertFalse($result);
    }

    public function testIsExclude_ExcludeDataAttr_ReturnTrue()
    {
        $link = new WPEL_Link( 'a', 'Some Text' );
        $link->set_attr('data-wpel-link', 'exclude');

        $result = $link->is_exclude();
        $this->assertTrue($result);
    }

    public function testSetIgnore_NonIgnore_Set()
    {
        $link = new WPEL_Link( 'a', 'Some Text' );

        $link->set_ignore();
        $attr_value = $link->get_attr('data-wpel-link');
        $this->assertSame('ignore', $attr_value);
    }

    public function testSetIgnore_AlreadyIgnore_Set()
    {
        $link = new WPEL_Link( 'a', 'Some Text' );
        $link->set_attr('data-wpel-link', 'ignore');

        $link->set_ignore();
        $attr_value = $link->get_attr('data-wpel-link');
        $this->assertSame('ignore', $attr_value);
    }

    public function testIsIgnore_NonIgnore_ReturnFalse()
    {
        $link = new WPEL_Link( 'a', 'Some Text' );
        $result = $link->is_ignore();
        $this->assertFalse($result);
    }

    public function testIsIgnore_IgnoreDataAttr_ReturnTrue()
    {
        $link = new WPEL_Link( 'a', 'Some Text' );
        $link->set_attr('data-wpel-link', 'ignore');

        $result = $link->is_ignore();
        $this->assertTrue($result);
    }

    public function testIsMailto_NoMailto_ReturnFalse()
    {
        $link = new WPEL_Link( 'a', 'Some Text' );
        $result = $link->is_mailto();
        $this->assertFalse($result);
    }

    public function testIsMailto_URL_ReturnFalse()
    {
        $link = new WPEL_Link( 'a', 'Some Text' );
        $link->set_attr('href', 'http://somedomain.com');
        $result = $link->is_mailto();
        $this->assertFalse($result);
    }

    public function testIsMailto_IsMailto_ReturnTrue()
    {
        $link = new WPEL_Link( 'a', 'Some Text' );
        $link->set_attr('href', 'mailto:some@domain.com');
        $result = $link->is_mailto();
        $this->assertTrue($result);
    }

}

/*?>*/
