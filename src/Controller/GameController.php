<?php
/**
 * Created by PhpStorm.
 * User: nono
 * Date: 03/11/2017
 * Time: 14:15
 */

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
/**
 * @Route("/game")
 */
class GameController extends Controller {
    /**
     * @Route("/")
     */
    public function game(){
        //dump(getenv(('APP_ENV')));
        return $this->render('game/game.html.twig');
    }
    /**
     * @Route("/letter/{letter}")
     */
    public function letter($letter){
        //dump(getenv(('APP_ENV')));
        dump($letter);
        return $this->render('game/game.html.twig');
    }

    /**
     * @Route("/reset")
     */
    public function reset(){
        //dump(getenv(('APP_ENV')));
        return $this->render('game/game.html.twig');
    }

    /**
     * @Route("/word")
     */
    public function word(){
        //dump(getenv(('APP_ENV')));
        return $this->render('game/game.html.twig');
    }

    public function won(){
        //dump(getenv(('APP_ENV')));
        return $this->render('game/won.html.twig');
    }

    public function failed(){
        //dump(getenv(('APP_ENV')));
        return $this->render('game/failed.html.twig');
    }
}