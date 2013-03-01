<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\Tests\DataModel\AbstractEntityTest
 */
namespace Malwarebytes\ZendeskBundle\Tests\DataModel;
use \ReflectionProperty;
use \ReflectionMethod;

class AbstractEntityTest extends \PHPUnit_Framework_TestCase
{
    protected $entity;
    
    public function setUp()
    {
        parent::setUp();
        $this->entity = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\AbstractEntity' )
                             ->disableOriginalConstructor();
        
        $this->repo = $this->getMockbuilder( '\Malwarebytes\ZendeskBundle\DataModel\AbstractRepository' )
                           ->disableOriginalConstructor()
                           ->getMockForAbstractClass();
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::__construct
     */
    public function testConstruct()
    {
        $fields = array( 'foo' => 'bar' );
        $mock = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\DataModel\AbstractEntity' )
                     ->setConstructorArgs( array( $this->repo, $fields ) )
                     ->getMockForAbstractClass();
        
        $fieldsRefl = new ReflectionProperty( '\Malwarebytes\ZendeskBundle\DataModel\AbstractEntity', '_fields' );
        $fieldsRefl->setAccessible( true );
        $this->assertEquals(
                $fields,
                $fieldsRefl->getValue( $mock ),
                'Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::__construct() should set fields passed during instantiation.'
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::setFields
     */
    public function testSetFieldsWithoutIntegrityCheck()
    {
        $entity = $this->entity->getMockForAbstractClass();
        $fieldsRefl = new ReflectionProperty( '\Malwarebytes\ZendeskBundle\DataModel\AbstractEntity', '_fields' );
        $fieldsRefl->setAccessible( true );
        $fields = array( 'foo' => 'bar' );
        $this->assertEquals(
                $entity,
                $entity->setFields( $fields, false ),
                'Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::setFields should provide a fluent interface'
        );
        $this->assertEquals(
                $fields,
                $fieldsRefl->getValue( $entity ),
                'Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::setFields should overwrite the _fields property in the absence of an integrity check'
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::setFields
     */
    public function testSetFieldsWithIntegrityCheck()
    {
        $entity = $this->entity->setMethods( array( 'offsetSet' ) )->getMockForAbstractClass();
        $fieldsRefl = new ReflectionProperty( '\Malwarebytes\ZendeskBundle\DataModel\AbstractEntity', '_fields' );
        $fieldsRefl->setAccessible( true );
        $fields = array( 'foo' => 'bar' );
        $entity
            ->expects( $this->once() )
            ->method ( 'offsetSet' )
            ->with   ( 'foo', 'bar' )
        ;
        $this->assertEquals(
                $entity,
                $entity->setFields( $fields ),
                'Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::setFields should provide a fluent interface'
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::exists
     */
    public function testExists()
    {
        $entity = $this->entity->setMethods( array( 'offsetExists', 'getPrimaryKey' ) )->getMockForAbstractClass();
        $entity
            ->expects( $this->once() )
            ->method ( 'getPrimaryKey' )
            ->will   ( $this->returnValue( 'id' ) )
        ;
        $entity
            ->expects( $this->once() )
            ->method ( 'offsetExists' )
            ->with   ( 'id' )
            ->will   ( $this->returnValue( true ) )
        ;
        $this->assertTrue(
                $entity->exists(),
                'Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::exists should return true if the primary key field is not empty'
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::__get
     */
    public function test__get()
    {
        $entity = $this->entity->setMethods( array( 'offsetGet' ) )->getMockForAbstractClass();
        $entity
            ->expects( $this->once() )
            ->method ( 'offsetGet' )
            ->with   ( 'id' )
            ->will   ( $this->returnValue( 123 ) )
        ;
        $fieldsRefl = new ReflectionProperty( '\Malwarebytes\ZendeskBundle\DataModel\AbstractEntity', '_fields' );
        $fieldsRefl->setAccessible( true );
        $fieldsRefl->setValue( $entity, array( 'id' => 123 ) );
        $this->assertEquals(
                123,
                $entity->id
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::__set
     */
    public function test__set()
    {
        $entity = $this->entity->setMethods( array( 'offsetSet' ) )->getMockForAbstractClass();
        $entity
            ->expects( $this->once() )
            ->method ( 'offsetSet' )
            ->with   ( 'id', 123 )
        ;
        $entity->id = 123;
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::offsetExists
     */
    public function testOffsetExists()
    {
        $entity = $this->entity->setMethods( array( null ) )->getMockForAbstractClass();
        $fieldsRefl = new ReflectionProperty( '\Malwarebytes\ZendeskBundle\DataModel\AbstractEntity', '_fields' );
        $fieldsRefl->setAccessible( true );
        $fieldsRefl->setValue( $entity, array( 'id' => 123 ) );
        $this->assertTrue(
                $entity->offsetExists( 'id' ),
                'Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::exists should return true if the primary key field is not empty'
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::offsetGet
     */
    public function testOffsetGet()
    {
        $entity = $this->entity->setMethods( array( 'offsetExists' ) )->getMockForAbstractClass();
        $fieldsRefl = new ReflectionProperty( '\Malwarebytes\ZendeskBundle\DataModel\AbstractEntity', '_fields' );
        $fieldsRefl->setAccessible( true );
        $fieldsRefl->setValue( $entity, array( 'id' => 123 ) );
        $entity
            ->expects( $this->once() )
            ->method ( 'offsetExists' )
            ->with   ( 'id' )
            ->will   ( $this->returnValue( true ) )
        ;
        $this->assertEquals(
                123,
                $entity->offsetGet( 'id' )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::offsetSet
     */
    public function testOffsetSet()
    {
        $entity = $this->entity->setMethods( array( null ) )->getMockForAbstractClass();
        $fieldsRefl = new ReflectionProperty( '\Malwarebytes\ZendeskBundle\DataModel\AbstractEntity', '_fields' );
        $fieldsRefl->setAccessible( true );
        $entity->offsetSet( 'id', 123 );
        $this->assertEquals(
                array( 'id' => 123 ),
                $fieldsRefl->getValue( $entity )
        );
    }
    
     /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::offsetSet
     */
    public function testOffsetSetReadonly()
    {
        $entity = $this->entity->setMethods( array( null ) )->getMockForAbstractClass();
        $fieldsRefl = new ReflectionProperty( '\Malwarebytes\ZendeskBundle\DataModel\AbstractEntity', '_fields' );
        $fieldsRefl->setAccessible( true );
        $readOnlyFieldsRefl = new ReflectionProperty( '\Malwarebytes\ZendeskBundle\DataModel\AbstractEntity', '_readOnlyFields' );
        $readOnlyFieldsRefl->setAccessible( true );
        $readOnlyFieldsRefl->setValue( $entity, array( 'foo' ) );
        $entity->offsetSet( 'foo', 123 );
        $this->assertEmpty(
                $fieldsRefl->getValue( $entity ),
                'Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::offsetSet should not assign a field a value if that field is read-only.'
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::offsetUnset
     */
    public function testOffsetUnset()
    {
        $entity = $this->entity->setMethods( array( null ) )->getMockForAbstractClass();
        $fieldsRefl = new ReflectionProperty( '\Malwarebytes\ZendeskBundle\DataModel\AbstractEntity', '_fields' );
        $fieldsRefl->setAccessible( true );
        $fieldsRefl->setValue( $entity, array( 'id' => 123 ) );
        $entity->offsetUnset( 'id' );
        $this->assertEmpty(
                $fieldsRefl->getValue( $entity )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::offsetUnset
     */
    public function testOffsetUnsetReadOnly()
    {
        $entity = $this->entity->setMethods( array( null ) )->getMockForAbstractClass();
        $fieldsRefl = new ReflectionProperty( '\Malwarebytes\ZendeskBundle\DataModel\AbstractEntity', '_fields' );
        $fieldsRefl->setAccessible( true );
        $fieldsRefl->setValue( $entity, array( 'id' => 123 ) );
        $readOnlyFieldsRefl = new ReflectionProperty( '\Malwarebytes\ZendeskBundle\DataModel\AbstractEntity', '_readOnlyFields' );
        $readOnlyFieldsRefl->setAccessible( true );
        $readOnlyFieldsRefl->setValue( $entity, array( 'id' ) );
        $entity->offsetUnset( 'id' );
        $this->assertNotEmpty(
                $fieldsRefl->getValue( $entity )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::toArray
     */
    public function testToArray()
    {
        $arr = array( 'entity' => array( 'id' => 123 ) );
        $entity = $this->entity->setMethods( array( 'getType' ) )->getMockForAbstractClass();
        $entity
            ->expects( $this->once() )
            ->method ( 'getType' )
            ->will   ( $this->returnValue( 'entity' ) )
        ;
        $fieldsRefl = new ReflectionProperty( '\Malwarebytes\ZendeskBundle\DataModel\AbstractEntity', '_fields' );
        $fieldsRefl->setAccessible( true );
        $fieldsRefl->setValue( $entity, $arr['entity'] );
        $this->assertEquals(
                $arr,
                $entity->toArray()
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::isIncomplete
     */
    public function testIsIncomplete()
    {
        $entity = $this->entity->setMethods( array( null ) )->getMockForAbstractClass();
        $fieldsRefl = new ReflectionProperty( '\Malwarebytes\ZendeskBundle\DataModel\AbstractEntity', '_fields' );
        $fieldsRefl->setAccessible( true );
        $fieldsRefl->setValue( $entity, array( 'id' => 123, 'foo' => 'bar', 'baz' => 'qux' ) );
        $mfieldsRefl = new ReflectionProperty( '\Malwarebytes\ZendeskBundle\DataModel\AbstractEntity', '_mandatoryFields' );
        $mfieldsRefl->setAccessible( true );
        $mfieldsRefl->setValue( $entity, array( 'id', 'foo', 'flerg' ) );
        $this->assertTrue(
                $entity->isIncomplete(),
                'Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::isIncomplete should return true if a mandatory field is not valuated'
        );
        $fieldsRefl->setValue( $entity, array( 'id' => 123, 'foo' => 'bar', 'baz' => 'qux', 'flerg' => 'blarg' ) );
        $this->assertFalse(
                $entity->isIncomplete(),
                'Malwarebytes\ZendeskBundle\DataModel\AbstractEntity::isIncomplete should return false if all mandatory fields are valuated'
        );
    }
}