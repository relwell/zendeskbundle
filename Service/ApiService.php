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
	
	public function __construct( $apiKey, $user, $subDomain )
	{
		$this->api = new zendesk( $apiKey, $user, $subDomain );
	}
}