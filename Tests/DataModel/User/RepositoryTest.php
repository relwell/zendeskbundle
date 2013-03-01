<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\Tests\DataModel\User\RepositoryTest
 */
namespace Malwarebytes\ZendeskBundle\Tests\DataModel\User;
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
        
        $this->repo = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\User\Repository' )
                           ->setMethods( $repoMethods )
                           ->setConstructorArgs( array( $this->apiService ) )
                           ->getMockForAbstractClass();
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\User\Repository::_buildFromResponse
     */
    public function test_buildFromResponseSingleUser()
    {
        $this->_configure();
        
        $reflResp = new ReflectionProperty( 'Malwarebytes\ZendeskBundle\DataModel\User\Repository', '_currentResponse' );
        $reflResp->setAccessible( true );
        $reflBuild = new ReflectionMethod( 'Malwarebytes\ZendeskBundle\DataModel\User\Repository', '_buildFromResponse' );
        $reflBuild->setAccessible( true );
        
        $response = array( 'user' => array( 'foo' => 'bar' ) );
        
        $entities = $reflBuild->invoke( $this->repo, $response );
        $this->assertEquals(
                1,
                count( $entities )
        );
        $this->assertInstanceOf(
                'Malwarebytes\ZendeskBundle\DataModel\User\Entity',
                $entities[0]
        );
        $this->assertEquals(
                $response,
                $reflResp->getValue( $this->repo )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\User\Repository::_buildFromResponse
     */
    public function test_buildFromResponseManyUsers()
    {
        $this->_configure();
        
        $reflResp = new ReflectionProperty( 'Malwarebytes\ZendeskBundle\DataModel\User\Repository', '_currentResponse' );
        $reflResp->setAccessible( true );
        $reflBuild = new ReflectionMethod( 'Malwarebytes\ZendeskBundle\DataModel\User\Repository', '_buildFromResponse' );
        $reflBuild->setAccessible( true );
        
        $response = array( 'users' => array( array( 'foo' => 'bar' ), array( 'baz' => 'qux' ) ) );
        
        $entities = $reflBuild->invoke( $this->repo, $response );
        $this->assertEquals(
                2,
                count( $entities )
        );
        $this->assertInstanceOf(
                'Malwarebytes\ZendeskBundle\DataModel\User\Entity',
                $entities[0]
        );
        $this->assertInstanceOf(
                'Malwarebytes\ZendeskBundle\DataModel\User\Entity',
                $entities[1]
        );
        $this->assertEquals(
                $response,
                $reflResp->getValue( $this->repo )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\User\Repository::_buildFromResponse
     */
    public function test_buildFromResponseManyUsersAsResult()
    {
        $this->_configure();
        
        $reflResp = new ReflectionProperty( 'Malwarebytes\ZendeskBundle\DataModel\User\Repository', '_currentResponse' );
        $reflResp->setAccessible( true );
        $reflBuild = new ReflectionMethod( 'Malwarebytes\ZendeskBundle\DataModel\User\Repository', '_buildFromResponse' );
        $reflBuild->setAccessible( true );
        
        $response = array( 'results' => array( array( 'foo' => 'bar' ), array( 'baz' => 'qux' ) ) );
        
        $entities = $reflBuild->invoke( $this->repo, $response );
        $this->assertEquals(
                2,
                count( $entities )
        );
        $this->assertInstanceOf(
                'Malwarebytes\ZendeskBundle\DataModel\User\Entity',
                $entities[0]
        );
        $this->assertInstanceOf(
                'Malwarebytes\ZendeskBundle\DataModel\User\Entity',
                $entities[1]
        );
        $this->assertEquals(
                $response,
                $reflResp->getValue( $this->repo )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\User\Repository::_create
     */
    public function testCreate()
    {
        $userFields = array( 'foo' => 'bar' );
        $this->_configure( array( 'createUser' ) );
        $mockEntity = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\User\Entity' )
                           ->setConstructorArgs( array( $this->repo, $userFields ) )
                           ->setMethods( array( 'toArray', 'setFields' ) )
                           ->getMock();
        $savedUserArray = array( 'id' => 123, 'foo' => 'bar' );
        $response = array( 'user' => $savedUserArray );
        $reflCreate = new ReflectionMethod( 'Malwarebytes\ZendeskBundle\DataModel\User\Repository', '_create' );
        try {
            $reflCreate->invoke( $this->repo, $mockEntity );
        } catch ( \Exception $e ) {}
        $this->assertInstanceOf(
                '\Exception',
                $e,
                "Trying to create a user without a name and email should result in an exception."
        );
        $userFields['name'] = "Joe Blow";
        $userFields['email'] = "joe@blow.com";
        
        $mockEntity
            ->expects( $this->once() )
            ->method ( 'toArray' )
            ->will   ( $this->returnValue( array( 'user' => $userFields ) ) )
        ;
        $this->apiService
            ->expects( $this->once() )
            ->method ( 'createUser' )
            ->with   ( array( 'user' => $userFields ) )
            ->will   ( $this->returnValue( $response ) )
        ;
        $mockEntity
            ->expects( $this->once() )
            ->method ( 'setFields' )
            ->with   ( $savedUserArray )
        ;
        
        $reflCreate->setAccessible( true );
        $this->assertEquals(
                $mockEntity,
                $reflCreate->invoke( $this->repo, $mockEntity )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\User\Repository::_update
     */
    public function testUpdate()
    {
        $userFields = array( 'id' => 123, 'foo' => 'bar' );
        $this->_configure( array( 'updateUser' ) );
        $mockEntity = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\User\Entity' )
                           ->setConstructorArgs( array( $this->repo, $userFields ) )
                           ->setMethods( array( 'toArray', 'setFields', 'offsetGet' ) )
                           ->getMock();
        $savedUserArray = array( 'id' => 123, 'foo' => 'bar' );
        $response = array( 'user' => $savedUserArray );
        $mockEntity
            ->expects( $this->once() )
            ->method ( 'toArray' )
            ->will   ( $this->returnValue( array( 'user' => $userFields ) ) )
        ;
        $mockEntity
            ->expects( $this->once() )
            ->method ( 'offsetGet' )
            ->with   ( 'id' )
            ->will   ( $this->returnValue( 123 ) )
        ;
        $this->apiService
            ->expects( $this->once() )
            ->method ( 'updateUser' )
            ->with   ( 123, array( 'user' => $userFields ) )
            ->will   ( $this->returnValue( $response ) )
        ;
        $mockEntity
            ->expects( $this->once() )
            ->method ( 'setFields' )
            ->with   ( $savedUserArray )
        ;
        $reflUpdate = new ReflectionMethod( 'Malwarebytes\ZendeskBundle\DataModel\User\Repository', '_update' );
        $reflUpdate->setAccessible( true );
        $this->assertEquals(
                $mockEntity,
                $reflUpdate->invoke( $this->repo, $mockEntity )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\User\Repository::getByDefaultSort
     */
    public function testGetByDefaultSort()
    {
        $this->_configure( array( 'getUsers' ), array( '_buildPaginatorFromResponse' ) );
        $mockPaginator = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\Paginator' )
                              ->disableOriginalConstructor()
                              ->getMock();
        $response = array( 'foo' );
        $this->apiService
            ->expects( $this->once() )
            ->method ( 'getUsers' )
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
     * @covers Malwarebytes\ZendeskBundle\DataModel\User\Repository::getById
     */
    public function testGetById()
    {
        $this->_configure( array( 'getUserById' ), array( '_buildFromResponse' ) );
        $mockEntity = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\User\Entity' )
                           ->disableOriginalConstructor()
                           ->getMock();
        $response = array( 'foo' );
        $this->apiService
            ->expects( $this->once() )
            ->method ( 'getUserById' )
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
     * @covers Malwarebytes\ZendeskBundle\DataModel\User\Repository::getForNameAndEmail
     */
    public function testGetForNameAndEmail()
    {
        $this->_configure( array( 'findUserByNameAndEmail', 'createUser' ), array( '_buildFromResponse' ) );
        $mockEntity = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\User\Entity' )
                           ->disableOriginalConstructor()
                           ->getMock();
        $name = 'Joe Blow';
        $email = 'joe@blow.com';
        $mockResponse = array( 'response' );
        $this->apiService
            ->expects( $this->once() )
            ->method ( 'findUserByNameAndEmail' )
            ->with   ( $name, $email )
            ->will   ( $this->returnValue( array() ) )
        ;
        $this->repo
            ->expects( $this->at( 0 ) )
            ->method ( '_buildFromResponse' )
            ->with   ( array() )
            ->will   ( $this->returnValue( array() ) )
        ;
        $this->apiService
            ->expects( $this->once() )
            ->method ( 'createUser' )
            ->with   ( array( 'user' => array( 'name' => $name, 'email' => $email ) ) )
            ->will   ( $this->returnValue( $mockResponse ) )
        ;
        $this->repo
            ->expects( $this->at( 1 ) )
            ->method ( '_buildFromResponse' )
            ->with   ( $mockResponse )
            ->will   ( $this->returnValue( array( $mockEntity ) ) )
        ;
        $this->assertEquals(
                $mockEntity,
                $this->repo->getForNameAndEmail( $name, $email, true )
        );
    }
}