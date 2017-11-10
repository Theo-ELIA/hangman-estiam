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
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Route("/game")
 */
class GameController extends Controller
{
    /**
     * @Route("/", name="game_index")
     */
    public function game(SessionInterface $session)
    {
        return $this->render('game/game.html.twig',[
            'game' => $this->getRunner($session)->loadGame(),
        ]);
    }
    /**
     * @Route("/letter/{letter}", requirements={"letter": "[A-Z]"})
     */
    public function letter(SessionInterface $session, $letter)
    {
        $this->getRunner($session)->playLetter($letter);

        return $this->redirectToRoute('game_index');
    }

    /**
     * @Route("/reset")
     */
    public function reset(SessionInterface $session)
    {
        $this->getRunner($session)->resetGame();

        return $this->redirectToRoute('game_index');
    }

    /**
     * @Route("/word")
     */
    public function word(Request $request)
    {
        $game = $this->getRunner($request->getSession())->playWord($request->request->getAlpha('word'));

        if ($game->isWon()) {
            return $this->redirectToRoute('app_game_won');
        }

        return $this->redirectToRoute('app_game_failed');
    }

    /**
     * @Route("/won")
     */
    public function won(SessionInterface $session)
    {
        return $this->render('game/won.html.twig', [
            'game' => $this->getRunner($session)->resetGameOnSuccess(),
        ]);
    }

    /**
     * @Route("/failed")
     */
    public function failed(SessionInterface $session)
    {
        return $this->render('game/failed.html.twig', [
            'game' => $this->getRunner($session)->resetGameOnFailure(),
        ]);
    }

    private function getRunner(SessionInterface $session): GameRunner
    {
        $wordList = new WordList();
        $wordList->addLoader('txt',new TextFileLoader());
        $wordList->addLoader('xml',new XmlFileLoader());

        $wordList->loadDictionaries([
            __DIR__.'/../../data/test.txt',
            __DIR__.'/../../data/words.txt',
            __DIR__.'/../../data/words.xml',
        ]);

        $context = new GameContext($session);

        return new GameRunner($context,$wordList);
    }
}
