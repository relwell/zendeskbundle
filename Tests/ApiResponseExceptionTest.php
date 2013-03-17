<?php
/**
 * Class definition for \Malwarebytes\ZendeskBundle\Test\DataModel\ApiResponseExceptionTest
 */
namespace Malwarebytes\ZendeskBundle\Test\DataModel;
use Malwarebytes\ZendeskBundle\DataModel\ApiResponseException;
/**
 * Tests ApiResponseException
 * @author relwell
 */
class ApiResponseExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Malwarebytes\ZendeskBundle\DataModel\ApiResponseException::__construct
     */
    public function test__construct()
    {
        $response = array( 'error' => 'damn', 'description' => 'something bad' );
        try {
            throw new ApiResponseException( $response );
        } catch ( ApiResponseException $e ) {}
        $this->assertContains(
                "API Response Error: {$response['error']} -- {$response['description']}",
                (string) $e
        );
    }
    
}