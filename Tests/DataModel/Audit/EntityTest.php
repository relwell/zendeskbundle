<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\Tests\DataModel\Audit\EntityTest
 */
namespace Malwarebytes\ZendeskBundle\Tests\DataModel\Audit;
use \ReflectionProperty;
use \ReflectionMethod;

class EntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Audit\Entity::getPrimaryKey
     */
    public function testGetPrimaryKey()
    {
        $Group = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Audit\Entity' )
                       ->disableOriginalConstructor()
                       ->setMethods( null )
                       ->getMock();
        $this->assertEquals(
                'id',
                $Group->getPrimaryKey()
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Audit\Entity::getType
     */
    public function testGetType()
    {
        $Group = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Audit\Entity' )
                       ->disableOriginalConstructor()
                       ->setMethods( null )
                       ->getMock();
        $this->assertEquals(
                'audit',
                $Group->getType()
        );
    }
}