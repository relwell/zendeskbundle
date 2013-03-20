<?php
/**
 * Class definition for Zendesk Service
 */
namespace Malwarebytes\ZendeskBundle\Service;
use Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity as Ticket;
use Malwarebytes\ZendeskBundle\DataModel\User\Entity as User;
use Malwarebytes\ZendeskBundle\DataModel\Group\Entity as Group;
/**
 * This class provides an API for some basic logic interacting with the Zendesk API through our Data Model
 * @author relwell
 */
class ZendeskService
{
    /**
     * Allows us to access any repository
     * @var RepositoryService
     */
    protected $_repos;
    
    /**
     * Memoization cache for user entities.
     * @var array
     */
    protected $_users = array();
    
    /**
     * Memoization cache for groups
     * @var array
     */
    protected $_groups = array();
    
    /**
     * Memoziation cache for ticket entities
     * @var array
     */
    protected $_tickets = array();
    
    public function __construct( RepositoryService $repoService )
    {
        $this->_repos = $repoService;
    }
    
    /**
     * Returns an array of tickets, with comments populated, for a user ID.
     * @param int $userId
     * @throws \Exception
     * @return array
     */
    public function getTicketsWithCommentsForUserId( $userId )
    {
        $user = $this->getUserById( $userId );
        $ticketRepo = $this->_repos->get( 'Ticket' );
        $auditRepo = $this->_repos->get( 'Audit' );
        
        $tickets = $ticketRepo->getTicketsRequestedByUser( $user );
        $ticketsRendered = array();
        foreach ( $tickets as $ticket ) {
            $ticket['comments'] = $auditRepo->getCommentsForTicket( $ticket );
            $ticketsRendered[] = $this->render( 'ZendeskBundle:Default:ticket.html.twig', array( 'ticket' => $ticket ) );
        }
        $tickets->rewind();
        return $tickets;
    }
    
    /**
     * Creates a ticket requested by the provided user
     * @param int $userId
     * @param string $subject
     * @param string $comment
     */
    public function createTicketAsUser( $userId, $subject, $comment )
    {
        $user = $this->getUserById( $userId ); // validates our user
        $ticketRepo = $this->_repos->get( 'Ticket' );
        $ticket = new Ticket( $ticketRepo );
        $ticket['requester_id'] = $user['id'];
        $ticket['subject'] = $subject;
        $ticket['comment'] = array( 'body' => $comment );
        $ticketRepo->save( $ticket );
        return $this;
    }
    
    /**
     * Provided ticket ID, name, and email, adds user as collaborator to ticket.
     * @param int $ticketId
     * @param string $userName
     * @param string $userEmail
     * @return \Malwarebytes\ZendeskBundle\Service\ZendeskService
     */
    public function addCollaboratorToTicket( $ticketId, $userName, $userEmail )
    {
        $ticket = $this->getTicketById( $ticketId );
        $user = $this->_repos->get( 'Users' )->getForNameAndEmail( $userName, $userEmail );
        if ( $user ) {
            $ticket->addCollaborator( $user );
        }
        return $this;
    }
    
    /**
     * Validates ticket and group ids, and then changes the group for the ticket.
     * @param int $ticketId
     * @param int $groupId
     * @return \Malwarebytes\ZendeskBundle\Service\ZendeskService
     */
    public function changeTicketGroup( $ticketId, $groupId )
    {
        $ticket = $this->getTicketById( $ticketId );
        $group = $this->getGroupById( $groupId );
        $ticket['group_id'] = $group['id'];
        $this->_repos->get( 'Tickets' )->save( $ticket );
        return $this;
    }
    
    /**
     * Provided an ID and a comment, adds a comment to a ticket and returns the entity.
     * @param int $ticketId
     * @param string $comment
     * @param bool $public
     * @return \Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity
     */
    public function addCommentToTicket( $ticketId, $comment, $public = false )
    {
        $ticket = $this->getTicketById( $ticketId );
        $ticket->addComment( $comment, $public );
        return $ticket;
    }
    
    /**
     * Returns a ticket given an ID
     * @param int $ticketId
     * @throws \Exception
     * @return Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity
     */
    public function getTicketById( $ticketId )
    {
        if ( empty( $this->_tickets[$ticketId] ) ) {
            $ticket = $this->_repos->get( 'Ticket' )->getById( $ticketId );
            if ( empty( $ticket ) ) {
                throw new \Exception( "No ticket with ID {$ticketId}" );
            }
            $this->_tickets[$ticketId] = $ticket;
        }
        return $this->_tickets[$ticketId];
    }
    
    /**
     * Returns a group given an ID
     * @param int $groupId
     * @throws \Exception
     * @return Malwarebytes\ZendeskBundle\DataModel\Group\Entity
     */
    public function getGroupById( $groupId )
    {
        if ( empty( $this->_groups[$groupId] ) ) {
            $group = $this->_repos->get( 'Group' )->getById( $groupId );
            if ( empty( $group ) ) {
                throw new \Exception( "No group with ID {$groupId}" );
            }
            $this->_groups[$groupId] = $group;
        }
        return $this->_groups[$groupId];
    }
}