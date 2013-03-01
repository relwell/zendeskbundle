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
        $request = $this->getRequest();
        if ( $request->getMethod() == 'POST' ) {
            $data = $request->request->all();
            $ticket = $ticketRepo->getById( $data['ticketId'] );
            if ( empty( $ticket ) ) {
                throw new \Exception( "No such ticket." );
            }
            $ticket->addComment( $data['comment'], !empty( $data['public'] ) );
        }
        
        $tickets = $ticketRepo->getTicketsRequestedByUser( $user );
        foreach ( $tickets as $ticket ) {
            $ticket['comments'] = $auditRepo->getCommentsForTicket( $ticket );
        }
        $tickets->rewind();
        return $this->render( 'ZendeskBundle:Default:view-user-tickets.html.twig', array( 'tickets' => $tickets, 'user' => $user ) );
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
    
    public function indexAction($name)
    {
        return $this->render('ZendeskBundle:Default:index.html.twig', array('name' => $name));
    }
}
