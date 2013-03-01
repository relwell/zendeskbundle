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
     * @covers Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository::_buildFromResponse
     */
    public function test_buildFromResponseManyTicketsAsResults()
    {
        $this->_configure();
        
        $reflResp = new ReflectionProperty( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository', '_currentResponse' );
        $reflResp->setAccessible( true );
        $reflBuild = new ReflectionMethod( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository', '_buildFromResponse' );
        $reflBuild->setAccessible( true );
        
        $response = array( 'results' => array( array( 'foo' => 'bar' ), array( 'baz' => 'qux' ) ) );
        
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
        $ticketFields = array( 'ticket' => array( 'foo' => 'bar' ) );
        $this->_configure( array( 'createTicket' ) );
        $mockEntity = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                           ->setConstructorArgs( array( $this->repo, $ticketFields ) )
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
     * @covers Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository::_update
     */
    public function testUpdate()
    {
        $ticketFields = array( 'id' => 123, 'foo' => 'bar' );
        $this->_configure( array( 'updateTicket' ) );
        $mockEntity = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                           ->setConstructorArgs( array( $this->repo, $ticketFields ) )
                           ->setMethods( array( 'toArray', 'setFields', 'offsetGet' ) )
                           ->getMock();
        $savedTicketArray = array( 'id' => 123, 'foo' => 'bar' );
        $response = array( 'ticket' => $savedTicketArray );
        $mockEntity
            ->expects( $this->once() )
            ->method ( 'toArray' )
            ->will   ( $this->returnValue( array( 'ticket' => $ticketFields ) ) )
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
            ->with   ( 123, array( 'ticket' => $ticketFields ) )
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
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository::getTicketsRequestedByUser
     */
    public function testGetTicketsRequestedByEmptyUser()
    {
        $this->_configure( array( 'getTicketsRequestedByUser' ), array( '_buildPaginatorFromResponse' ) );
        $user = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\User\Entity' )
                     ->setConstructorArgs( array( $this->repo, array( 'id' => 123 ) ) )
                     ->setMethods( array( 'exists' ) )
                     ->getMock();
        $mockPaginator = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Paginator' )
                              ->disableOriginalConstructor()
                              ->getMock();
        
        $mockResponse = array( "deosn't matter" );
        
        $user
            ->expects( $this->at( 0 ) )
            ->method ( 'exists' )
            ->will   ( $this->returnValue( false ) )
        ;
        $this->assertEquals(
                array(),
                $this->repo->getTicketsRequestedByUser( $user ),
                "A non-existent user does not have tickets, so return an empty array."
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository::getTicketsRequestedByUser
     */
    public function testGetTicketsRequestedByRealUser()
    {
        $this->_configure( array( 'getTicketsRequestedByUser' ), array( '_buildPaginatorFromResponse' ) );
        $user = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\User\Entity' )
                     ->setConstructorArgs( array( $this->repo, array( 'id' => 123 ) ) )
                     ->setMethods( array( 'exists' ) )
                     ->getMock();
        $mockPaginator = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Paginator' )
                              ->disableOriginalConstructor()
                              ->getMock();
        
        $mockResponse = array( "deosn't matter" );
        $user
            ->expects( $this->once() )
            ->method ( 'exists' )
            ->will   ( $this->returnValue( true ) )
        ;
        $this->apiService
            ->expects( $this->once() )
            ->method ( 'getTicketsRequestedByUser' )
            ->with   ( 123 )
            ->will   ( $this->returnValue ( $mockResponse ) )
        ;
        $this->repo
            ->expects( $this->once() )
            ->method ( '_buildPaginatorFromResponse' )
            ->with   ( $mockResponse )
            ->will   ( $this->returnValue( $mockPaginator ) )
        ;
        $this->assertEquals(
                $mockPaginator,
                $this->repo->getTicketsRequestedByUser( $user )
        );
    }
    
    public function testAddCommentToTicketNotExists()
    {
        $this->_configure( array( 'addCommentToTicket' ) );
        $mockEntity = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                           ->disableOriginalConstructor()
                           ->setMethods( array( 'exists', 'setFields' ) )
                           ->getMock();
        $mockEntity
            ->expects( $this->once() )
            ->method ( 'exists' )
            ->will   ( $this->returnValue( false ) )
        ;
        try {
            $this->repo->addCommentToTicket( $mockEntity, 'foo' );
        } catch ( \Exception $e ) {}
        $this->assertInstanceOf(
                '\Exception',
                $e
        );
    }
    
    public function testAddCommentToTicketExists()
    {
        $this->_configure( array( 'addCommentToTicket' ) );
        $mockEntity = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                           ->setConstructorArgs( array( $this->repo, array( 'id' => 123 ) ) )
                           ->setMethods( array( 'exists', 'setFields', 'offsetGet' ) )
                           ->getMock();
        $fields = array( 'foo' => 'bar' );
        $response = array( 'ticket' => $fields );
        $mockEntity
            ->expects( $this->once() )
            ->method ( 'exists' )
            ->will   ( $this->returnValue( true ) )
        ;
        $mockEntity
            ->expects( $this->once() )
            ->method ( 'offsetGet' )
            ->with   ( 'id' )
            ->will   ( $this->returnValue( 123 ) )
        ;
        $this->apiService
            ->expects( $this->once() )
            ->method ( 'addCommentToTicket' )
            ->with   ( 123, 'foo', true )
            ->will   ( $this->returnValue( $response ) )
        ;
        $mockEntity
            ->expects( $this->once() )
            ->method ( 'setFields' )
            ->with   ( $fields )
        ;
        $this->assertEquals(
                $mockEntity,
                $this->repo->addCommentToTicket( $mockEntity, 'foo' )
        );
    }
}