<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\Model\AbstractModelFactory
 */
namespace Malwarebytes\ZendeskBundle\Model;
use Malwarebytes\ZendeskBundle\Service\ApiService;
/**
 * This class is responsible for generating and saving specific instances
 * @author relwell
 */
abstract class AbstractModelFactory
{
    /**
     * Interface to Zendesk API, through our service.
     * @var ApiService
     */
    protected $_apiService;
    
    public function __construct( ApiService $apiService )
    {
        $this->_apiService = $apiService;
    }
    
    /**
     * Strategy for creating or updating instance.
     * @param AbstractModel $instance
     * @return AbstractModel
     */
    public function save( AbstractModel $instance )
    {
        if ( $instance->exists() ) {
            return $this->_update( $instance );
        } else {
            return $this->_create( $instance );
        }
    }
    
    /**
     * Interacts with API to update fields in an existing instance. 
     * @param AbstractModel $instance
     * @return AbstractModel
     */
    abstract protected function _update( AbstractModel $instance );
    
    /**
     * Interacts with the API to create a new instance and fill in that instance's new fields.
     * @param AbstractModel $instance
     * @return AbstractModel
     */
    abstract protected function _create( AbstractModel $instance );
    
    /**
     * Returns a single instance given the appropriate ID.
     * This requires accessing a response from the API and then apply the appropriate fields to the given model. 
     * @param unknown_type $id
     * @return AbstractModel
     */
    abstract public function getById( $id );
    
    abstract public function get
}