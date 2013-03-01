<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\DataModel\AbstractEntity
 */
namespace Malwarebytes\ZendeskBundle\DataModel;
use Malwarebytes\ZendeskBundle\Service\ApiService;

/**
 * Provides a common API for handling specific instances within our data model.
 * @author relwell
 *
 */
abstract class AbstractEntity implements \ArrayAccess
{
    /**
     * Associative array mapping fields to value.
     * @var array
     */
    protected $_fields = array();
    
    /**
     * These are the fields that we cannot mutate.
     * @var array
     */
    protected $_readOnlyFields = array();
    
    /**
     * These fields are required for a well-formed instance.
     * @var array
     */
    protected $_mandatoryFields = array();
    
    /**
     * This allows repos to control access to entities.
     * @var AbstractRepository
     */
    protected $_repository;
    
    /**
     * Create an instance using the provided fields.
     * @param array $fields
     */
    public function __construct( AbstractRepository $repo, array $fields = array() )
    {
        $this->_repository = $repo;
        $this->setFields( $fields, false );
    }
    
    /**
     * Allows us to set a bunch of fields 
     * @param array $fields
     * @param bool $integrityCheck set to false if you want to allow overwriting read-only fields.
     * @return AbstractEntity
     */
    public function setFields( array $fields = array(), $integrityCheck = true )
    {
        if ( $integrityCheck ) {
            foreach ( $fields as $field => $value ) {
                $this->offsetSet( $field, $value );
            }
        } else {
            $this->_fields = $fields;
        }
        return $this;
    }
    
    /**
     * Allows us to determine the field that uniquely identifies the instance.
     */
    abstract public function getPrimaryKey();
    
    /**
     * Allows us to determine the string type
     */
    abstract public function getType();
    
    /**
     * Helps us determine whether this exists in the API.
     */
    public function exists()
    {
        return $this->offsetExists( $this->getPrimaryKey() ); 
    }
    
    /**
     * Determines whether an instance contains all mandatory fields.
     * @return bool
     */
    public function isIncomplete()
    {
        return \array_intersect_key( \array_flip( $this->_mandatoryFields ), $this->_fields ) != \array_flip( $this->_mandatoryFields ); 
    }
    
    /**
     * Allows us to access fields as attributes.
     * @param string $name
     * @return mixed
     */
    public function __get( $name )
    {
        return property_exists( $this, $name ) ? $this->{$name} : $this->offsetGet( $name );
    }
    
    /**
     * Allows us to set values in an OO fashion.
     * @param string $name
     * @param string $value
     */
    public function __set( $name, $value )
    {
        $this->offsetSet( $name, $value );
    }
    
    /** 
     * Whether a field has a value
     * @see ArrayAccess::offsetExists()
     * @param string $offset
     * @return bool
     */
    public function offsetExists( $offset )
    {
        return !empty( $this->_fields[$offset] );
    }

    /**
     * Returns value for a field.
     * @see ArrayAccess::offsetGet()
     * @param string $offset
     * @return mixed
     */
    public function offsetGet( $offset )
    {
        return $this->offsetExists( $offset ) ? $this->_fields[$offset] : null;
    }

    /** 
     * Sets a value for our fields.
     * @see ArrayAccess::offsetSet()
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet( $offset, $value )
    {
        if (! in_array( $offset, $this->_readOnlyFields ) ) {
            $this->_fields[$offset] = $value;
        }
    }

    /** 
     * (non-PHPdoc)
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset( $offset )
    {
        if (! in_array( $offset, $this->_readOnlyFields ) ) {
            unset( $this->_fields[$offset] );
        }
    }
    
    /**
     * Returns fields and their values.
     * @return array
     */
    public function toArray()
    {
        return array( $this->getType() => $this->_fields );
    }
}