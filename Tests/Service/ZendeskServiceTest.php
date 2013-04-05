<?php
/**
 * Class definition for Zendesk Service Test
 */
namespace Mawlarebytes\ZendeskBundle\Test\Service;

class ZendeskServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * This gets depednency injected on construct so here's a helper method
     * @param array|null $methods
     * @return Ambigous <PHPUnit_Framework_MockObject_MockObject, object>
     */
    protected function _getRepoMock( $methods = null )
    {
        return $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\Service\RepositoryService' )
                    ->disableOriginalConstructor()
                    ->setMethods( $methods )
                    ->getMock();
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\Service\ZendeskService::getTicketsWithCommentsForUserId
     */
    public function testGetTicketsWithCommentsForUserId()
    {
        $user = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\User\Entity' )
                     ->disableOriginalConstructor()
                     ->getMock();
        
        
    }
    
}