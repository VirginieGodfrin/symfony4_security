<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface; 
use Symfony\Component\Security\Core\Exception\AuthenticationException; 
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
	
	 // the supports() method is always called at the start of the request.
	public function supports(Request $request) {
		die('Our authenticator is alive!');
	}

	public function getCredentials(Request $request) {
		
	}

	public function getUser($credentials, UserProviderInterface $userProvider) {
		
	}

	public function checkCredentials($credentials, UserInterface $user) {
		
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
		
	}

	protected function getLoginUrl() {
		
	}

}