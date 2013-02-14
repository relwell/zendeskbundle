<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\Model\ModelAbstract
 */
namespace Malwarebytes\ZendeskBundle\Model;
use Malwarebytes\ZendeskBundle\Service\ApiService;

/**
 * Provides a common API for handling models.
 * @author relwell
 *
 */
abstract class AbstractModel implements \ArrayAccess
{
    /**
     * Associative array mapping fields to value.
     * @var array
     */
    protected $_fields = array();
    
    /**
     * Create an instance using the provided fields.
     * @param array $fields
     */
    public function __construct( array $fields = array() )
    {
        $this->_fields = $fields;
    }
    
    /**
     * Allows us to determine the field that uniquely identifies the instance.
     */
    abstract public function getPrimaryKey();
    
    /**
     * Helps us determine whether this exists in the API.
     */
    public function exists()
    {
        return !empty( $this->_data[ $this->getPrimaryKey() ] ); 
    }
    
    /**
     * Allows us to access fields as attributes.
     * @param string $name
     * @return mixed
     */
    public function __get( $name )
    {
        return $this->{$name} ?: $this->offsetGet( $name );
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
     * (non-PHPdoc)
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists( $offset )
    {
        return !empty( $this->_fields[$offset] );
    }

    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet( $offset )
    {
        return !empty( $this->_fields[$offset] ) ? $this->_fields[$offset] : null;
    }

    /** 
     * (non-PHPdoc)
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet( $offset, $value )
    {
        $this->_fields[$offset] = $value;
    }

    /** 
     * (non-PHPdoc)
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset( $offset )
    {
        unset( $this->_fields[$offset] );
    }
}