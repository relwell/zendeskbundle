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
     * @return mixed return value of \zendesk::call
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
     * Creates a ticket as your current user
     * @param array $ticketData associative array of fields to values
     * @return array
     */
    public function createTicket( array $ticketData )
    {
        return $this->api->call( '/tickets', json_encode( $ticketData ), 'POST' );
    }
    
    /**
     * Wraps a very, very stupid API without default param values
     * @param string $path
     * @return mixed
     */
    protected function _get( $path )
    {
        return $this->api->call( $path, '', 'GET' );
    }
}