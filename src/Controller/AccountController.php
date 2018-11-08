<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Psr\Log\LoggerInterface;

// new controller : make:controller

/**
 * @IsGranted("ROLE_USER") 
 */
class AccountController extends BaseController
{
    /**
     * @Route("/account", name="app_account")
     */
    public function index(LoggerInterface $logger)
    {
    	// dd($this->getUser()->getFirstName());
    	// use the LoggerInterface to to get into the profiler (log - debug)
    	$logger->debug('Checking account page for '.$this->getUser()->getEmail());
        return $this->render('account/index.html.twig', [
            
        ]);
    }
    /**
     * @Route("/api/account", name="api_account")
     */
    public function accountApi()
    {
        $user = $this->getUser();
        // jsnon() - checks to see if Symfony's serializer component is installed.
        // all it does internally is called json_encode() on that data we pass in.
        // Do you know what happens when you call json_encode() on an object in PHP? 
        // It only... sorta works: it encodes only the public properties on that class. 
        // And because we have no public properties, we get back nothing! AH AH !
        // return $this->json($user);
        // to tell the json() method to only serialize properties that are in the group called "main".
        // json(data, statut code, header, groups)
        return $this->json($user, 200, [], [ 
            'groups' => ['main'],
        ]);
    }
}
