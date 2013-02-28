<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\DataModel\Audit\Repository
 */
namespace Malwarebytes\ZendeskBundle\DataModel\Audit;
use Malwarebytes\ZendeskBundle\DataModel\AbstractRepository;
use Malwarebytes\ZendeskBundle\DataModel\AbstractEntity;
use Malwarebytes\ZendeskBundle\DataModel\ApiResponseException;
use Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity as Ticket;
/**
 * Responsible for grabbing comments, among other things.
 * @author relwell
 */
class Repository extends AbstractRepository
{
    /**
     * @var Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity
     */
    protected $_ticket;
    
    /**
     * (non-PHPdoc)
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::_buildFromResponse()
     */
    protected function _buildFromResponse( array $response )
    {
        $this->_currentResponse = $response;
        $entities = array();
        if (! empty( $response['error'] ) ) {
            throw new ApiResponseException( $response );
        }
        if (! empty( $response['audit'] ) ) {
            $entities[] = new Entity( $this, $response['audit'] );
        } else if (! empty( $response['audits'] ) ) {
            foreach ( $response['audits'] as $audit )
            {
                $entities[] = new Entity( $this, $audit );
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
     * (non-PHPdoc)
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::_create()
     */
    protected function _create( AbstractEntity $instance )
    {
        throw new \Exception( "This needs to be implemented, but we don't have a use for it yet." );
    }
    
    /**
     * (non-PHPdoc)
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::_update()
     */
    protected function _update( AbstractEntity $instance )
    {
        throw new \Exception( "This needs to be implemented, but we don't have a use for it yet." );
    }
    
    /**
     * Registers the present ticket.
     * @param Ticket $ticket
     */
    public function setTicket( Ticket $ticket )
    {
        $this->_ticket = $ticket;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::getByDefaultSort()
     */
    public function getByDefaultSort()
    {
        return $this->_apiService->getAuditsForTicket( $this->_ticket[$this->_ticket->getPrimaryKey()] );
    }
    
    /**
     * Retrieves audits for a ticket.
     * @param Ticket $ticket
     * @return array
     */
    public function getForTicket( Ticket $ticket )
    {
        $this->setTicket( $ticket );
        return $this->_apiService->getAuditsForTicket( $ticket[$ticket->getPrimaryKey()] );
    }
    
    /**
     * Retrieves comment instances for a ticket.
     * @param Ticket $ticket
     * @return array
     */
    public function getCommentsForTicket( Ticket $ticket )
    {
        $response = $this->getForTicket( $ticket );
        $comments = array();
        foreach ( $response['audits'] as $audit )
        {
            if ( empty( $audit['events'] ) ) {
                continue;
            }
            foreach ( $audit['events'] as $event )
            {
                if ( $event['type'] == 'Comment' ) {
                    $comments[] = new Comment( $event );
                }
            }
        }
        return $comments;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Malwarebytes\ZendeskBundle\DataModel\AbstractRepository::getById()
     */
    public function getById( $id, $ticket = null )
    {
        if ( $ticket !== null ) {
            $this->setTicket( $ticket );
        }
        if ( $this->_ticket === null ) {
            throw new \Exception( "Please set a ticket to operate on first" );
        }
        
    }
}