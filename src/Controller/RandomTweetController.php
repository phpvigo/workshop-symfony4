<?php

namespace App\Controller;

use App\Entity\Hashtag;
use App\Repository\HashtagRepository;
use App\Repository\TweetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tweet;

class RandomTweetController extends Controller
{
    /**
     * @Route("/hashtag/{slug}", name="random_tweet")
     *
     * @param string $slug
     * @param TweetRepository $tweetRepository
     * @param HashtagRepository $hashtagRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getRandomTweet(string $slug, TweetRepository $tweetRepository, HashtagRepository $hashtagRepository)
    {

        $hashtag = $hashtagRepository->find($slug);

        return $this->render('default/randomTweet/winner.html.twig', [
            'hashtag' => $hashtag,
            'winner' => $tweetRepository->getRandomTweet($hashtag),
        ]);
    }
}
