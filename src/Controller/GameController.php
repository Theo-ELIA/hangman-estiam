<?php
/**
 * Created by PhpStorm.
 * User: nono
 * Date: 03/11/2017
 * Time: 14:15
 */

namespace App\Controller;

use App\Game\GameContext;
use App\Game\GameRunner;
use App\Game\Loader\TextFileLoader;
use App\Game\Loader\XmlFileLoader;
use App\Game\WordList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/game")
 */
class GameController extends Controller {
    /**
     * @Route("/")
     */
    public function game(Request $request){
        //dump(getenv(('APP_ENV')));
        $session = $request->getSession();
        $wordList = new WordList();
        $wordList->addLoader('txt',new TextFileLoader());
        $wordList->addLoader('xml',new XmlFileLoader());

        $wordList->loadDictionaries([
            __DIR__.'/../../data/test.txt',
            __DIR__.'/../../data/words.txt',
            __DIR__.'/../../data/words.xml',
        ]);

        $context = new GameContext($session);

        $runner = new GameRunner($context,$wordList);


        return $this->render('game/game.html.twig',[
            'game' => $runner->loadGame(11),
        ]);
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

    /**
     * @Route("/won")
     */
    public function won(){
        //dump(getenv(('APP_ENV')));
        return $this->render('game/won.html.twig');
    }

    /**
     * @Route("/failed")
     */
    public function failed(){
        //dump(getenv(('APP_ENV')));
        return $this->render('game/failed.html.twig');
    }
}