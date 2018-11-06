<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface; 
use Symfony\Component\Security\Core\Exception\AuthenticationException; 
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use App\Repository\UserRepository;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
	private $userRepository;
	private $router;
	private $csrfTokenManager;

	public function __construct(UserRepository $userRepository, RouterInterface $router, CsrfTokenManagerInterface $csrfTokenManager)
	{
		$this->userRepository = $userRepository;
		$this->router = $router;
		$this->csrfTokenManager = $csrfTokenManager;
	}
	
	 // the supports() method is always called at the start of the request.
	public function supports(Request $request) {
		// return true if this request contains authentication info
		// to find out the name of the currently-matched route use the _route key from the request attributes
		// and verify if this is a POST request
		// if it return false from supports() , nothing else happens, and the request continues on like normal to our controller
		// If we return true , Symfony will immediately call getCredentials():
		return $request->attributes->get('_route') === 'app_login'
			&& $request->isMethod('POST');
	}

	public function getCredentials(Request $request) {
		// dd: a quick new Easter egg in Symfony 4.1 (dump & die)
		// dd($request->request->all());
		// getCredentials's job: read our authentication credentials off of the request and return them.
		// we'll return the email and password . 
		// But, if this were an API token authenticator, we would return that token. We'll see that later.
		// Store the last user name into the session.
		// let's also return a csrf_token key
		$credentials = [
			'email' => $request->request->get('email'), 
			'password' => $request->request->get('password'),
			'csrf_token' => $request->request->get('_csrf_token'),
		];
		// set the email onto the session
		// Use a special key: Security
		$request->getSession()->set( 
			Security::LAST_USERNAME, 
			$credentials['email']
		);
		return $credentials;
	}

	public function getUser($credentials, UserProviderInterface $userProvider) {
		// after we return from getCredentials(), Symfony will immediately call getUser() 
		// and pass this array back to us as the first $credentials argument
		// dd($credentials);
		// getUser's job is to use these $credentials to return a User object, we need the UserRepository
		// The cool thing is that if this returns null , the whole authentication process will stop, 
		// and the user will see an error. But if we return a User object, then Symfony immediately calls checkCredentials(),
		// and passes it the same $credentials and the User object we just returned:
		// getUser is where we'll check the CSRF token,  make sure it's valid before we query for the user.
		
		$token = new CsrfToken('authenticate', $credentials['csrf_token']);
		if (!$this->csrfTokenManager->isTokenValid($token)) {
			throw new InvalidCsrfTokenException(); 
		}
		return $this->userRepository->findOneBy(['email' => $credentials['email']]);
	}

	public function checkCredentials($credentials, UserInterface $user) {
		// checkCredentials'job: check to see if the user's password is correct,
		// only needed if we need to check a password - we'll do that later!
		// dd($user);
		// in many systems, simply returning true because authentication is successful! 
		// For example, if you have an API token system, there's no password.
		return true;
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
		// Now that the user is authenticated, what do you want to do?
		// For a form login system: redirect to another page 
		// For an API token system: nothing
		// Redirecting on Success (send the user to the homepage)
		// to redirect in Symfony, you return a RedirectResponse object, which is a sub-class of the normal Response . 
		// It just sets the status code to 301 or 302 and adds a Location header that points to where the user should go. 
		// a redirect is just a special type of response!
		// generateUrl() , is a shortcut to use the "router" to convert a route name into its URL
		$url = $this->router->generate('app_homepage');
		return new RedirectResponse($url);
	}

	protected function getLoginUrl() {
		//  if we fail login, the user should be redirected back to the login page.
		return $this->router->generate('app_login');
	}

}