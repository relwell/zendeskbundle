<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\DataModel\AbstractRepository
 */
namespace Malwarebytes\ZendeskBundle\DataModel;
use Malwarebytes\ZendeskBundle\Service\ApiService;
use Malwarebytes\ZendeskBundle\DataModel\Paginator;
/**
 * This class is responsible for generating and saving specific instances of an entity within its provided namespace.
 * @author relwell
 */
abstract class AbstractRepository
{
    /**
     * Interface to Zendesk API, through our service.
     * @var ApiService
     */
    protected $_apiService;
    
    /**
     * JSON-decoded response from last service call.
     * @var array
     */
    protected $_currentResponse;
    
    public function __construct( ApiService $apiService )
    {
        $this->_apiService = $apiService;
    }
    
    /**
     * Strategy for creating or updating instance.
     * @param AbstractEntity $instance
     * @return AbstractEntity
     */
    public function save( AbstractEntity $instance )
    {
        if ( $instance->exists() ) {
            if ( $instance->isIncomplete() ) {
                throw new \Exception( 'Violated integrity check for completeness on existing instance.' );
            }
            return $this->_update( $instance );
        } else {
            return $this->_create( $instance );
        }
    }
    
    /**
     * Interacts with API to update fields in an existing instance. 
     * @param AbstractEntity $instance
     * @return AbstractEntity
     */
    abstract protected function _update( AbstractEntity $instance );
    
    /**
     * Interacts with the API to create a new instance and fill in that instance's new fields.
     * @param AbstractEntity $instance
     * @return AbstractEntity
     */
    abstract protected function _create( AbstractEntity $instance );
    
    /**
     * A repository should know how to take a raw API response and create one or more entities from it.
     * @param array $response
     * @return array
     */
    abstract protected function _buildFromResponse( array $response );
    
    /**
     * Wraps _buildFromResponse and returns a paginator instead.
     * @param array $response
     * @return Paginator
     */
    protected function _buildPaginatorFromResponse( array $response )
    {
        $this->_currentResponse = $response;
        $entities = $this->_buildFromResponse( $response );
        $nextPage = empty( $this->_currentResponse['next_page'] ) ? null : $this->_currentResponse['next_page'];
        return new Paginator( $this, $entities, $nextPage );
    }
    
    /**
     * Tests for errors.
     * @param array $response
     * @throws ApiResponseException
     * @return boolean
     */
    protected function _validateResponse( array $response )
    {
        if (! empty( $response['error'] ) ) {
            throw new ApiResponseException( $response );
        }
        return true;
    }
    
    /**
     * Returns a single instance given the appropriate ID.
     * This requires accessing a response from the API and then apply the appropriate fields to the given model. 
     * @param unknown_type $id
     * @return AbstractEntity
     */
    abstract public function getById( $id );
    
    /**
     * Returns the stream of entities by the default sort provided by the API.
     * For instance, tickets are sorted by date descending.
     * This response is wrapped in a paginator.
     * @return Paginator
     */
    abstract public function getByDefaultSort();
    
    /**
     * A hook for paginators to call to ask the repo for more entities.
     * Returns whether there's more for the paginator or not.
     * @param Paginator $paginator
     * @return bool
     */
    public function updatePaginator( Paginator $paginator )
    {
        $entities = array();
        $nextPage = $paginator->getNextPage();
        if ( $nextPage !== null ) {
            $entities = $this->_buildFromResponse( $this->_apiService->getNextPage( $nextPage ) );
            $nextPage = empty( $this->_currentResponse['next_page'] ) ? null : $this->_currentResponse['next_page'];
            $paginator->setEntities( $entities )
                      ->setNextPage( $nextPage );
        }
        return !empty( $entities );
    }
    
    /**
     * Creates a new, unsaved instance of the current class.
     * @return AbstractEntity
     */
    public function factory()
    {
        $entity = '\\' . implode( '\\', array_slice( explode( '\\', get_class( $this ) ), 0, -1 ) ) . '\Entity';
        return new $entity( $this ); 
    }
}