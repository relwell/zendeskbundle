<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\Tests\DataModel\Ticket\EntityTest
 */
namespace Malwarebytes\ZendeskBundle\Tests\DataModel\Ticket;
use \ReflectionProperty;
use \ReflectionMethod;

class EntityTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPrimaryKey()
    {
        $ticket = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                       ->disableOriginalConstructor()
                       ->setMethods( null )
                       ->getMock();
        $this->assertEquals(
                'id',
                $ticket->getPrimaryKey()
        );
    }
}