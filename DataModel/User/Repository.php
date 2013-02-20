<?php
/**
 * Class definition for \Malwarebytes\ZendeskBundle\DataModel\User\Repository
 */
namespace Malwarebytes\ZendeskBundle\DataModel\User;
use Malwarebytes\ZendeskBundle\DataModel\AbstractRepository;
/**
 * Repository for users.
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
        $users = array();
        if (! empty( $response['user'] ) ) {
            $users[] = new Entity( $response['user'] );
        } else if (! empty( $response['users'] ) ) {
            foreach ( $response['users'] as $user )
            {
                $users[] = new Entity( $user );
            }
        }
        return $users;
    }
    
    /**
     * Validates and then creates a new entity.
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::_create()
     * @param Entity $instance
     * @return Entity
     */
    protected function _create( AbstractEntity $instance )
    {
        if ( empty( $instance['name'] ) || empty( $instance['email'] ) ) {
            throw new \Exception( 'Users need a name or an email to be created' );
        }
        $response = $this->_apiService->createUser( $instance['name'], $instance['email'], true );
        $instance->setFields( $response['user'] );
        return $instance;
    }
    
    protected function _update( AbstractEntity $instance )
    {
        $response = $this->_apiService->updateUser( $instance->toArray() );
        $instance->setFields( $response['user'] );
        return $instance;
    }
    
    public function getByDefaultSort()
    {
        return $this->_buildPaginatorFromResponse( $this->_apiService->getUsers() );
    }
    
    public function getById( $id ) 
    {
        return $this->_buildFromResponse( $this->_apiService->getUserById( $id ) );
    }

}