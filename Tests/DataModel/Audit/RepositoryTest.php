<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\Test\DataModel\Audit\Respository
 */
namespace Malwarebytes\ZendeskBundle\Test\DataModel\Audit;
use Malwarebytes\ZendeskBundle\DataModel\Audit\Respository, ReflectionProperty, ReflectionMethod;
/**
 * Tests Malwarebytes\ZendeskBundle\DataModel\Audit\Respository
 * @author relwell
 */
class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    protected function _configure( $serviceMethods = array(), $repoMethods = array() )
    {
        $this->apiService = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\Service\ApiService' )
                                 ->setMethods( $serviceMethods )
                                 ->disableOriginalConstructor()
                                 ->getMock();
        
        $this->repo = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Audit\Repository' )
                           ->setMethods( $repoMethods )
                           ->setConstructorArgs( array( $this->apiService ) )
                           ->getMockForAbstractClass();
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Audit\Repository::_buildFromResponse
     */
    public function test_buildFromResponseSingleAudit()
    {
        $this->_configure();
        
        $reflResp = new ReflectionProperty( 'Malwarebytes\ZendeskBundle\DataModel\Audit\Repository', '_currentResponse' );
        $reflResp->setAccessible( true );
        $reflBuild = new ReflectionMethod( 'Malwarebytes\ZendeskBundle\DataModel\Audit\Repository', '_buildFromResponse' );
        $reflBuild->setAccessible( true );
        
        $response = array( 'audit' => array( 'foo' => 'bar' ) );
        
        $entities = $reflBuild->invoke( $this->repo, $response );
        $this->assertEquals(
                1,
                count( $entities )
        );
        $this->assertInstanceOf(
                'Malwarebytes\ZendeskBundle\DataModel\Audit\Entity',
                $entities[0]
        );
        $this->assertEquals(
                $response,
                $reflResp->getValue( $this->repo )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Audit\Repository::_buildFromResponse
     */
    public function test_buildFromResponseManyAudits()
    {
        $this->_configure();
        
        $reflResp = new ReflectionProperty( 'Malwarebytes\ZendeskBundle\DataModel\Audit\Repository', '_currentResponse' );
        $reflResp->setAccessible( true );
        $reflBuild = new ReflectionMethod( 'Malwarebytes\ZendeskBundle\DataModel\Audit\Repository', '_buildFromResponse' );
        $reflBuild->setAccessible( true );
        
        $response = array( 'audits' => array( array( 'foo' => 'bar' ), array( 'baz' => 'qux' ) ) );
        
        $entities = $reflBuild->invoke( $this->repo, $response );
        $this->assertEquals(
                2,
                count( $entities )
        );
        $this->assertInstanceOf(
                'Malwarebytes\ZendeskBundle\DataModel\Audit\Entity',
                $entities[0]
        );
        $this->assertInstanceOf(
                'Malwarebytes\ZendeskBundle\DataModel\Audit\Entity',
                $entities[1]
        );
        $this->assertEquals(
                $response,
                $reflResp->getValue( $this->repo )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Audit\Repository::_buildFromResponse
     */
    public function test_buildFromResponseManyAuditsAsResults()
    {
        $this->_configure();
        
        $reflResp = new ReflectionProperty( 'Malwarebytes\ZendeskBundle\DataModel\Audit\Repository', '_currentResponse' );
        $reflResp->setAccessible( true );
        $reflBuild = new ReflectionMethod( 'Malwarebytes\ZendeskBundle\DataModel\Audit\Repository', '_buildFromResponse' );
        $reflBuild->setAccessible( true );
        
        $response = array( 'results' => array( array( 'foo' => 'bar' ), array( 'baz' => 'qux' ) ) );
        
        $entities = $reflBuild->invoke( $this->repo, $response );
        $this->assertEquals(
                2,
                count( $entities )
        );
        $this->assertInstanceOf(
                'Malwarebytes\ZendeskBundle\DataModel\Audit\Entity',
                $entities[0]
        );
        $this->assertInstanceOf(
                'Malwarebytes\ZendeskBundle\DataModel\Audit\Entity',
                $entities[1]
        );
        $this->assertEquals(
                $response,
                $reflResp->getValue( $this->repo )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Audit\Repository::_create
     * @covers Malwarebytes\ZendeskBundle\DataModel\Audit\Repository::_update
     */
    public function testSave()
    {
        $this->_configure();
        $audit = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Audit\Entity' )
                      ->disableOriginalConstructor()
                      ->setMethods( array( 'exists' ) )
                      ->getMock();
        $refl = new \ReflectionMethod( 'Malwarebytes\ZendeskBundle\DataModel\Audit\Repository', '_create' );
        $refl->setAccessible( true );
        try {
            $refl->invoke( $this->repo, $audit );
        } catch ( \Exception $e ) {}
        $this->assertInstanceOf(
                '\Exception',
                $e
        );
        unset( $e );
        $refl = new \ReflectionMethod( 'Malwarebytes\ZendeskBundle\DataModel\Audit\Repository', '_update' );
        $refl->setAccessible( true );
        try {
            $refl->invoke( $this->repo, $audit );
        } catch ( \Exception $e ) {}
        $this->assertInstanceOf(
                '\Exception',
                $e
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Audit\Repository::setTicket
     */
    public function testSetTicket()
    {
        $this->_configure();
        $ticket = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                       ->disableOriginalConstructor()
                       ->getMock();
        $this->repo->setTicket( $ticket );
        $this->assertAttributeEquals(
                $ticket,
                '_ticket',
                $this->repo
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Audit\Repository::getByDefaultSort
     */
    public function testGetByDefaultSort()
    {
        $this->_configure( array( 'getAuditsForTicket' ), array( '_buildPaginatorFromResponse' ) );
        $ticket = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                       ->disableOriginalConstructor()
                       ->setMethods( array( 'getPrimaryKey', 'offsetGet' ) )
                       ->getMock();
        $paginator = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Paginator' )
                          ->disableOriginalConstructor()
                          ->getMock();
        $this->repo->setTicket( $ticket );
        $this->apiService
            ->expects( $this->once() )
            ->method ( 'getAuditsForTicket' )
            ->with   ( 123 )
            ->will   ( $this->returnValue( array( 'foo' ) ) )
        ;
        $ticket
            ->expects( $this->once() )
            ->method ( 'getPrimaryKey' )
            ->will   ( $this->returnValue( 'id' ) )
        ;
        $ticket
            ->expects( $this->once() )
            ->method ( 'offsetGet' )
            ->with   ( 'id' )
            ->will   ( $this->returnValue( 123 ) )
        ;
        $this->repo
            ->expects( $this->once() )
            ->method ( '_buildPaginatorFromResponse' )
            ->with   ( array( 'foo' ) )
            ->will   ( $this->returnValue( $paginator ) )
        ;
        $this->assertInstanceOf(
                '\Malwarebytes\ZendeskBundle\DataModel\Paginator',
                $this->repo->getByDefaultSort()
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Audit\Repository::getForTicket
     */
    public function testGetForTicket()
    {
        $this->_configure( array( 'getAuditsForTicket' ), array( 'setTicket', '_buildPaginatorFromResponse' ) );
        $ticket = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                       ->disableOriginalConstructor()
                       ->setMethods( array( 'getPrimaryKey', 'offsetGet' ) )
                       ->getMock();
        $paginator = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Paginator' )
                          ->disableOriginalConstructor()
                          ->getMock();
        $this->repo
            ->expects( $this->once() )
            ->method ( 'setTicket' )
            ->with   ( $ticket )
        ;
        $this->apiService
            ->expects( $this->once() )
            ->method ( 'getAuditsForTicket' )
            ->with   ( 123 )
            ->will   ( $this->returnValue( array( 'foo' ) ) )
        ;
        $ticket
            ->expects( $this->once() )
            ->method ( 'getPrimaryKey' )
            ->will   ( $this->returnValue( 'id' ) )
        ;
        $ticket
            ->expects( $this->once() )
            ->method ( 'offsetGet' )
            ->with   ( 'id' )
            ->will   ( $this->returnValue( 123 ) )
        ;
        $this->repo
            ->expects( $this->once() )
            ->method ( '_buildPaginatorFromResponse' )
            ->with   ( array( 'foo' ) )
            ->will   ( $this->returnValue( $paginator ) )
        ;
        $this->assertInstanceOf(
                '\Malwarebytes\ZendeskBundle\DataModel\Paginator',
                $this->repo->getForTicket( $ticket )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Audit\Repository::getCommentsForTicket
     */
    public function testGetCommentsForTicket()
    {
        $this->_configure( null, array( 'getForTicket' ) );
        
        $ticket = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                       ->disableOriginalConstructor()
                       ->getMock();
        
        $audit = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Audit\Entity' )
                      ->disableOriginalConstructor()
                      ->setMethods( array( 'offsetGet', 'offsetExists' ) )
                      ->getMock();
        
        $paginator = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Paginator' )
                          ->setConstructorArgs( array( $this->repo, array( $audit ) ) )
                          ->setMethods( null )
                          ->getMock();
        
        $this->repo
            ->expects( $this->once() )
            ->method ( 'getForTicket' )
            ->with   ( $ticket )
            ->will   ( $this->returnValue( $paginator ) )
        ;
        $audit
            ->expects( $this->any() )
            ->method ( 'offsetExists' )
            ->with   ( 'events' )
            ->will   ( $this->returnValue( true ) )
        ;
        $audit
            ->expects( $this->any() )
            ->method ( 'offsetGet' )
            ->with   ( 'events' )
            ->will   ( $this->returnValue( array( array( 'type' => 'Comment' ), array( 'type' => 'Not A Comment' ) ) ) )
        ;
        $result = $this->repo->getCommentsForTicket( $ticket );
        $this->assertContainsOnly(
                'Malwarebytes\ZendeskBundle\DataModel\Audit\Comment',
                $result
        );
        $this->assertEquals(
                1,
                count( $result )
        );
    }
    
    public function testGetById()
    {
        $this->_configure( array( 'getAudit' ), array( 'setTicket', 'getForTicket', '_buildFromResponse' ) );
        $ticket = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                       ->disableOriginalConstructor()
                       ->setMethods( array( 'getPrimaryKey', 'offsetGet' ) )
                       ->getMock();
        $audit = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Audit\Entity' )
                      ->disableOriginalConstructor()
                      ->getMock();
        try {
            $this->repo->getById( 123 );
        } catch ( \Exception $e ) {}
        $this->assertInstanceOf(
                '\Exception',
                $e
        );
        $this->repo
            ->expects( $this->once() )
            ->method ( 'setTicket' )
            ->with   ( $ticket )
        ;
        $ticket
            ->expects( $this->once() )
            ->method ( 'offsetGet' )
            ->with   ( 'id' )
            ->will   ( $this->returnValue( 123 ) )
        ;
        $this->apiService
            ->expects( $this->once() )
            ->method ( 'getAudit' )
            ->with   ( 123, 234 )
            ->will   ( $this->returnValue( array( 'foo' ) ) )
        ;
        $this->repo
            ->expects( $this->once() )
            ->method ( '_buildFromResponse' )
            ->with   ( array( 'foo' ) )
            ->will   ( $this->returnValue( array( $audit ) ) )
        ;
        $ticketRefl = new \ReflectionProperty( 'Malwarebytes\ZendeskBundle\DataModel\Audit\Repository', '_ticket' );
        $ticketRefl->setAccessible( true );
        $ticketRefl->setValue( $this->repo, $ticket );
        $this->assertEquals(
                $audit,
                $this->repo->getById( 234, $ticket )
        );
    }
}