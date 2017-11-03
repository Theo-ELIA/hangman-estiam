<?php
/**
 * Created by PhpStorm.
 * User: nono
 * Date: 03/11/2017
 * Time: 11:54
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends Controller {
    /**
     * @Route("/")
     */
    public function index(){
        //dump(getenv(('APP_ENV')));
        //dump($this->get('logger'));
        return $this->render('main/index.html.twig');
    }

}