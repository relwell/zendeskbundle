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
    
}