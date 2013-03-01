<?php
/**
 * Class definition for Malwarebytes\ZendeskBundle\DataModel\ApiResponseException
 */
namespace Malwarebytes\ZendeskBundle\DataModel;
use Exception;

class ApiResponseException extends Exception
{
    /**
     * @var array
     */
    public $response;
    
    public function __construct( array $response )
    {
        $this->response = $response;
        $prepared = <<<ENDSTRING
API Response Error: {$response['error']} -- {$response['description']}
ENDSTRING;
        
        parent::__construct( $prepared );
    }
}