<?php

namespace RoanokeLibBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * //Route("/")
     */
    public function indexAction()
    {
        return $this->render('RoanokeLibBundle:Default:index.html.twig');
    }
    
    /**
     * @Route("/thankyou", name="thankyou")
     */
    public function thankyouAction()
    {
        return $this->render('RoanokeLibBundle:Default:thankyou.html.twig');
    }
}
