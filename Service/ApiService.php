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
     * Creates API wrapper 
     * @param string $apiKey
     * @param string $user
     * @param string $subDomain
     */
    public function __construct( $apiKey, $user, $subDomain )
    {
        $this->api = new zendesk( $apiKey, $user, $subDomain );
    }
    
    /**
     * Sends a create request to API's user service
     * Returns a json-decoded array from the response
     * @param string $name
     * @param string $email
     * @return mixed return value of \zendesk::call
     */
    public function createSubmitter( $name, $email )
    {
        $jsonArray = array(
                'name' => $name,
                'email' => $email
                );
        $this->api->call( 'users' , json_encode( $jsonArray ), 'POST' );
    } 
}