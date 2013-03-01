<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\Tests\DataModel\User\EntityTest
 */
namespace Malwarebytes\ZendeskBundle\Tests\DataModel\User;
use \ReflectionProperty;
use \ReflectionMethod;

class EntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\User\Entity::getPrimaryKey
     */
    public function testGetPrimaryKey()
    {
        $ticket = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\User\Entity' )
                       ->disableOriginalConstructor()
                       ->setMethods( null )
                       ->getMock();
        $this->assertEquals(
                'id',
                $ticket->getPrimaryKey()
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\User\Entity::getType
     */
    public function testGetType()
    {
        $ticket = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\User\Entity' )
                       ->disableOriginalConstructor()
                       ->setMethods( null )
                       ->getMock();
        $this->assertEquals(
                'user',
                $ticket->getType()
        );
    }
}