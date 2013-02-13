<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\Service\ApiService
 */
namespace Malwarebytes\ZendeskBundle\Service;
use \zendesk;
/**
 * Wraps the <a href="https://github.com/ludwigzzz/Zendesk-API">Zendesk API</a> cURL library 
 * with logic, and also accommodates dependency injection with Symfony2
 * @author Robert Elwell
 */
class ApiService
{
    /**
     * Interface to core API request wrapper
     * @var \zendesk
     */
    protected $api;
    
    /**
     * Keeps track of subdomain, since zendesk API doesn't
     * @var string
     */
    protected $subDomain;
    
    /**
     * Api key for zendesk 
     * @var string
     */
    protected $apiKey;
    
    /**
     * User ID interacting with zendesk
     * @var string
     */
    protected $user;
    
    /**
     * Creates API wrapper
     * @param string $apiKey
     * @param string $user
     * @param string $subDomain
     */
    public function __construct( $apiKey, $user, $subDomain )
    {
        $this->setZendeskApi( new zendesk( $apiKey, $user, $subDomain ) );
    }
    
    /**
     * Registers a newly configured zendesk api wrapper
     * @param \zendesk $zendesk
     */
    public function setZendeskApi( \zendesk $zendesk )
    {
        $this->api       = $zendesk;
        $this->apiKey    = $zendesk->api_key;
        $this->user      = $zendesk->user;
        $this->subDomain = preg_replace( '/https:\/\/([^\.]+)\.*/', '$1', $zendesk->base );
        return $this;
    }
    
    /**
     * Sends a create request to API's user service
     * Returns a json-decoded array from the response
     * @param string $name
     * @param string $email
     * @param bool $verified
     * @return array return value of \zendesk::call
     */
    public function createUser( $name, $email, $verified = true )
    {
        $jsonArray = array(
                'name' => $name,
                'email' => $email,
                'verified' => $verified
                );
        return $this->api->call( '/users' , json_encode( $jsonArray ), 'POST' );
    }
    
    /**
     * Determines whether a given user ID has any tickets
     * @param string $userId
     * @return boolean
     */
    public function userHasRequestedTickets( $userId )
    {
        $response = $this->getTicketsRequestedByUser( $userId );
        return !empty( $response['tickets'] );
    }
    
    /**
     * Returns an array of tickets the user requested
     * @param string $userId
     * @return array
     */
    public function getTicketsRequestedByUser( $userId )
    {
        return $this->_get( "/users/{$userId}/tickets/requested" );
    }
    
    /**
     * Returns all tickets CCed to the given user ID
     * @param string  $userId
     * @return array
     */
    public function getTicketsCCedToUser( $userId )
    {
        return $this->_get( "/users/{$userId}/tickets/ccd" );
    }
    
    /**
     * Determines whether a user has any tickets CC'ed to them.
     * @param string $userId
     * @return boolean
     */
    public function userHasCCs( $userId )
    {
        $response = $this->getTicketsCCedToUser( $userId );
        return !empty( $response['tickets'] );
    }
    
    /**
     * Creates a ticket as your current user.
     * @param array $ticketData associative array of fields to values
     * @return array
     */
    public function createTicket( array $ticketData )
    {
        //@todo add validation -- ticket needs requester id
        return $this->api->call( '/tickets', json_encode( $ticketData ), 'POST' );
    }
    
    /**
     * Provides a common interface for updating data in a ticket.
     * @param int $ticketId
     * @param array $data -- just the fields for that particular ticket
     */
    public function updateTicket( $ticketId, array $data )
    {
        return $this->api->call( "/tickets/{$ticketId}", json_encode( array( 'ticket' => $data ) ), 'PUT' );
    }

    /**
     * Adds a comment to the provided ticket ID.
     * @param int $ticketId
     * @param string $comment
     * @param bool $public
     * @return array
     */
    public function addCommentToTicket( $ticketId, $comment, $public = true )
    {
        return $this->updateTicket(
                $ticketId,
                array(
                        'comment' => array(
                                'public' => $public,
                                'body'   => $comment
                                )
                        )
                );
    }
    
    /**
     * Updates a ticket with the assignee user ID.
     * @param int $ticketId
     * @param int $userId
     */
    public function assignTicketToUser( $ticketId, $userId )
    {
        return $this->updateTicket(
                $ticketId,
                array( 'assignee_id' => $userId )
                );
    }
    
    /**
     * Updates a ticket with the provided group ID.
     * @param int $ticketId
     * @param int $groupId
     */
    public function assignTicketToGroup( $ticketId, $groupId )
    {
        return $this->updateTicket(
                $ticketId,
                array( 'group_id' => $groupId )
                );
    }
    
    /**
     * Allows us to search for a user name or ID.
     * @param string $user
     * @return array
     */
    public function getTicketsAssignedToUser( $user )
    {
        return $this->_search( sprintf( 'type:ticket assignee:%s', $user ) );
    }
    
    /**
     * Searches for tickets belonging to a particular group.
     * @param string $group
     * @return array
     */
    public function getTicketsForGroup( $group )
    {
        return $this->_search( sprintf( 'type:ticket group:%s', $group ) );
    }
    
    /**
     * Provides a common interface for searching via API.
     * @param string $queryString
     * @return array
     */
    protected function _search( $queryString )
    {
        $path = "/search?" . http_build_query( array( 'query' => $queryString ) );
        return $this->_get( $path );
    }
    
    /**
     * Wraps a very, very stupid API without default param values
     * @param string $path
     * @return array
     */
    protected function _get( $path )
    {
        return $this->api->call( $path, '', 'GET' );
    }
}