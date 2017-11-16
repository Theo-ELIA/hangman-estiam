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
}
