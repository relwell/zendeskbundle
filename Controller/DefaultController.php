<?php

namespace Malwarebytes\ZendeskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Malwarebytes\ZendeskBundle\DataModel;

class DefaultController extends Controller
{
    public function accessUserAction($name, $email)
    {
        var_dump( $this->get( 'zendesk.repos' )->get( 'User' )->getForNameAndEmail( $name, $email ) ); die;
    }
    
    public function indexAction($name)
    {
        return $this->render('ZendeskBundle:Default:index.html.twig', array('name' => $name));
    }
}
