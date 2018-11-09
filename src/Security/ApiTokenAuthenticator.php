<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use App\Repository\ApiTokenRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class ApiTokenAuthenticator extends AbstractGuardAuthenticator
{
    private $apiTokenRepo;

    public function __construct(ApiTokenRepository $apiTokenRepo)
    {
        $this->apiTokenRepo = $apiTokenRepo; 
    }

    public function supports(Request $request)
    {
        // check if request have an Authorization header that start with Bearer
        return $request->headers->has('Authorization')
            && 0 === strpos($request->headers->get('Authorization'), 'Bearer ');

    }

    public function getCredentials(Request $request)
    {
        // getCredentials: read the token string and return it.
        $authorizationHeader = $request->headers->get('Authorization');
        // return token without bearer
        return substr($authorizationHeader, 7);
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        // dump($credentials);die;
        // query the ApiToken entity & get the user !
        $token = $this->apiTokenRepo->findOneBy([ 
            'token' => $credentials
        ]);
        // to control error messages in an authenticator, and it's super flexible. 
        // At any point in your authenticator, you can throw a new CustomUserMessageAuthenticationException() 
        // that will cause authentication to fail and accepts any custom error message you want, like, "Invalid API Token"
        // This exception will be passed to onAuthenticationFailure() and its getMessageKey() method will return that message.
        if (!$token) {
            throw new CustomUserMessageAuthenticationException(
                'Invalid API Token'
            ); 
        }
        // check if token is expired
        if ($token->isExpired()) {
            throw new CustomUserMessageAuthenticationException(
                'Token expired'
            ); 
        }
        // I chose getUser() just because we have access to the $token object there to check token with this 2 tests
        return $token->getUser();
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // dd('checking credentials');
        // no password to check to check here
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // Call When authentication fails, it's return a Response that should be sent back to the client.
        // If an API client sends a bad API token, we need to tell them! Bad API client!
        // Let's return a new JsonResponse() with a message key that describes what went wrong
        // If you fail to return a User from getUser() , you get this "Username could not be found"
        return new JsonResponse([
            'message' => $exception->getMessageKey()
        ], 401);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // when authentication is successful we do nothing
        // We want to allow the request to continue so that it can hit the controller and return the JSON response:
    }

    // start(): because we chose LoginFormAuthenticator as the entry_point , this will never be called.
    public function start(Request $request, AuthenticationException $authException = null)
    {
        throw new \Exception('Not used: entry_point from other authentication is used');
    }

    public function supportsRememberMe()
    {
        // If you return true from this method, it just means that the "remember me" 
        // system is activated and looking for that _remember_me checkbox to be checked.
        return false;
    }
}
