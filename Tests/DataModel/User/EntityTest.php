<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\Tests\DataModel\User\EntityTest
 */
namespace Malwarebytes\ZendeskBundle\Tests\DataModel\User;
use \ReflectionProperty;
use \ReflectionMethod;

class EntityTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPrimaryKey()
    {
        $ticket = new \Malwarebytes\ZendeskBundle\DataModel\User\Entity( array() );
        $this->assertEquals(
                'id',
                $ticket->getPrimaryKey()
        );
    }
}