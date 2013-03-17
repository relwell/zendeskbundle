<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\Test\DataModel\Paginator
 */
namespace Malwarebytes\ZendeskBundle\Test\DataModel;
use Malwarebytes\ZendeskBundle\DataModel;
/**
 * Tests Malwarebytes\ZendeskBundle\DataModel\Paginator
 * @author relwell
 */
class PaginatorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->_repo = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository' )
                            ->disableOriginalConstructor()
                            ->setMethods( array( 'updatePaginator' ) )
                            ->getMock();
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Paginator::__construct
     */
    public function test__construct()
    {
        $paginator = new DataModel\Paginator( $this->_repo );
        $this->assertAttributeEmpty(
                '_iterator',
                $paginator
        );
        $this->assertAttributeEmpty(
                '_nextPage',
                $paginator
        );
        $paginator = new DataModel\Paginator( $this->_repo, array( 'foo' ), 2 );
        $this->assertAttributeNotEmpty(
                '_iterator',
                $paginator
        );
        $this->assertAttributeNotEmpty(
                '_nextPage',
                $paginator
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Paginator::setEntities
     */
    public function testSetEntities()
    {
        $paginator = new DataModel\Paginator( $this->_repo );
        $this->assertAttributeEmpty(
                '_iterator',
                $paginator
        );
        $this->assertEquals(
                $paginator,
                $paginator->setEntities( array( 'fake entity' ) )
        );
        $this->assertAttributeInstanceOf(
                '\ArrayIterator',
                '_iterator',
                $paginator
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Paginator::setNextPage
     * @covers Malwarebytes\ZendeskBundle\DataModel\Paginator::getNextPage
     */
    public function testSetGetNextPage()
    {
        $paginator = new DataModel\Paginator( $this->_repo );
        $this->assertAttributeEmpty(
                '_nextPage',
                $paginator
        );
        $this->assertEquals(
                $paginator,
                $paginator->setNextPage( 2 )
        );
        $this->assertAttributeEquals(
                2,
                '_nextPage',
                $paginator
        );
        $this->assertEquals(
                2,
                $paginator->getNextPage()
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Paginator::valid
     */
    public function testValid()
    {
        $mockRepo = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository' )
                         ->disableOriginalConstructor()
                         ->setMethods( array( 'updatePaginator' ) )
                         ->getMock();
        $paginator = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Paginator' )
                          ->setConstructorArgs( array( $mockRepo ) )
                          ->setMethods( null )
                          ->getMock();
        $mockIterator = $this->getMock( '\ArrayIterator', array( 'valid' ) );
        $it = new \ReflectionProperty( 'Malwarebytes\ZendeskBundle\DataModel\Paginator', '_iterator' );
        $it->setAccessible( true );
        $it->setValue( $paginator, $mockIterator );
        $mockIterator
            ->expects( $this->at( 0 ) )
            ->method ( 'valid' )
            ->will   ( $this->returnValue( false ) )
        ;
        $mockIterator
            ->expects( $this->at( 1 ) )
            ->method ( 'valid' )
            ->will   ( $this->returnValue( true ) )
        ;
        $this->assertTrue(
                $paginator->valid()
        );
        $paginator->setNextPage( 2 );
        $mockIterator
            ->expects( $this->at( 0 ) )
            ->method ( 'valid' )
            ->will   ( $this->returnValue( false ) )
        ;
        $mockRepo
            ->expects( $this->once() )
            ->method ( 'updatePaginator' )
            ->with   ( $paginator )
        ;
        $mockIterator
            ->expects( $this->at( 1 ) )
            ->method ( 'valid' )
            ->will   ( $this->returnValue( true ) )
        ;
        $this->assertTrue(
                $paginator->valid()
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\Paginator::current
     * @covers Malwarebytes\ZendeskBundle\DataModel\Paginator::key
     * @covers Malwarebytes\ZendeskBundle\DataModel\Paginator::next
     * @covers Malwarebytes\ZendeskBundle\DataModel\Paginator::rewind
     */
    public function testIteratorMethods()
    {
        $methods = array( 'current', 'key', 'next', 'rewind' );
        $mockRepo = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository' )
                         ->disableOriginalConstructor()
                         ->setMethods( array( 'updatePaginator' ) )
                         ->getMock();
        $paginator = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Paginator' )
                          ->setConstructorArgs( array( $mockRepo ) )
                          ->setMethods( null )
                          ->getMock();
        $mockIterator = $this->getMock( '\ArrayIterator', $methods );
        $it = new \ReflectionProperty( 'Malwarebytes\ZendeskBundle\DataModel\Paginator', '_iterator' );
        $it->setAccessible( true );
        foreach ( $methods as $method ) {
            $mockIterator
                ->expects( $this->once() )
                ->method ( $method )
                ->will   ( $this->returnValue( true ) )
            ;
        }
        $it->setValue( $paginator, $mockIterator );
        foreach ( $methods as $method ) {
            $this->assertTrue( $paginator->{$method}() );
        }
    }
}