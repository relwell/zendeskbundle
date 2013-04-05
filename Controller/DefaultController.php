<?php

namespace Malwarebytes\ZendeskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Malwarebytes\ZendeskBundle\DataModel;

class DefaultController extends Controller
{
    public function accessUserAction( $name, $email )
    {
        $user = $this->get( 'zendesk.repos' )
                     ->get( 'User' )
                     ->getForNameAndEmail( 
                             $name, 
                             $email, 
                             $this->getRequest()->query->get( 'create', false) 
                             );
        $dataArray = empty( $user ) ? array( 'user' => null ) : $user->toArray();
        $dataArray['name'] = $name;
        $dataArray['email'] = $email;
        return $this->render( 'ZendeskBundle:Default:view-user.html.twig', $dataArray );
    }
    
    public function userTicketsAction( $userId )
    {
        $zendeskService = $this->get( 'zendesk.service' );
        $tickets = $zendeskService->getTicketsWithCommentsForUserId( $userId );
        $user = $zendeskService->getById( 'User', $userId );
        $groups = $this->get( 'zendesk.repos' )->get( 'Group' )->getByDefaultSort();
        return $this->render( 'ZendeskBundle:Default:view-user-tickets.html.twig', array( 'tickets' => $tickets, 'user' => $user, 'groups' => $groups ) );
    }

    public function addCommentAction()
    {
        $request = $this->getRequest();
        if ( $request->getMethod() == 'POST' ) {
            $data = $request->request->all();
            $ticket = $this->get( 'zendesk.service' )->addCommentToTicket( $data['ticketId'], $data['comment'], !empty( $data['public'] ) );
        } else {
            throw new \Exception( "only supports POST" );
        }
        return $this->redirect( $this->generateUrl( 'zendesk_ticket', array( 'ticket' => $ticket['id'] ) ) );
    }
        
    
    public function createTicketAction( $userId )
    {
        $request = $this->getRequest();
        if ( $request->getMethod() == 'POST' ) {
            $data = $request->request->all();
            $this->get( 'zendesk.service' )->createTicketAsUser( $userId, $data['subject'], $data['comment'] );
        }
        return $this->redirect( $this->generateUrl( 'zendesk_user_tickets', array( 'userId' => $userId ) ) );
    }

    public function addCollaboratorAction() {
        $request = $this->getRequest();
        $data = $request->request->all();
        $this->get( 'zendesk.service' )->addCollaboratorToTicket( $data['ticketId'], $data['name'], $data['email'] );
        return $this->redirect( $this->generateUrl( 'zendesk_ticket', array( 'ticket' => $data['ticketId'] ) ) );
    }
    
    public function viewTicketAction( $ticket )
    {
        $ticket = $this->get( 'zendesk.service' )->getById( 'Ticket', $ticket );
        $ticket['comments'] = $this->get( 'zendesk.service' )->getCommentsForTicket( $ticket );
        $groups = $this->get( 'zendesk.repos' )->get( 'Group' )->getByDefaultSort();
        return $this->render( 'ZendeskBundle:Default:ticket.html.twig', array( 'ticket' => $ticket, 'groups' => $groups ) );
    }
    
    public function changeTicketGroupAction()
    {
        $request = $this->getRequest();
        $data = $request->request->all();
        $this->get( 'zendesk.service' )->changeTicketGroup( $data['ticketId'], $data['groupId'] );
        return $this->redirect( $this->generateUrl( 'zendesk_ticket', array( 'ticket' => $data['ticketId'] ) ) );
    }
    
    public function untouchedTicketsAction( $unixtime )
    {
        $tickets = $this->get( 'zendesk.repos' )->get( 'Ticket' )->getOpenTicketsOlderThan( $unixtime );
        $ticketsRendered = array();
        foreach ( $tickets as $ticket ) {
            $ticketsRendered[] = $this->render( 'ZendeskBundle:Default:ticket.html.twig', array( 'ticket' => $ticket ) );
        }
        return $this->render( 'ZendeskBundle:Default:tickets.html.twig', array( 'tickets' => $tickets ) );
    }
    
    public function groupAction( $groupId )
    {
        $group = $this->get( 'zendesk.service' )->getById( 'Group', $groupId );
        return $this->renderView( 'ZendeskBundle:Default:group.html.twig', array( 'group' => $group ) );
    }
    
    public function indexAction($name)
    {
        return $this->render('ZendeskBundle:Default:index.html.twig', array('name' => $name));
    }
}
