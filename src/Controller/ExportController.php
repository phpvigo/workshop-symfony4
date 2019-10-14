<?php

namespace App\Controller;

use App\Repository\HashtagRepository;
use App\Repository\TweetRepository;
use App\Service\ResponserFormatterContainer;
use App\UseCase\TransformTweetsOfHashtagIntoFormat;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExportController
{
    /**
     * @Route("/export/{slug}/{type}", name="export_hashtag")
     *
     * @param string $slug
     * @param string $type
     * @param TweetRepository $tweetRepository
     * @param HashtagRepository $hashtagRepository
     * @return Response
     * @throws \Exception
     */
    public function exportHashtag(string $slug, string $type, TweetRepository $tweetRepository, HashtagRepository $hashtagRepository, ResponserFormatterContainer $responserFormatterContainer)
    {
        $hashtag = $hashtagRepository->find($slug);
        $tweets = $tweetRepository->findBy(['hashtag' => $hashtag]);

        $response = $responserFormatterContainer->format($type);
        $response->setContent((new TransformTweetsOfHashtagIntoFormat())->dispatch($hashtag, $tweets, $type));

        return $response;
    }
}


