<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\Test\Service\RepositoryServiceTest
 */
namespace Malwarebytes\ZendeskBundle\Test\Service;

use Malwarebytes\ZendeskBundle\Service\RepositoryService;

class RepositoryServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Malwarebytes\ZendeskBundle\Service\RepositoryService::__construct
     */
    public function test__construct()
    {
        $mockApiService = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\Service\ApiService' )
                               ->disableOriginalConstructor()
                               ->getMock();
        
        $repositoryService = new RepositoryService( $mockApiService );
        
        $reflApi = new \ReflectionProperty( '\Malwarebytes\ZendeskBundle\Service\RepositoryService', '_api' );
        $reflApi->setAccessible( true );
        $this->assertEquals(
                $mockApiService,
                $reflApi->getValue( $repositoryService )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\Service\RepositoryService::get
     */
    public function testGet()
    {
        $mockApiService = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\Service\ApiService' )
                               ->disableOriginalConstructor()
                               ->getMock();
        
        $mockRepoService = $this->getMockBuilder( '\Malwarebytes\ZendeskBundle\Service\RepositoryService' )
                                ->setConstructorArgs( array( $mockApiService ) )
                                ->setMethods( null )
                                ->getMock();
        
        try {
            $mockRepoService->get( 'Fake' );
        } catch ( \Exception $e ) { }
        
        $this->assertInstanceOf(
                '\Exception',
                $e
        );
        
        $this->assertInstanceOf(
                '\Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository',
                $mockRepoService->get( 'Ticket' )
        );
    }
}