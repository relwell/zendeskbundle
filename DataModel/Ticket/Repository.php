<?php
/**
 * Class definition for \Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository
 */
namespace Malwarebytes\ZendeskBundle\DataModel\Ticket;
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
        $entities = array();
        if (! empty( $response['ticket'] ) ) {
            $entities[] = new Entity( $response['ticket'] );
        } else if (! empty( $response['tickets'] ) ) {
            foreach ( $response['tickets'] as $ticket )
            {
                $entites[] = new Entity( $ticket );
            }
        }
        return $entities;
    }
    
    protected function _create( AbstractEntity $instance )
    {
        
    }
    
    protected function _update( AbstractEntity $instance )
    {
        
    }
    
    /**
     * Return tickets sorted by creation date ascending.
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::getByDefaultSort()
     * @return Paginator
     */
    public function getByDefaultSort()
    {
        return $this->_buildFromResponse( $this->apiService->getTickets() );
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