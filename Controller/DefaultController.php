<?php

namespace Malwarebytes\ZendeskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Malwarebytes\ZendeskBundle\DataModel;

class DefaultController extends Controller
{
    public function accessUserAction($name, $email)
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
        return $this->render( 'ZendeskBundle:Default:view.html.twig', $dataArray );
    }
    
    public function indexAction($name)
    {
        return $this->render('ZendeskBundle:Default:index.html.twig', array('name' => $name));
    }
}
