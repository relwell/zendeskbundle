<?php
/**
 * Class definition for \Malwarebytes\ZendeskBundle\DataModel\User\Repository
 */
namespace Malwarebytes\ZendeskBundle\DataModel\User;
use Malwarebytes\ZendeskBundle\DataModel\Paginator;
use Malwarebytes\ZendeskBundle\DataModel\AbstractEntity;
use Malwarebytes\ZendeskBundle\DataModel\ApiResponseException;
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
        $this->_currentResponse = $response;
        $users = array();
        $this->_validateResponse( $response );
        if (! empty( $response['user'] ) ) {
            $users[] = new Entity( $this, $response['user'] );
        } else if (! empty( $response['users'] ) ) {
            foreach ( $response['users'] as $user )
            {
                $users[] = new Entity( $this, $user );
            }
        } else if (! empty( $response['results'] ) ) {
            foreach ( $response['results'] as $result )
            {
                $users[] = new Entity( $this, $result );
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
        $response = $this->_apiService->createUser( $instance->toArray(), true );
        if ( $response['user'] ) {
            $instance->setFields( $response['user'] );
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
        $response = $this->_apiService->updateUser( $instance['id'], $instance->toArray() );
        if ( $response['user'] ) {
            $instance->setFields( $response['user'] );
        }
        return $instance;
    }
    
    /**
     * Returns users according to the zendesk API default sort
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::getByDefaultSort()
     * @return Malwarebytes\ZendeskBundle\DataModel\Paginator
     */
    public function getByDefaultSort()
    {
        return $this->_buildPaginatorFromResponse( $this->_apiService->getUsers() );
    }
    
    /**
     * Returns the user entity based on the provided ID
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::getById()
     * @return Entity
     */
    public function getById( $id ) 
    {
        $entities = $this->_buildFromResponse( $this->_apiService->getUserById( $id ) );
        return array_shift( $entities );
    }
    
    /**
     * Tries to grab a user based on name and email. If it doesn't exist, we create one (if $create is true).
     * @var string $name
     * @var string $email
     * @var bool $create
     * @return Entity|null
     */
    public function getForNameAndEmail( $name, $email, $create = false )
    {
        $entities = $this->_buildFromResponse( $this->_apiService->findUserByNameAndEmail( $name, $email ) );
        if ( $create && empty( $entities ) ) {
            $data = array( 'name' => $name, 'email' => $email );
            $entities = $this->_buildFromResponse( $this->_apiService->createUser( array( 'user' => $data  ) ) );
        }
        return array_shift( $entities );
    }
}