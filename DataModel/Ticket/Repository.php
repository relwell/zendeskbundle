<?php
/**
 * Class definition for \Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository
 */
namespace Malwarebytes\ZendeskBundle\DataModel\Ticket;
use Malwarebytes\ZendeskBundle\DataModel\AbstractEntity;
use Malwarebytes\ZendeskBundle\DataModel\AbstractRepository;
use Malwarebytes\ZendeskBundle\Service\ApiService;
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
        if (! empty( $response['ticket'] ) ) {
            $entities[] = new Entity( $response['ticket'] );
        } else if (! empty( $response['tickets'] ) ) {
            foreach ( $response['tickets'] as $ticket )
            {
                $entities[] = new Entity( $ticket );
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
        if ( $response['ticket'] ) {
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
}