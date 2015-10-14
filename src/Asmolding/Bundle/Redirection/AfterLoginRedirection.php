<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Asmolding\Bundle\Redirection;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * Description of AfterLoginRedirection
 *
 * @author SGOURMELON
 */
class AfterLoginRedirection implements AuthenticationSuccessHandlerInterface {
    
    /**
     * @var RouterInterfacer;
     */
 
   protected $router;
    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }
 
    /**
     * @param Request $request
     * @param TokenInterface $token
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        // On récupère la liste des rôles d'un utilisateur
        $roles = $token->getRoles();
        // On transforme le tableau d'instance en tableau simple
        $rolesTab = array_map(function($role){ 
          return $role->getRole(); 
        }, $roles);
        // S'il s'agit d'un admin on le redirige vers l'espace admin
        if (in_array('ROLE_ADMIN', $rolesTab, true) || in_array('ROLE_CHEFPROJET', $rolesTab, true)){
            $redirection = new RedirectResponse($this->router->generate('asmolding_admin_homepage'));
        }
        // sinon il s'agit d'un client
        elseif(in_array('ROLE_PARTENAIRE', $rolesTab, true) || in_array('ROLE_CLIENT', $rolesTab, true)){
            $redirection = new RedirectResponse($this->router->generate('asmolding_client_homepage'));
        }
 
        return $redirection;
    }

}
