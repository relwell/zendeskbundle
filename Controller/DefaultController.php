<?php

namespace Malwarebytes\ZendeskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Malwarebytes\ZendeskBundle\DataModel;

class DefaultController extends Controller
{
    public function accessUserAction($name, $email)
    {
        $service = $this->get( 'api' );
        $userRepo = new DataModel\User\Repository( $service );
        $user = $userRepo->getForNameAndEmail($name, $email);
        return print_r( $user->toArray() );
    }
    
    public function indexAction($name)
    {
        return $this->render('ZendeskBundle:Default:index.html.twig', array('name' => $name));
    }
}
