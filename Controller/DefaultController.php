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
        $user = $this->get( 'zendesk.repos' )->get( 'User' )->getById( $userId );
        if ( empty( $user ) ) {
            throw new \Exception( "No user with ID {$userId}" );
        }
        
        $ticketRepo = $this->get( 'zendesk.repos' )->get( 'Ticket' );
        $auditRepo = $this->get( 'zendesk.repos' )->get( 'Audit' );
        
        $tickets = $ticketRepo->getTicketsRequestedByUser( $user );
        $ticketsRendered = array();
        foreach ( $tickets as $ticket ) {
            $ticket['comments'] = $auditRepo->getCommentsForTicket( $ticket );
            $ticketsRendered[] = $this->render( 'ZendeskBundle:Default:ticket.html.twig', array( 'ticket' => $ticket ) );
        }
        $tickets->rewind();
        return $this->render( 'ZendeskBundle:Default:view-user-tickets.html.twig', array( 'tickets' => $ticketsRendered, 'user' => $user ) );
    }

    public function addCommentAction()
    {
        $request = $this->getRequest();
        if ( $request->getMethod() == 'POST' ) {
            $data = $request->request->all();
            $ticket = $this->get( 'zendesk.repos' )->get( 'Ticket' )->getById( $data['ticketId'] );
            if ( empty( $ticket ) ) {
                throw new \Exception( "No such ticket." );
            }
            $ticket->addComment( $data['comment'], !empty( $data['public'] ) );
        } else {
            throw new \Exception( "only supports POST" );
        }
        return $this->redirect( $this->generateUrl( 'zendesk_ticket', array( 'ticket' => $ticket ) ) );
    }
        
    
    public function createTicketAction( $userId )
    {
        $user = $this->get( 'zendesk.repos' )->get( 'User' )->getById( $userId );
        if ( empty( $user ) ) {
            throw new \Exception( "No user with ID {$userId}" );
        }
        $request = $this->getRequest();
        if ( $request->getMethod() == 'POST' ) {
            $data = $request->request->all();
            $ticketRepo = $this->get( 'zendesk.repos' )->get( 'Ticket' );
            $ticket = new DataModel\Ticket\Entity( $ticketRepo );
            $ticket['requester_id'] = $userId;
            $ticket['subject'] = $data['subject'];
            $ticket['comment'] = array( 'body' => $data['comment'] );
            $ticketRepo->save( $ticket );
        }
        return $this->redirect( $this->generateUrl( 'zendesk_user_tickets', array( 'userId' => $userId ) ) );
    }

    public function addCollaboratorAction() {
        $request = $this->getRequest();
        $data = $request->request->all();
        $ticket = $this->get( 'zendesk.repos' )->get( 'Ticket' )->getById( $data['ticketId'] );
        if (! $ticket ) {
            throw new \Exception( "no ticket found" );
        }
        $user = $this->get( 'zendesk.repos' )->get( 'User' )->getForNameAndEmail( $data['name'], $data['email'] );
        if ( $user ) {
            $ticket->addCollaborator( $user );
        }
        return $this->redirect( $this->generateUrl( 'zendesk_ticket', array( 'ticket' => $ticket['id'] ) ) );
    }
    
    public function viewTicketAction( $ticket )
    {
        if (! $ticket instanceof \MalwareBytes\ZendeskBundle\DataModel\Ticket\Entity ) {
            $ticket = $this->get( 'zendesk.repos' )->get( 'Ticket' )->getById( $ticket );
        }
        $groups = $this->get( 'zendesk.repos' )->get( 'Group' )->getByDefaultSort();
        return $this->render( 'ZendeskBundle:Default:ticket.html.twig', array( 'ticket' => $ticket, 'groups' => $groups ) );
    }
    
    public function changeTicketGroupAction()
    {
        $request = $this->getRequest();
        $data = $request->request->all();
        $ticketRepo = $this->get( 'zendesk.repos' )->get( 'Ticket' );
        $ticket = $ticketRepo->getById( $data['ticketId'] );
        if (! $ticket ) {
            throw new \Exception( "no ticket found" );
        }
        $group = $this->get( 'zendesk.repos' )->get( 'Group' )->getById( $data['groupId'] );
        if (! $group ) {
            throw new \Exception( "no group found" );
        }
        $ticket['group_id'] = $group['id'];
        $ticketRepo->save( $ticket );
        return $this->redirect( $this->generateUrl( 'zendesk_ticket', array( 'ticket' => $ticket['id'] ) ) );
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
        $group = $this->get( 'zendesk.repos' )->get( 'Group' )->getById( $groupId );
        return $this->renderView( 'ZendeskBundle:Default:group.html.twig', array( 'group' => $group ) );
    }
    
    public function indexAction($name)
    {
        return $this->render('ZendeskBundle:Default:index.html.twig', array('name' => $name));
    }
}
