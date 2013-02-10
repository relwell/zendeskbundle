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
    
    public function setUp()
    {
        $this->apiService = $this->getMockBuilder( '\MalwareBytes\ZendeskBundle\Service\ApiService' )
                                 ->disableOriginalConstructor();
        
        $this->zendesk = $this->getMockBuilder( '\zendesk' )
                              ->disableOriginalConstructor()
                              ->setMethods( array( 'call' ) )
                              ->getMock();
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
                'email' => $email
                );
        
        $responseArray = array( 'mockresponse' );
        
        $this->zendesk
            ->expects( $this->at( 0 ) )
            ->method ( 'call' )
            ->with   ( 'users', json_encode( $dataArray ), 'POST' )
            ->will   ( $this->returnValue( $responseArray ) )
        ;
        $service->setZendeskApi( $this->zendesk )
                ->createUser   ( $name, $email );
    }
}
