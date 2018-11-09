<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Security\LoginFormAuthenticator;


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

    // register methode is in security controller because after registration, I want to instantly authenticate the new user.
    /**
     * @Route("/register", name="app_register") 
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $formAuthenticator)
    {
        if ($request->isMethod('POST')) {
            $user = new User(); 
            $user->setEmail($request->request->get('email'));
            $user->setFirstName('Mystery');
            // encode password with passwordEncoder
            $user->setPassword($passwordEncoder->encodePassword( $user,
                $request->request->get('password') 
            ));

            $em = $this->getDoctrine()->getManager(); 
            $em->persist($user);
            $em->flush();

            // after registration login the user and redirect them to his initial page
            // with GuardAuthenticationHandler 
            // parameters : the user, the request, the autheticator and the name of your firewall(provider key): main
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $formAuthenticator,
                'main'
            );
        }

        return $this->render('security/register.html.twig');
    }
}
