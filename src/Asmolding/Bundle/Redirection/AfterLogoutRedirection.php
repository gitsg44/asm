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
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

/**
 * Description of AfterLogoutRedirection
 *
 * @author SGOURMELON
 */
class AfterLogoutRedirection implements LogoutSuccessHandlerInterface
{
    /**
     * @var RouterInterfacer;
     */
    private $router;
    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $security;
 
    /**
     * @param SecurityContextInterface $security
     */
    public function __construct(RouterInterface $router, SecurityContextInterface $security)
    {
        $this->router = $router;
        $this->security = $security;
    }
 
    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function onLogoutSuccess(Request $request)
    {
        // On récupère la liste des rôles d'un utilisateur
        $roles = $this->security->getToken()->getRoles();
        // On transforme le tableau d'instance en tableau simple
        $rolesTab = array_map(function($role){ 
            return $role->getRole(); 
        }, $roles);
        // Si c'est admin ou un client on redirige vers la page de login.
        if (in_array('ROLE_ADMIN', $rolesTab, true) || in_array('ROLE_CHEFPROJET', $rolesTab, true) || in_array('ROLE_PARTENAIRE', $rolesTab, true) || in_array('ROLE_CLIENT', $rolesTab, true) ){
            $response = new RedirectResponse($this->router->generate('asmolding_login'));
        }
 
        return $response;
    }

}
