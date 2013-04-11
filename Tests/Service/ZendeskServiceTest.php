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
    
    /**
     * @covers Malwarebytes\ZendeskBundle\Service\ZendeskService::createTicketAsUser
     */
    public function testCreateTicketAsUser()
    {
        $mockUser = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\User\Entity' )
                         ->disableOriginalConstructor()
                         ->setMethods( array( 'offsetGet' ) )
                         ->getMock();
        
        $mockTicketRepo = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository' )
                               ->disableOriginalConstructor()
                               ->setMethods( array( 'factory', 'save' ) )
                               ->getMock();
        
        $mockTicket = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                           ->disableOriginalConstructor()
                           ->setMethods( array( 'offsetSet' ) )
                           ->getMock();
        
        $service = $this->_getServiceMock( array( 'getById' ) );
        
        $userId = 123;
        $subject = 'drake is too damn soft';
        $comment = 'aubrey graham';
        
        $service
            ->expects( $this->once() )
            ->method ( 'getById' )
            ->with   ( 'User', 123 )
            ->will   ( $this->returnValue( $mockUser ) )
        ;
        $this->_repo
            ->expects( $this->once() )
            ->method ( 'get' )
            ->with   ( 'Ticket' )
            ->will   ( $this->returnValue( $mockTicketRepo ) )
        ;
        $mockTicketRepo
            ->expects( $this->once() )
            ->method ( 'factory' )
            ->will   ( $this->returnValue( $mockTicket ) )
        ;
        $mockUser
            ->expects( $this->once() )
            ->method ( 'offsetGet' )
            ->with   ( 'id' )
            ->will   ( $this->returnValue( 123 ) )
        ;
        $mockTicket
            ->expects( $this->at( 0 ) )
            ->method ( 'offsetSet' )
            ->with   ( 'requester_id', 123 )
        ;
        $mockTicket
            ->expects( $this->at( 1 ) )
            ->method ( 'offsetSet' )
            ->with   ( 'subject', $subject )
        ;
        $mockTicket
            ->expects( $this->at( 2 ) )
            ->method ( 'offsetSet' )
            ->with   ( 'comment', array( 'body' => $comment ) )
        ;
        $mockTicketRepo
            ->expects( $this->once() )
            ->method ( 'save' )
            ->with   ( $mockTicket )
        ;
        $this->assertEquals(
                $service,
                $service->createTicketAsUser( $userId, $subject, $comment )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\Service\ZendeskService::addCollaboratorToTicket
     */
    public function testAddCollaboratorToTicket()
    {
        $service = $this->_getServiceMock( array( 'getById' ) );
        
        $mockUserRepo = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\User\Repository' )
                             ->disableOriginalConstructor()
                             ->setMethods( array( 'getForNameAndEmail' ) )
                             ->getMock();
        
        $mockTicket = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                           ->disableOriginalConstructor()
                           ->setMethods( array( 'addCollaborator' ) )
                           ->getMock();
        
        $mockUser = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\User\Entity' )
                         ->disableOriginalConstructor()
                         ->getMock();
        
        $id = 123;
        $name = 'Foo Barson';
        $email = 'Foo@bar.com';
        
        $service
            ->expects( $this->once() )
            ->method ( 'getById' )
            ->with   ( 'Ticket', $id )
            ->will   ( $this->returnValue( $mockTicket ) )
        ;
        $this->_repo
            ->expects( $this->once() )
            ->method ( 'get' )
            ->with   ( 'User' )
            ->will   ( $this->returnValue( $mockUserRepo ) )
        ;
        $mockUserRepo
            ->expects( $this->once() )
            ->method ( 'getForNameAndEmail' )
            ->with   ( $name, $email )
            ->will   ( $this->returnValue( $mockUser ) )
        ;
        $mockTicket
            ->expects( $this->once() )
            ->method ( 'addCollaborator' )
            ->with   ( $mockUser )
        ;
        $this->assertEquals(
                $service,
                $service->addCollaboratorToTicket( $id, $name, $email )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\Service\ZendeskService::changeTicketGroup
     */
    public function testChangeTicketGroup()
    {
        $mockTicket = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                           ->disableOriginalConstructor()
                           ->setMethods( array( 'offsetSet' ) )
                           ->getMock();
        
        $mockGroup = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Group\Entity' )
                          ->disableOriginalConstructor()
                          ->setMethods( array( 'offsetGet' ) )
                          ->getMock();
        
        $mockTicketRepo = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository' )
                               ->disableOriginalConstructor()
                               ->setMethods( array( 'save' ) )
                               ->getMock();
        
        $service = $this->_getServiceMock( array( 'getById' ) );
        
        $ticketId = 123;
        $groupId = 234;
        
        $service
            ->expects( $this->at( 0 ) )
            ->method ( 'getById' )
            ->with   ( 'Ticket', $ticketId )
            ->will   ( $this->returnValue( $mockTicket ) )
        ;
        $service
            ->expects( $this->at( 1 ) )
            ->method ( 'getById' )
            ->with   ( 'Group' )
            ->will   ( $this->returnValue( $mockGroup ) )
        ;
        $mockGroup
            ->expects( $this->once() )
            ->method ( 'offsetGet' )
            ->with   ( 'id' )
            ->will   ( $this->returnValue( $groupId ) )
        ;
        $mockTicket
            ->expects( $this->once() )
            ->method ( 'offsetSet' )
            ->with   ( 'group_id', $groupId )
        ;
        $this->_repo
            ->expects( $this->once() )
            ->method ( 'get' )
            ->with   ( 'Ticket' )
            ->will   ( $this->returnValue( $mockTicketRepo ) )
        ;
        $mockTicketRepo
            ->expects( $this->once() )
            ->method ( 'save' )
            ->with   ( $mockTicket )
        ;
        $this->assertEquals(
                $service,
                $service->changeTicketGroup( $ticketId, $groupId )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\Service\ZendeskService::addCommentToTicket
     */
    public function testAddCommentToTicket()
    {
        $mockTicket = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                           ->disableOriginalConstructor()
                           ->setMethods( array( 'addComment' ) )
                           ->getMock();
        
        $service = $this->_getServiceMock( array( 'getById' ) );
        
        $id = 123;
        $comment = 'j cole is a wimp';
        
        $service
            ->expects( $this->once() )
            ->method ( 'getById' )
            ->with   ( 'Ticket', $id )
            ->will   ( $this->returnValue( $mockTicket ) )
        ;
        $mockTicket
            ->expects( $this->once() )
            ->method ( 'addComment' )
            ->with   ( $comment, true )
        ;
        $this->assertEquals(
                $mockTicket,
                $service->addCommentToTicket( $id, $comment, true )
        );
    }
    
    /**
     * @covers Malwarebytes\ZendeskBundle\Service\ZendeskService::getById
     */
    public function testGetById()
    {
        $service = $this->_getServiceMock();
        $cache = new \ReflectionProperty( 'Malwarebytes\ZendeskBundle\Service\ZendeskService', '_cache' );
        $cache->setAccessible( true );
        $this->assertEmpty(
                $cache->getValue( $service )
        );
        $mockTicketRepo = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Repository' )
                               ->disableOriginalConstructor()
                               ->setMethods( array( 'getById' ) )
                               ->getMock();
        
        $mockTicket = $this->getMockBuilder( 'Malwarebytes\ZendeskBundle\DataModel\Ticket\Entity' )
                           ->disableOriginalConstructor()
                           ->getMock();
        
        $this->_repo
            ->expects( $this->any() )
            ->method ( 'get' )
            ->with   ( 'Ticket' )
            ->will   ( $this->returnValue( $mockTicketRepo ) )
        ;
        $mockTicketRepo
            ->expects( $this->at( 0 ) )
            ->method ( 'getById' )
            ->with   ( 123 )
            ->will   ( $this->returnValue( $mockTicket ) )
        ;
        $this->assertEquals(
                $mockTicket,
                $service->getById( 'Ticket', 123 )
        );
        $this->assertEquals(
                array( 'Ticket' => array( '123' => $mockTicket ) ),
                $cache->getValue( $service )
        );
        $mockTicketRepo
            ->expects( $this->at( 0 ) )
            ->method ( 'getById' )
            ->with   ( 234 )
            ->will   ( $this->returnValue( null ) )
        ;
        try {
            $service->getById( 'Ticket', 234 );
        } catch ( \Exception $e ) {}
        $this->assertInstanceOf(
                'Exception',
                $e
        );
    }
    
}