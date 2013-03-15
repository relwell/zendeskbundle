<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\DataModel\Group\Repository
 */
namespace Malwarebytes\ZendeskBundle\DataModel\Group;
use Malwarebytes\ZendeskBundle\DataModel\AbstractRepository, Malwarebytes\ZendeskBundle\DataModel\AbstractEntity;
/**
 * Repository for retrieving and changing group data
 * @author relwell
 */
class Repository extends AbstractRepository
{
    /**
     * Instantiates entities from the provided response.
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::_buildFromResponse()
     * @param array $response
     * @return array
     */
    protected function _buildFromResponse( array $response )
    {
        $this->_currentResponse = $response;
        $groups = array();
        $this->_validateResponse( $response );
        if (! empty( $response['group'] ) ) {
            $groups[] = new Entity( $this, $response['group'] );
        } else if (! empty( $response['groups'] ) ) {
            foreach ( $response['groups'] as $group )
            {
                $groups[] = new Entity( $this, $group );
            }
        } else if (! empty( $response['results'] ) ) {
            foreach ( $response['results'] as $result )
            {
                $groups[] = new Entity( $this, $result );
            }
        }
        return $groups;
    }
    
    /**
     * Validates and then creates a new entity.
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::_create()
     * @param Entity $instance
     * @return Entity
     */
    protected function _create( AbstractEntity $instance )
    {
        $response = $this->_apiService->createGroup( $instance->toArray(), true );
        if ( $response['group'] ) {
            $instance->setFields( $response['group'] );
        }
        return $instance;
    }
    
    /**
     * Makes the appropriate API call with the instance data
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::_update()
     * @param Entity $instance
     * @return $instance
     */
    protected function _update( AbstractEntity $instance )
    {
        $response = $this->_apiService->updateGroup( $instance['id'], $instance->toArray() );
        if ( $response['group'] ) {
            $instance->setFields( $response['group'] );
        }
        return $instance;
    }
    
    /**
     * Returns a group by its id.
     * (non-PHPdoc)
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::getById()
     * @param $id
     * @return Entity
     */
    public function getById( $id ) 
    {
        $entities = $this->_buildFromResponse( $this->_apiService->getGroupById( $id ) );
        return array_shift( $entities );
    }
    
    /**
     * Retrieves all groups
     * (non-PHPdoc)
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::getByDefaultSort()
     * @return Paginator
     */
    public function getByDefaultSort()
    {
        return $this->_buildPaginatorFromResponse( $this->_apiService->getGroups() );
    }
}