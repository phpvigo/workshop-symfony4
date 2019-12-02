<?php

namespace App\Controller;

use App\Repository\HashtagRepository;
use App\Repository\TweetRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class RandomTweetController
{
    /**
     * @Route("/hashtag/{slug}", name="random_tweet")
     *
     * @param string $slug
     * @param TweetRepository $tweetRepository
     * @param HashtagRepository $hashtagRepository
     * @param Environment $twig
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getRandomTweet(string $slug,
                                   TweetRepository $tweetRepository,
                                   HashtagRepository $hashtagRepository,
                                   Environment $twig): Response
    {
        $hashtag = $hashtagRepository->find($slug);

        return (new Response())->setContent($twig->render('default/randomTweet/winner.html.twig', [
            'hashtag' => $hashtag,
            'winner' => $tweetRepository->getRandomTweet($hashtag),
        ]));
    }
}
