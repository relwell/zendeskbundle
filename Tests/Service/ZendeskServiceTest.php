<?php
/**
 * Class definition for Zendesk Service Test
 */
namespace Mawlarebytes\ZendeskBundle\Test\Service;

class ZendeskServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * This gets depednency injected on construct so here's a helper method
     * @return Ambigous <PHPUnit_Framework_MockObject_MockObject, object>
     */
    protected function _getRepoMock()
    {
        $repo =  $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\Service\RepositoryService' )
                      ->disableOriginalConstructor()
                      ->setMethods( array( 'get' ) )
                      ->getMock();
        $this->_repo = $repo;
        return $repo;
    }
    
    protected function _getServiceMock( $methods = null )
    {
        return $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\Service\ZendeskService' )
                    ->setConstructorArgs( array( $this->_getRepoMock() ) )
                    ->setMethods( $methods )
                    ->getMock();
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\Service\ZendeskService::__construct
     * @covers Malwarebytes\ZendeskBundle\Service\ZendeskService::getTicketsWithCommentsForUserId
     */
    public function testGetTicketsWithCommentsForUserId()
    {
        $user = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\User\Entity' )
                     ->disableOriginalConstructor()
                     ->getMock();
        
        $ticketRepo = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository' )
                           ->disableOriginalConstructor()
                           ->setMethods( array( 'getTicketsRequestedByUser' ) )
                           ->getMock();
        
        $ticket = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                       ->disableOriginalConstructor()
                       ->setMethods( array( 'offsetSet' ) )
                       ->getMock();
        
        $service = $this->_getServiceMock( array( 'getTicketsRequestedByUser', 'getById', 'getCommentsForTicket' ) );
        
        $service
            ->expects( $this->once() )
            ->method ( 'getById' )
            ->with   ( 'User', 123 )
            ->will   ( $this->returnValue( $user ) )
        ;
        $this->_repo
            ->expects( $this->once() )
            ->method ( 'get' )
            ->with   ( 'Ticket' )
            ->will   ( $this->returnValue( $ticketRepo ) )
        ;
        $ticketRepo
            ->expects( $this->once() )
            ->method ( 'getTicketsRequestedByUser' )
            ->with   ( $user )
            ->will   ( $this->returnValue( array( $ticket ) ) )
        ;
        $service
            ->expects( $this->once() )
            ->method ( 'getCommentsForTicket' )
            ->with   ( $ticket )
            ->will   ( $this->returnValue( array( 'foo' ) ) )
        ;
        $ticket
            ->expects( $this->once() )
            ->method ( 'offsetSet' )
            ->with   ( 'comments', array( 'foo' ) )
        ;
        $this->assertEquals(
                array( $ticket ),
                $service->getTicketsWithCommentsForUserId( 123 )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\Service\ZendeskService::getCommentsForTicket
     */
    public function testGetCommentsForTicket()
    {
        $auditRepo = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Audit\Repository' )
                          ->disableOriginalConstructor()
                          ->setMethods( array( 'getCommentsForTicket' ) )
                          ->getMock();
        
        $ticket = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                       ->disableOriginalConstructor()
                       ->setMethods( array( 'offsetSet' ) )
                       ->getMock();
        
        $service = $this->_getServiceMock();
        $this->_repo
            ->expects( $this->once() )
            ->method ( 'get' )
            ->with   ( 'Audit' )
            ->will   ( $this->returnValue( $auditRepo ) )
        ;
        $auditRepo
            ->expects( $this->once() )
            ->method ( 'getCommentsForTicket' )
            ->with   ( $ticket )
            ->will   ( $this->returnValue( array( 'foo' ) ) )
        ;
        $this->assertEquals(
                array( 'foo' ),
                $service->getCommentsForTicket( $ticket )
        );
    }
    
}