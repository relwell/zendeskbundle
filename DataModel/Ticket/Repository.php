<?php
/**
 * Class definition for \Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository
 */
namespace Malwarebytes\ZendeskBundle\DataModel\Ticket;
use Malwarebytes\ZendeskBundle\DataModel\AbstractEntity;
use Malwarebytes\ZendeskBundle\DataModel\AbstractRepository;
use Malwarebytes\ZendeskBundle\DataModel\User\Entity as User;
use Malwarebytes\ZendeskBundle\Service\ApiService;
use Malwarebytes\ZendeskBundle\DataModel\ApiResponseException;
/**
 * Responsible for accessing and managing one or more ticket entities.
 * @author relwell
 */
class Repository extends AbstractRepository
{
    /**
     * Pulls entities out of json-decoded response.
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::_buildFromResponse()
     * @param array $response
     * @return array
     */
    protected function _buildFromResponse( array $response )
    {
        $this->_currentResponse = $response;
        $entities = array();
        $this->_validateResponse( $response );
        if (! empty( $response['ticket'] ) ) {
            $entities[] = new Entity( $this, $response['ticket'] );
        } else if (! empty( $response['tickets'] ) ) {
            foreach ( $response['tickets'] as $ticket )
            {
                $entities[] = new Entity( $this, $ticket );
            }
        } else if (! empty( $response['results'] ) ) {
            foreach ( $response['results'] as $result )
            {
                $entities[] = new Entity( $this, $result );
            }
        }
        return $entities;
    }
    
    /**
     * Creates a ticket and resets the ticket entity with the returned field values.
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::_create()
     * @param AbstractEntity $instance
     * @return Entity
     */
    protected function _create( AbstractEntity $instance )
    {
        $response = $this->_apiService->createTicket( $instance->toArray() );
        if (! empty( $response['ticket'] ) ) {
            $instance->setFields( $response['ticket'], true );
        }
        return $instance;
    }
    
    /**
     * Updates a ticket and resets the ticket entity with the returned field values.
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::_create()
     * @param AbstractEntity $instance
     * @return Entity
     */
    protected function _update( AbstractEntity $instance )
    {
        $response = $this->_apiService->updateTicket( $instance['id'], $instance->toArray() );
        if ( $response['ticket'] ) {
            $instance->setFields( $response['ticket'], true );
        }
        return $instance;
    }
    
    /**
     * Immediately updates the entity.
     * @param Entity $entity
     * @param string $comment
     * @param bool $public
     * @throws \Exception
     */
    public function addCommentToTicket( Entity $entity, $comment, $public = true )
    {
        if (! $entity->exists() ) {
            throw new \Exception( "We can't add a comment to a ticket that doesn't already exist" );
        }
        $response = $this->_apiService->addCommentToTicket( $entity['id'], $comment, $public );
        if ( $response['ticket'] ) {
            $entity->setFields( $response['ticket'] );
        }
        return $entity;
    }
    
    /**
     * Return tickets sorted by creation date ascending.
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::getByDefaultSort()
     * @return Paginator
     */
    public function getByDefaultSort()
    {
        return $this->_buildPaginatorFromResponse( $this->_apiService->getTickets() );
    }
    
    /**
     * Returns a ticket entity 
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::getById()
     * @param string $id
     * @return Entity
     */
    public function getById( $id )
    {
        $entities = $this->_buildFromResponse( $this->_apiService->getTicket( $id ) );
        return array_shift( $entities ); 
    }
    
    /**
     * Returns a paginator (or empty array, if the user doesn't exist) containing all the tickets for a user.
     * @param User $user
     * @return array|\Malwarebytes\ZendeskBundle\DataModel\Paginator
     */
    public function getTicketsRequestedByUser( User $user )
    {
        if (! $user->exists() ) {
            return array();
        }
        return $this->_buildPaginatorFromResponse( $this->_apiService->getTicketsRequestedByUser( $user['id'] ) );
    }
    
    /**
     * Returns all tickets older than the provided timestamp (long int)
     * @param int $timestamp
     */
    public function getOpenTicketsOlderThan( $unixTimestamp )
    {
        return $this->_buildPaginatorFromResponse( $this->_apiService->getUnresolvedTicketsUntouchedSince( $unixTimestamp ) );
    }
}