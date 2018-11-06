<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

// created with make:controller

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
    	// Copy-colle this method from https://symfony.com/doc/current/security/form_login_setup.html
        // get the login error if there is one
        // our authenticator stores the error in the session
        // the lastAuthenticationError() method is just a shortcut to read that key off of the session!
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="app_logout") 
     */
    public function logout()
    {
        // Remember how "authenticators" run automatically at the beginning of every request, before the controllers? 
        // The logout process works the same way. All we need to do is tell Symfony what URL we want to use for logging out.
        // at the beginning of that request, Symfony will automatically log the user out and then redirect them... 
        // all before the controller is ever executed.
        // get a look to security.yaml
        throw new \Exception('Will be intercepted before getting here');
    }
}
