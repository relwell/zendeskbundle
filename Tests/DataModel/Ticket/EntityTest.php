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
        $ticket = new \Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity( array() );
        $this->assertEquals(
                'id',
                $ticket->getPrimaryKey()
        );
    }
}