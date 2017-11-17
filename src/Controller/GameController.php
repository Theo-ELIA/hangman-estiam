<?php

namespace App\Controller;

use App\Game\GameRunner;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/game")
 * @Security("is_granted('ROLE_PLAYER')")
 */
class GameController extends Controller
{
    /**
     * @Route("/", name="game_index")
     */
    public function game()
    {
        return $this->render('game/game.html.twig',[
            'game' => $this->get(GameRunner::class)->loadGame(),
        ]);
    }
    /**
     * @Route("/letter/{letter}", requirements={"letter": "[A-Z]"})
     */
    public function letter(string $letter)
    {
        $game = $this->get(GameRunner::class)->playLetter($letter);

        if ($game->isOver()) {
            if ($game->isWon()) {
                return $this->redirectToRoute('app_game_won');
            }

            return $this->redirectToRoute('app_game_failed');
        }

        return $this->redirectToRoute('game_index');
    }

    /**
     * @Route("/reset")
     */
    public function reset()
    {
        $this->get(GameRunner::class)->resetGame();

        return $this->redirectToRoute('game_index');
    }

    /**
     * @Route("/word")
     */
    public function word(Request $request)
    {
        $game = $this->get(GameRunner::class)->playWord($request->request->getAlpha('word'));

        if ($game->isWon()) {
            return $this->redirectToRoute('app_game_won');
        }

        return $this->redirectToRoute('app_game_failed');
    }

    /**
     * @Route("/won")
     */
    public function won()
    {
        return $this->render('game/won.html.twig', [
            'game' => $this->get(GameRunner::class)->resetGameOnSuccess(),
        ]);
    }

    /**
     * @Route("/failed")
     */
    public function failed()
    {
        return $this->render('game/failed.html.twig', [
            'game' => $this->get(GameRunner::class)->resetGameOnFailure(),
        ]);
    }

    /**
     * @Route("/admin", name="admin_index")
     */
    public function admin()
    {
        return $this->render('admin/word-list-administration.html.twig', [
            'wordList' => $this->get(GameRunner::class)->getWordList()->getCustomWords(),
        ]);
    }

    /**
     * @Route("/removeWord/{word}", requirements={"word": "\w+"})
     */
    public function removeWord(string $word)
    {
        $game = $this->get(GameRunner::class)->getWordList()->removeCustomWord($word);

        return $this->redirectToRoute('admin_index');
    }

    /**
     * @Route("/addWord", name="add_custom_word")
     * Method("POST")
     */
    public function addWord(Request $request) {	
        $newWord = $request->request->get('newWord');
        $this->get(GameRunner::class)->getWordList()->addCustomWord($newWord);
        return $this->redirectToRoute('admin_index');
    }
}
