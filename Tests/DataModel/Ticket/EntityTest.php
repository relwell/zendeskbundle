<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\Tests\DataModel\Ticket\EntityTest
 */
namespace Malwarebytes\ZendeskBundle\Tests\DataModel\Ticket;
use \ReflectionProperty;
use \ReflectionMethod;

class EntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity::getPrimaryKey
     */
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
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity::getType
     */
    public function testGetType()
    {
        $ticket = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                       ->disableOriginalConstructor()
                       ->setMethods( null )
                       ->getMock();
        $this->assertEquals(
                'ticket',
                $ticket->getType()
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity::getComments
     */
    public function testGetComments()
    {
        $ticket = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                       ->disableOriginalConstructor()
                       ->setMethods( null )
                       ->getMock();
        
        $this->assertEquals(
                array(),
                $ticket->getComments()
        );
        $ticket['comments'] = array( 'foo' );
        $this->assertEquals(
                array( 'foo' ),
                $ticket->getComments()
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity::addComment
     */
    public function testAddComment()
    {
        $mockRepo = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository' )
                         ->disableOriginalConstructor()
                         ->setMethods( array( 'addCommentToTicket' ) )
                         ->getMock();
        $mockEntity = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                           ->setConstructorArgs( array( $mockRepo, array( 'foo' => 'bar' ) ) )
                           ->setMethods( null )
                           ->getMock();
        $response = array( 'foo' );
        $mockRepo
            ->expects( $this->once() )
            ->method ( 'addCommentToTicket' )
            ->with   ( $mockEntity, 'foo', true )
            ->will   ( $this->returnValue( $mockEntity ) )
        ;
        $this->assertEquals(
                $mockEntity,
                $mockEntity->addComment( 'foo' )
        );
    }
}