<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\Tests\Service\ApiServiceTest
 * @author relwell
 */
namespace Malwarebytes\ZendeskBundle\Tests\Service;

require_once( __DIR__. '/../../Service/ApiService.php' );

use MalwareBytes\ZendeskBundle\Service\ApiService;
use \zendesk;
use \ReflectionProperty;
use \ReflectionMethod;
/**
 * Tests for MalwareBytes\ZendeskBundle\Service\ApiService
 * @author relwell
 */
class ApiServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockBuilder
     */
    protected $apiService;
    
    /**
     * @var \zendesk
     */
    protected $zendesk;
    
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
    
    public function setUp()
    {
        $this->apiService = $this->getMockBuilder( '\MalwareBytes\ZendeskBundle\Service\ApiService' )
                                 ->disableOriginalConstructor();
        
        $this->apiKey    = 'apiKey';
        $this->subDomain = 'subdomain';
        $this->user      = 'user';
        
        $this->zendesk = $this->getMockBuilder( '\zendesk' )
                              ->setConstructorArgs( array( $this->apiKey, $this->subDomain, $this->user ) )
                              ->setMethods( array( 'call' ) )
                              ->getMock();
    }
    
    /**
     * @covers MalwareBytes\ZendeskBundle\Service\ApiService::_get
     */
    public function test_get()
    {
        $service = $this->apiService->setMethods( null )->getMock();
        $path = 'path';
        $responseArray = array( 'foo' );
        $this->zendesk
            ->expects( $this->at( 0 ) )
            ->method ( 'call' )
            ->with   ( $path, '', 'GET' )
            ->will   ( $this->returnValue( $responseArray ) )
        ;
        $_get = new ReflectionMethod( 'Malwarebytes\ZendeskBundle\Service\ApiService', '_get' );
        $_get->setAccessible( true );
        $this->assertEquals(
                $responseArray,
                $_get->invoke( $service->setZendeskApi( $this->zendesk ), $path )
        );
    }
    
    /**
     * @covers MalwareBytes\ZendeskBundle\Service\ApiService::createUser
     */
    public function testCreateUser()
    {
        $service = $this->apiService->setMethods( null )->getMock();
        
        $name = 'Name';
        $email = 'email@foo.com';
        
        $dataArray = array(
                'name' => $name,
                'email' => $email,
                'verified' => true
                );
        
        $responseArray = array( 'mockresponse' );
        
        $this->zendesk
            ->expects( $this->at( 0 ) )
            ->method ( 'call' )
            ->with   ( '/users', json_encode( $dataArray ), 'POST' )
            ->will   ( $this->returnValue( $responseArray ) )
        ;
        $this->assertEquals(
                $responseArray,
                $service->setZendeskApi( $this->zendesk )
                        ->createUser   ( $name, $email )
        );
        $dataArray['verified'] = false;
        $this->zendesk
            ->expects( $this->at( 0 ) )
            ->method ( 'call' )
            ->with   ( '/users', json_encode( $dataArray ), 'POST' )
            ->will   ( $this->returnValue( $responseArray ) )
        ;
        $this->assertEquals(
                $responseArray,
                $service->setZendeskApi( $this->zendesk )
                        ->createUser   ( $name, $email, false )
        );
        
    }
    
    /**
     * @covers MalwareBytes\ZendeskBundle\Service\ApiService::createTicket
     */
    public function testCreateTicket()
    {
        $service = $this->apiService->setMethods( null )->getMock();
        
        $subject = 'My Printer Is On Fire';
        $comment = array(
                'body' => 'One of my idiot friends printed out that speaking printer joke so I set it on fire.'
                );
        $dataArray = array(
                'subject' => $subject,
                'comment' => $comment
                );
        $responseArray = array( 'mockresponse' );
        
        $this->zendesk
            ->expects( $this->at( 0 ) )
            ->method ( 'call' )
            ->with   ( '/tickets', json_encode( $dataArray ), 'POST' )
            ->will   ( $this->returnValue( $responseArray ) )
        ;
        $this->assertEquals(
                $responseArray,
                $service->setZendeskApi( $this->zendesk )
                        ->createTicket   ( $dataArray )
        );
    }
    
    /**
     * @covers MalwareBytes\ZendeskBundle\Service\ApiService::getTicketsRequestedByUser
     */
    public function testGetTicketsRequestedByUser()
    {
        $service = $this->apiService->setMethods( array( '_get' ) )->getMock();
        $userId = 'userid';
        $response = array( 'mock' );
        $service
            ->expects( $this->at( 0 ) )
            ->method ( '_get' )
            ->with   ( "/users/{$userId}/tickets/requested" )
            ->will   ( $this->returnValue( $response ) )
        ;
        $this->assertEquals(
                $response,
                $service->getTicketsRequestedByUser( $userId )
        );
    }
    
    /**
     * @covers MalwareBytes\ZendeskBundle\Service\ApiService::userHasRequestedTickets 
     */
    public function testUserHasRequestedTicketsNoTickets()
    {
        $service = $this->apiService->setMethods( array( 'getTicketsRequestedByUser' ) )->getMock();
        $userId = 'userid';
        $service
            ->expects( $this->at( 0 ) )
            ->method ( 'getTicketsRequestedByUser' )
            ->with   ( $userId )
            ->will   ( $this->returnValue( array() ) )
        ;
        $this->assertFalse(
                $service->userHasRequestedTickets( $userId )
        );
    }
    
    /**
     * @covers MalwareBytes\ZendeskBundle\Service\ApiService::userHasRequestedTickets 
     */
    public function testUserHasRequestedTicketsWithTickets()
    {
        $service = $this->apiService->setMethods( array( 'getTicketsRequestedByUser' ) )->getMock();
        $userId = 'userid';
        $service
            ->expects( $this->at( 0 ) )
            ->method ( 'getTicketsRequestedByUser' )
            ->with   ( $userId )
            ->will   ( $this->returnValue( array( 'tickets' => array( array( 'here is one' ) ) ) ) )
        ;
        $this->assertTrue(
                $service->userHasRequestedTickets( $userId )
        );
    }
    
    /**
     * @covers MalwareBytes\ZendeskBundle\Service\ApiService::getTicketsCCedToUser
     */
    public function testGetTicketsCCedToUser()
    {
        $service = $this->apiService->setMethods( array( '_get' ) )->getMock();
        $userId = 'userid';
        $response = array( 'mock' );
        $service
            ->expects( $this->at( 0 ) )
            ->method ( '_get' )
            ->with   ( "/users/{$userId}/tickets/ccd" )
            ->will   ( $this->returnValue( $response ) )
        ;
        $this->assertEquals(
                $response,
                $service->getTicketsCCedToUser( $userId )
        );
    }
    
    /**
     * @covers MalwareBytes\ZendeskBundle\Service\ApiService::userHasCCs 
     */
    public function testUserHasCCsNoTickets()
    {
        $service = $this->apiService->setMethods( array( 'getTicketsCCedToUser' ) )->getMock();
        $userId = 'userid';
        $service
            ->expects( $this->at( 0 ) )
            ->method ( 'getTicketsCCedToUser' )
            ->with   ( $userId )
            ->will   ( $this->returnValue( array() ) )
        ;
        $this->assertFalse(
                $service->userHasCCs( $userId )
        );
    }
    
    /**
     * @covers MalwareBytes\ZendeskBundle\Service\ApiService::userHasCCs 
     */
    public function testUserHasCCsWithTickets()
    {
        $service = $this->apiService->setMethods( array( 'getTicketsCCedToUser' ) )->getMock();
        $userId = 'userid';
        $service
            ->expects( $this->at( 0 ) )
            ->method ( 'getTicketsCCedToUser' )
            ->with   ( $userId )
            ->will   ( $this->returnValue( array( 'tickets' => array( array( 'here is one' ) ) ) ) )
        ;
        $this->assertTrue(
                $service->userHasCCs( $userId )
        );
    }
    
    /**
     * @covers MalwareBytes\ZendeskBundle\Service\ApiService::setZendeskApi
     */
    public function testSetZendeskApi()
    {
        $service = $this->apiService->setMethods( null )->getMock();
        $this->assertEquals(
                $service,
                $service->setZendeskApi( $this->zendesk )
        );
        $this->assertAttributeEquals(
                $this->apiKey,
                'apiKey',
                $service
        );
    }
}
