<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\Tests\DataModel\Ticket\RepositoryTest
 */
namespace Malwarebytes\ZendeskBundle\Tests\DataModel\Ticket;
use \ReflectionProperty;
use \ReflectionMethod;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    protected function _configure( $serviceMethods = array(), $repoMethods = array() )
    {
        $this->apiService = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\Service\ApiService' )
                                 ->setMethods( $serviceMethods )
                                 ->disableOriginalConstructor()
                                 ->getMock();
        
        $this->repo = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository' )
                           ->setMethods( $repoMethods )
                           ->setConstructorArgs( array( $this->apiService ) )
                           ->getMockForAbstractClass();
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository::_buildFromResponse
     */
    public function test_buildFromResponseSingleTicket()
    {
        $this->_configure();
        
        $reflResp = new ReflectionProperty( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository', '_currentResponse' );
        $reflResp->setAccessible( true );
        $reflBuild = new ReflectionMethod( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository', '_buildFromResponse' );
        $reflBuild->setAccessible( true );
        
        $response = array( 'ticket' => array( 'foo' => 'bar' ) );
        
        $entities = $reflBuild->invoke( $this->repo, $response );
        $this->assertEquals(
                1,
                count( $entities )
        );
        $this->assertInstanceOf(
                'Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity',
                $entities[0]
        );
        $this->assertEquals(
                $response,
                $reflResp->getValue( $this->repo )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository::_buildFromResponse
     */
    public function test_buildFromResponseManyTickets()
    {
        $this->_configure();
        
        $reflResp = new ReflectionProperty( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository', '_currentResponse' );
        $reflResp->setAccessible( true );
        $reflBuild = new ReflectionMethod( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository', '_buildFromResponse' );
        $reflBuild->setAccessible( true );
        
        $response = array( 'tickets' => array( array( 'foo' => 'bar' ), array( 'baz' => 'qux' ) ) );
        
        $entities = $reflBuild->invoke( $this->repo, $response );
        $this->assertEquals(
                2,
                count( $entities )
        );
        $this->assertInstanceOf(
                'Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity',
                $entities[0]
        );
        $this->assertInstanceOf(
                'Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity',
                $entities[1]
        );
        $this->assertEquals(
                $response,
                $reflResp->getValue( $this->repo )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository::_create
     */
    public function testCreate()
    {
        $ticketFields = array( 'foo' => 'bar' );
        $this->_configure( array( 'createTicket' ) );
        $mockEntity = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                           ->setConstructorArgs( array( $ticketFields ) )
                           ->setMethods( array( 'toArray', 'setFields' ) )
                           ->getMock();
        $savedTicketArray = array( 'id' => 123, 'foo' => 'bar' );
        $response = array( 'ticket' => $savedTicketArray );
        $mockEntity
            ->expects( $this->once() )
            ->method ( 'toArray' )
            ->will   ( $this->returnValue( $ticketFields ) )
        ;
        $this->apiService
            ->expects( $this->once() )
            ->method ( 'createTicket' )
            ->with   ( $ticketFields )
            ->will   ( $this->returnValue( $response ) )
        ;
        $mockEntity
            ->expects( $this->once() )
            ->method ( 'setFields' )
            ->with   ( $savedTicketArray )
        ;
        $reflCreate = new ReflectionMethod( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository', '_create' );
        $reflCreate->setAccessible( true );
        $this->assertEquals(
                $mockEntity,
                $reflCreate->invoke( $this->repo, $mockEntity )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository::_create
     */
    public function testUpdate()
    {
        $ticketFields = array( 'id' => 123, 'foo' => 'bar' );
        $this->_configure( array( 'updateTicket' ) );
        $mockEntity = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                           ->setConstructorArgs( array( $ticketFields ) )
                           ->setMethods( array( 'toArray', 'setFields', 'offsetGet' ) )
                           ->getMock();
        $savedTicketArray = array( 'id' => 123, 'foo' => 'bar' );
        $response = array( 'ticket' => $savedTicketArray );
        $mockEntity
            ->expects( $this->once() )
            ->method ( 'toArray' )
            ->will   ( $this->returnValue( $ticketFields ) )
        ;
        $mockEntity
            ->expects( $this->once() )
            ->method ( 'offsetGet' )
            ->with   ( 'id' )
            ->will   ( $this->returnValue( 123 ) )
        ;
        $this->apiService
            ->expects( $this->once() )
            ->method ( 'updateTicket' )
            ->with   ( 123, $ticketFields )
            ->will   ( $this->returnValue( $response ) )
        ;
        $mockEntity
            ->expects( $this->once() )
            ->method ( 'setFields' )
            ->with   ( $savedTicketArray )
        ;
        $reflUpdate = new ReflectionMethod( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository', '_update' );
        $reflUpdate->setAccessible( true );
        $this->assertEquals(
                $mockEntity,
                $reflUpdate->invoke( $this->repo, $mockEntity )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository::getByDefaultSort
     */
    public function testGetByDefaultSort()
    {
        $this->_configure( array( 'getTickets' ), array( '_buildPaginatorFromResponse' ) );
        $mockPaginator = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Paginator' )
                              ->disableOriginalConstructor()
                              ->getMock();
        $response = array( 'foo' );
        $this->apiService
            ->expects( $this->once() )
            ->method ( 'getTickets' )
            ->will   ( $this->returnValue( $response ) )
        ;
        $this->repo
            ->expects( $this->once() )
            ->method ( '_buildPaginatorFromResponse' )
            ->with   ( $response )
            ->will   ( $this->returnValue( $mockPaginator ) )
        ;
        $this->assertEquals(
                $mockPaginator,
                $this->repo->getByDefaultSort()
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository::getById
     */
    public function testGetById()
    {
        $this->_configure( array( 'getTicket' ), array( '_buildFromResponse' ) );
        $mockEntity = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                           ->disableOriginalConstructor()
                           ->getMock();
        $response = array( 'foo' );
        $this->apiService
            ->expects( $this->once() )
            ->method ( 'getTicket' )
            ->with   ( 123 )
            ->will   ( $this->returnValue( $response ) )
        ;
        $this->repo
            ->expects( $this->once() )
            ->method ( '_buildFromResponse' )
            ->with   ( $response )
            ->will   ( $this->returnValue( array( $mockEntity ) ) )
        ;
        $this->assertEquals(
                $mockEntity,
                $this->repo->getById( 123 )
        );
    }
}