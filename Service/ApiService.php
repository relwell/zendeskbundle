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
    protected $_api;
    
    /**
     * Keeps track of subdomain, since zendesk API doesn't
     * @var string
     */
    protected $_subDomain;
    
    /**
     * Api key for zendesk 
     * @var string
     */
    protected $_apiKey;
    
    /**
     * User ID interacting with zendesk
     * @var string
     */
    protected $_user;
    
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
        $this->_api       = $zendesk;
        $this->_apiKey    = $zendesk->api_key;
        $this->_user      = $zendesk->user;
        $this->_subDomain = preg_replace( '/https:\/\/([^\.]+)\.*/', '$1', $zendesk->base );
        return $this;
    }
    
    /**
     * Sends a create request to API's user service
     * Returns a json-decoded array from the response
     * @param array $data
     * @param bool $verified
     * @return array return value of \zendesk::call
     */
    public function createUser( array $data, $verified = true )
    {
        if ( empty( $data['user']['name'] ) || empty( $data['user']['email'] ) ) {
            throw new \Exception( 'Users need a name or an email to be created' );
        }
        $data['user']['verified'] = $verified;
        return $this->_api->call( '/users' , json_encode( $data ), 'POST' );
    }
    
    /**
     * Updates a user provided an array of user data.
     * @param array $data
     * @return array
     */
    public function updateUser( $userId, array $data ) {
        return $this->_api->call( "/users/{$userId}", json_encode( $data ), 'PUT' );
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
     * Returns tickets in a default sort (oldest to newest).
     * @return array
     */
    public function getTickets()
    {
        return $this->_get( "/tickets" );
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
        return $this->_api->call( '/tickets', json_encode( $ticketData ), 'POST' );
    }
    
    /**
     * Returns information about the current user in an array format.
     * @return array
     */
    public function getCurrentUser()
    {
        return $this->_get( "/users/me" );
    }
    
    /**
     * Asks Zendesk for a user matching the provided credentials.
     * @param string $name
     * @param string $email
     * @return array
     */
    public function findUserByNameAndEmail( $name, $email )
    {
        return $this->_search( sprintf( 'type:user name:"%s" email:%s', $name, $email ) );
    }
    
    /**
     * This one is more forgiving.
     * @param string $name
     * @param string $email
     * @return array
     */
    public function findUserByEmail( $email )
    {
        return $this->_search( sprintf( 'type:user email:%s', $email ) );
    }
    
    /**
     * Returns an array representing ticket fields
     * @param int $ticketId
     * @return array
     */
    public function getTicket( $ticketId )
    {
        return $this->_get( "/tickets/{$ticketId}" );
    }
    
    /**
     * Provides a common interface for updating data in a ticket.
     * @param int $ticketId
     * @param array $data -- just the fields for that particular ticket
     */
    public function updateTicket( $ticketId, array $data )
    {
        return $this->_api->call( "/tickets/{$ticketId}", json_encode( $data ), 'PUT' );
    }
    
    /**
     * Sets the collaborators on a provided ticket. This overwrites the collaborator IDs for that ticket.
     * @param int $ticketId
     * @param array $collaboratorIds
     */
    public function setTicketCollaborators( $ticketId, array $collaboratorIds )
    {
        return $this->updateTicket( $ticketId, array( 'collaborator_ids' => $collaboratorIds ) );
    }
    
    /**
     * Allows us to push a collaborator ID onto the existing list of collaborator IDs. 
     * @param int $ticketId
     * @param int $collaboratorId
     */
    public function addCollaboratorToTicket( $ticketId, $collaboratorId )
    {
        $ticketResponse = $this->getTicket( $ticketId );
        $ticket = $ticketResponse['ticket'];
        $collaboratorIds = empty( $ticket['collaborator_ids'] ) 
                         ? array( $collaboratorId ) 
                         : array_merge( $ticket['collaborator_ids'], array( $collaboratorId ) );
        return $this->setTicketCollaborators( $ticketId, array_unique( $collaboratorIds ) );
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
                array( 'ticket' => array(
                        'comment' => array(
                                'public' => $public,
                                'body'   => $comment
                                )
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
     * Allows you to select tickets that haven't been updated since a certain time.
     * So for instance, if you want to get tickets that have not been touched for a day, 
     * you would want to pass something like time() - 86400.
     * @param int $time 
     */
    public function getTicketsUntouchedSinceTime( $time )
    {
        $timestamp = gmdate( 'Y-m-d\TH:i:s\Z', $time );
        return $this->_search( sprintf( 'type:ticket updated_at<%s', $timestamp ) );
    }
    
    /**
     * Returns a response for the default users service.
     * @return array
     */
    public function getUsers()
    {
        return $this->_get( "/users" );
    }
    
    /**
     * Returns a response array for a particular user ID.
     * @param int $id
     * @return array
     */
    public function getUserById( $id )
    {
        return $this->_get( "/users/$id" );
    }
    
    /**
     * Function-agnostic way of returning a pagination URL.
     * @param string $pageUrl
     * @return array
     */
    public function getNextPage( $pageUrl )
    {
        $parsed = \parse_url( $pageUrl );
        return $this->_get( $parsed['path'] . '?' . $parsed['query'] );
    }
    
    /**
     * Returns all audits for a ticket.
     * @param int $ticketId
     * @return array
     */
    public function getAuditsForTicket( $ticketId )
    {
        return $this->_get( "/tickets/{$ticketId}/audits" );
    }
    
    /**
     * Grabs a specific audit for a ticket.
     * @param int $ticketId
     * @param int $auditId
     * @return array
     */
    public function getAudit( $ticketId, $auditId )
    {
        return $this->_get( "/tickets/{$ticketId}/audits/{$auditId}" );
    }
    
    /**
     * Provides a common interface for searching via API.
     * @param string $queryString
     * @return array
     */
    protected function _search( $queryString )
    {
        // we have to add json here because the lib we're using sucks apparently
        $path = "/search.json?" . http_build_query( array( 'query' => $queryString ) );
        return $this->_get( $path );
    }
    
    /**
     * Wraps a very, very stupid API without default param values
     * @param string $path
     * @return array
     */
    protected function _get( $path )
    {
        return $this->_api->call( $path, '', 'GET' );
    }
}