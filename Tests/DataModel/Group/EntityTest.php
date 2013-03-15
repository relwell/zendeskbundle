<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\Tests\DataModel\Group\EntityTest
 */
namespace Malwarebytes\ZendeskBundle\Tests\DataModel\Group;
use \ReflectionProperty;
use \ReflectionMethod;

class EntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Group\Entity::getPrimaryKey
     */
    public function testGetPrimaryKey()
    {
        $Group = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Group\Entity' )
                       ->disableOriginalConstructor()
                       ->setMethods( null )
                       ->getMock();
        $this->assertEquals(
                'id',
                $Group->getPrimaryKey()
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Group\Entity::getType
     */
    public function testGetType()
    {
        $Group = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Group\Entity' )
                       ->disableOriginalConstructor()
                       ->setMethods( null )
                       ->getMock();
        $this->assertEquals(
                'group',
                $Group->getType()
        );
    }
}