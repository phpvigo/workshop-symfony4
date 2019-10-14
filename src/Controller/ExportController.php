<?php

namespace App\Controller;

use App\Repository\HashtagRepository;
use App\Repository\TweetRepository;
use App\UseCase\TransformTweetsOfHashtagIntoFormat;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
    public function exportHashtag(string $slug, string $type, TweetRepository $tweetRepository, HashtagRepository $hashtagRepository)
    {
        $hashtag = $hashtagRepository->find($slug);
        $tweets = $tweetRepository->findBy(['hashtag' => $hashtag]);

        $response = new Response();
        switch ($type) {
            case "json":
                $response->headers->set('Content-Type', 'application/json');
                break;

            case "csv":
                $response->headers->set('Content-Type', 'text/csv');
                break;

            case "excel":
                $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                $response->headers->set('Content-Disposition', 'attachment; filename="hashtag-' . (new \DateTimeImmutable())->format('YmdHis') . '.xlsx"');
                $response->sendHeaders();
                break;
        }

        $response->setContent((new TransformTweetsOfHashtagIntoFormat())->dispatch($hashtag, $tweets, $type));

        return $response;
    }


}


