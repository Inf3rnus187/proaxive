<?php
/*
 * AdminRedirectController Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date October 19, 2021
 */
namespace App\Controller\Dashboard;

class AdminRedirectController extends AppController
{
    /**
     * Permet de rediriger vers la page de non connecté
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function needlogin()
    {
        $this->render('dashboard/redirect/needlogin.twig');
    }


    /**
     * Permet de rediriger vers la page besoin de permission
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function needperms()
    {
        $this->render('dashboard/redirect/needperms.twig');
    }


    /**
     * Permet de rediriger vers la page d'erreur 404
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function notfound()
    {
        $this->render('dashboard/redirect/notfound.twig');
    }
}