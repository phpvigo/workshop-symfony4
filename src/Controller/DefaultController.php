<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tweet;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="random_tweet")
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getRandomTweet(EntityManagerInterface $entityManager)
    {

        $tweets = $entityManager->getRepository(Tweet::class)->findAll();

        shuffle($tweets);

        $winner = $tweets[0];

        return $this->render('default/index.html.twig', [
            'winner' => $winner
        ]);

    }

}
