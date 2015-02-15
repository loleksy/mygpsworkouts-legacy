<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 14.02.15
 * Time: 19:12
 */

namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class AuthenticationEntryPoint implements AuthenticationEntryPointInterface {

    /**
     * @var Router
     */
    protected $router;

    public function __construct(RouterInterface $router){
        $this->router = $router;
    }

    /**
     * Starts the authentication scheme.
     *
     * @param Request $request The request that resulted in an AuthenticationException
     * @param AuthenticationException $authException The exception that started the authentication process
     *
     * @return Response
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        if($request->isXmlHttpRequest()){
            //401 json if ajax
            $array = array('message' => 'Unauthorized');
            $response = new Response(json_encode($array), 401);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        else{
            //redirect to login page
            return new RedirectResponse($this->router->generate('fos_user_security_login'));
        }
    }
}