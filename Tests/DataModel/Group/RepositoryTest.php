<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\Tests\DataModel\Group\RepositoryTest
 */
namespace Malwarebytes\ZendeskBundle\Tests\DataModel\Group;
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
        
        $this->repo = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Group\Repository' )
                           ->setMethods( $repoMethods )
                           ->setConstructorArgs( array( $this->apiService ) )
                           ->getMockForAbstractClass();
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Group\Repository::_buildFromResponse
     */
    public function test_buildFromResponseSingleGroup()
    {
        $this->_configure();
        
        $reflResp = new ReflectionProperty( 'Malwarebytes\ZendeskBundle\DataModel\Group\Repository', '_currentResponse' );
        $reflResp->setAccessible( true );
        $reflBuild = new ReflectionMethod( 'Malwarebytes\ZendeskBundle\DataModel\Group\Repository', '_buildFromResponse' );
        $reflBuild->setAccessible( true );
        
        $response = array( 'group' => array( 'foo' => 'bar' ) );
        
        $entities = $reflBuild->invoke( $this->repo, $response );
        $this->assertEquals(
                1,
                count( $entities )
        );
        $this->assertInstanceOf(
                'Malwarebytes\ZendeskBundle\DataModel\Group\Entity',
                $entities[0]
        );
        $this->assertEquals(
                $response,
                $reflResp->getValue( $this->repo )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Group\Repository::_buildFromResponse
     */
    public function test_buildFromResponseManyGroups()
    {
        $this->_configure();
        
        $reflResp = new ReflectionProperty( 'Malwarebytes\ZendeskBundle\DataModel\Group\Repository', '_currentResponse' );
        $reflResp->setAccessible( true );
        $reflBuild = new ReflectionMethod( 'Malwarebytes\ZendeskBundle\DataModel\Group\Repository', '_buildFromResponse' );
        $reflBuild->setAccessible( true );
        
        $response = array( 'groups' => array( array( 'foo' => 'bar' ), array( 'baz' => 'qux' ) ) );
        
        $entities = $reflBuild->invoke( $this->repo, $response );
        $this->assertEquals(
                2,
                count( $entities )
        );
        $this->assertInstanceOf(
                'Malwarebytes\ZendeskBundle\DataModel\Group\Entity',
                $entities[0]
        );
        $this->assertInstanceOf(
                'Malwarebytes\ZendeskBundle\DataModel\Group\Entity',
                $entities[1]
        );
        $this->assertEquals(
                $response,
                $reflResp->getValue( $this->repo )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Group\Repository::_buildFromResponse
     */
    public function test_buildFromResponseManyGroupsAsResults()
    {
        $this->_configure();
        
        $reflResp = new ReflectionProperty( 'Malwarebytes\ZendeskBundle\DataModel\Group\Repository', '_currentResponse' );
        $reflResp->setAccessible( true );
        $reflBuild = new ReflectionMethod( 'Malwarebytes\ZendeskBundle\DataModel\Group\Repository', '_buildFromResponse' );
        $reflBuild->setAccessible( true );
        
        $response = array( 'results' => array( array( 'foo' => 'bar' ), array( 'baz' => 'qux' ) ) );
        
        $entities = $reflBuild->invoke( $this->repo, $response );
        $this->assertEquals(
                2,
                count( $entities )
        );
        $this->assertInstanceOf(
                'Malwarebytes\ZendeskBundle\DataModel\Group\Entity',
                $entities[0]
        );
        $this->assertInstanceOf(
                'Malwarebytes\ZendeskBundle\DataModel\Group\Entity',
                $entities[1]
        );
        $this->assertEquals(
                $response,
                $reflResp->getValue( $this->repo )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Group\Repository::_create
     */
    public function testCreate()
    {
        $groupFields = array( 'group' => array( 'foo' => 'bar' ) );
        $this->_configure( array( 'createGroup' ) );
        $mockEntity = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Group\Entity' )
                           ->setConstructorArgs( array( $this->repo, $groupFields ) )
                           ->setMethods( array( 'toArray', 'setFields' ) )
                           ->getMock();
        $savedGroupArray = array( 'id' => 123, 'foo' => 'bar' );
        $response = array( 'group' => $savedGroupArray );
        $mockEntity
            ->expects( $this->once() )
            ->method ( 'toArray' )
            ->will   ( $this->returnValue( $groupFields ) )
        ;
        $this->apiService
            ->expects( $this->once() )
            ->method ( 'createGroup' )
            ->with   ( $groupFields )
            ->will   ( $this->returnValue( $response ) )
        ;
        $mockEntity
            ->expects( $this->once() )
            ->method ( 'setFields' )
            ->with   ( $savedGroupArray )
        ;
        $reflCreate = new ReflectionMethod( 'Malwarebytes\ZendeskBundle\DataModel\Group\Repository', '_create' );
        $reflCreate->setAccessible( true );
        $this->assertEquals(
                $mockEntity,
                $reflCreate->invoke( $this->repo, $mockEntity )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Group\Repository::_update
     */
    public function testUpdate()
    {
        $groupFields = array( 'id' => 123, 'foo' => 'bar' );
        $this->_configure( array( 'updateGroup' ) );
        $mockEntity = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Group\Entity' )
                           ->setConstructorArgs( array( $this->repo, $groupFields ) )
                           ->setMethods( array( 'toArray', 'setFields', 'offsetGet' ) )
                           ->getMock();
        $savedGroupArray = array( 'id' => 123, 'foo' => 'bar' );
        $response = array( 'group' => $savedGroupArray );
        $mockEntity
            ->expects( $this->once() )
            ->method ( 'toArray' )
            ->will   ( $this->returnValue( array( 'group' => $groupFields ) ) )
        ;
        $mockEntity
            ->expects( $this->once() )
            ->method ( 'offsetGet' )
            ->with   ( 'id' )
            ->will   ( $this->returnValue( 123 ) )
        ;
        $this->apiService
            ->expects( $this->once() )
            ->method ( 'updateGroup' )
            ->with   ( 123, array( 'group' => $groupFields ) )
            ->will   ( $this->returnValue( $response ) )
        ;
        $mockEntity
            ->expects( $this->once() )
            ->method ( 'setFields' )
            ->with   ( $savedGroupArray )
        ;
        $reflUpdate = new ReflectionMethod( 'Malwarebytes\ZendeskBundle\DataModel\Group\Repository', '_update' );
        $reflUpdate->setAccessible( true );
        $this->assertEquals(
                $mockEntity,
                $reflUpdate->invoke( $this->repo, $mockEntity )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Group\Repository::getByDefaultSort
     */
    public function testGetByDefaultSort()
    {
        $this->_configure( array( 'getGroups' ), array( '_buildPaginatorFromResponse' ) );
        $mockPaginator = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Paginator' )
                              ->disableOriginalConstructor()
                              ->getMock();
        $response = array( 'foo' );
        $this->apiService
            ->expects( $this->once() )
            ->method ( 'getGroups' )
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
     * @covers Malwarebytes\ZendeskBundle\DataModel\Group\Repository::getById
     */
    public function testGetById()
    {
        $this->_configure( array( 'getGroupById' ), array( '_buildFromResponse' ) );
        $mockEntity = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Group\Entity' )
                           ->disableOriginalConstructor()
                           ->getMock();
        $response = array( 'foo' );
        $this->apiService
            ->expects( $this->once() )
            ->method ( 'getGroupById' )
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