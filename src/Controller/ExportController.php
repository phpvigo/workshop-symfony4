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
     * @param ResponserFormatterContainer $responserFormatterContainer
     * @return Response
     */
    public function exportHashtag(string $slug, string $type, TweetRepository $tweetRepository, HashtagRepository $hashtagRepository, ResponserFormatterContainer $responserFormatterContainer)
    {
        return $responserFormatterContainer->format(
            $type,
            (new TransformTweetsOfHashtagIntoFormat($hashtagRepository, $tweetRepository))->dispatch($slug, $type)
        );
    }
}


