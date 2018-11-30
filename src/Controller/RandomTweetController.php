<?php

namespace App\Controller;

use App\Repository\HashtagRepository;
use App\Repository\TweetRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RandomTweetController
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
    public function getRandomTweet(string $slug, TweetRepository $tweetRepository, HashtagRepository $hashtagRepository, \Twig_Environment $twig)
    {
        $hashtag = $hashtagRepository->find($slug);

        return (new Response())->setContent($twig->render('default/randomTweet/winner.html.twig', [
            'hashtag' => $hashtag,
            'winner' => $tweetRepository->getRandomTweet($hashtag),
        ]));
    }
}
