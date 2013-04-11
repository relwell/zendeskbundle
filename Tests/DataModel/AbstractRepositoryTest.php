<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\Tests\DataModel\AbstractRepositoryTest
 */
namespace Malwarebytes\ZendeskBundle\Tests\DataModel;
use \ReflectionProperty;
use \ReflectionMethod;

class AbstractRepositoryTest extends \PHPUnit_Framework_TestCase
{
    protected function _configure( $serviceMethods = array(), $repoMethods = array() )
    {
        $this->apiService = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\Service\ApiService' )
                                 ->setMethods( $serviceMethods )
                                 ->disableOriginalConstructor()
                                 ->getMock();
        
        $this->repo = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\AbstractRepository' )
                           ->setMethods( $repoMethods )
                           ->setConstructorArgs( array( $this->apiService ) )
                           ->getMockForAbstractClass();
    }
    
    /**
     * @covers \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::__construct
     */
    public function test__construct()
    {
        $this->_configure();
        $reflApi = new ReflectionProperty( '\Malwarebytes\ZendeskBundle\DataModel\AbstractRepository', '_apiService' );
        $reflApi->setAccessible( true );
        $this->assertEquals(
                $this->apiService,
                $reflApi->getValue( $this->repo )
        );
    }
    
    /**
     * @covers \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::save
     */
    public function testSaveUpdate()
    {
        $entity = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\AbstractEntity' )
                       ->setMethods( array( 'exists' ) )
                       ->disableOriginalConstructor()
                       ->getMockForAbstractClass();
        $this->_configure( array(), array( '_update' ) );
        $entity
            ->expects( $this->once() )
            ->method ( 'exists' )
            ->will   ( $this->returnValue( true ) )
        ;
        $this->repo
            ->expects( $this->once() )
            ->method ( '_update' )
            ->will   ( $this->returnValue( $entity ) )
        ;
        $this->assertEquals(
                $entity,
                $this->repo->save( $entity )
        );
    }
    
    /**
     * @covers \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::save
     */
    public function testSaveCreate()
    {
        $entity = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\AbstractEntity' )
                       ->setMethods( array( 'exists' ) )
                       ->disableOriginalConstructor()
                       ->getMockForAbstractClass();
        $this->_configure( array(), array( '_update' ) );
        $entity
            ->expects( $this->once() )
            ->method ( 'exists' )
            ->will   ( $this->returnValue( false ) )
        ;
        $this->repo
            ->expects( $this->once() )
            ->method ( '_create' )
            ->will   ( $this->returnValue( $entity ) )
        ;
        $this->assertEquals(
                $entity,
                $this->repo->save( $entity )
        );
    }
    
    /**
     * @covers \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::save
     */
    public function testSaveIncomplete()
    {
        $entity = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\AbstractEntity' )
                       ->setMethods( array( 'exists', 'isIncomplete' ) )
                       ->disableOriginalConstructor()
                       ->getMockForAbstractClass();
        $exception = $this->getMockBuilder( '\Exception' )
                          ->disableOriginalConstructor()
                          ->getMock();
        $this->_configure( array(), array( '_update' ) );
        $entity
            ->expects( $this->once() )
            ->method ( 'exists' )
            ->will   ( $this->returnValue( true ) )
        ;
        $entity
            ->expects( $this->once() )
            ->method ( 'isIncomplete' )
            ->will   ( $this->returnValue( true ) )
        ;
        $this->repo
            ->expects( $this->never() )
            ->method ( '_update' )
            ->will   ( $this->returnValue( $entity ) )
        ;
        try {
            $this->repo->save( $entity );
        } catch ( \Exception $e ) {}
        $this->assertInstanceOf(
                '\Exception',
                $e
        );
    }
    
    /**
     * @covers \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::_buildPaginatorFromResponse
     */
    public function test_buildPaginatorFromResponse()
    {
        $this->_configure( array(), array( '_buildFromResponse' ) );
        $response = array( 'next_page' => 'foo' );
        $entities = array( (object) array( 'foo' => 'bar' ) );
        $this->repo
            ->expects( $this->once() )
            ->method ( '_buildFromResponse' )
            ->with   ( $response )
            ->will   ( $this->returnValue( $entities ) )
        ;
        $reflBuild = new ReflectionMethod( '\Malwarebytes\ZendeskBundle\DataModel\AbstractRepository', '_buildPaginatorFromResponse' );
        $reflBuild->setAccessible( true );
        $paginator = $reflBuild->invoke( $this->repo, $response );
        $this->assertInstanceOf(
                'Malwarebytes\ZendeskBundle\DataModel\Paginator',
                $paginator
        );
    }
    
    /**
     * @covers \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::updatePaginator
     */
    public function testUpdatePaginator()
    {
        $mockPaginator = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Paginator' )
                              ->disableOriginalConstructor()
                              ->setMethods( array( 'getNextPage', 'setEntities', 'setNextPage' ) )
                              ->getMock();
        
        $this->_configure( array( 'getNextPage' ), array( '_buildFromResponse' ) );
        $nextPage = 'http://foo.zendesk.com/tickets.json?whatever=yeah';
        $entities = array( (object) array( 'foo' => 'bar' ) );
        $response = array( 'mock response' );
        $mockPaginator
            ->expects( $this->once() )
            ->method ( 'getNextPage' )
            ->will   ( $this->returnValue( $nextPage ) )
        ;
        $this->apiService
            ->expects( $this->once() )
            ->method ( 'getNextPage' )
            ->with   ( $nextPage )
            ->will   ( $this->returnValue( $response ) )
        ;
        $this->repo
            ->expects( $this->once() )
            ->method ( '_buildFromResponse' )
            ->with   ( $response )
            ->will   ( $this->returnValue( $entities ) )
        ;
        $mockPaginator
            ->expects( $this->once() )
            ->method ( 'setEntities' )
            ->with   ( $entities )
            ->will   ( $this->returnValue( $mockPaginator ) )
        ;
        $mockPaginator
            ->expects( $this->once() )
            ->method ( 'setNextPage' )
            ->will   ( $this->returnValue( null ) )
        ;
        $this->assertTrue(
                $this->repo->updatePaginator( $mockPaginator )
        );
    }
    
    /**
     * @covers \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::_validateResponse
     */
    public function testValidateResponse() {
        $this->_configure( array(), array() );
        $response = array( 'error' => 'your mom', 'description' => 'she so fat' );
        $validate = new \ReflectionMethod( '\Malwarebytes\ZendeskBundle\DataModel\AbstractRepository', '_validateResponse' );
        $validate->setAccessible( true );
        try {
            $validate->invoke( $this->repo, $response );
        } catch ( \Malwarebytes\ZendeskBundle\DataModel\ApiResponseException $e ) {}
        $this->assertInstanceOf(
                '\Malwarebytes\ZendeskBundle\DataModel\ApiResponseException',
                $e
        );
        unset( $response['error'] );
        $this->assertTrue(
                $validate->invoke( $this->repo, $response )
        );
    }
    
    /**
     * @covers \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::factory
     */
    public function testFactory() {
        $this->_configure();
        $repo = new \Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository( $this->apiService );
        
        $this->assertInstanceOf(
                'Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity',
                $repo->factory()
        );
    }
}