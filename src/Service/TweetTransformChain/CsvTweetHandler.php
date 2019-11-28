<?php


namespace App\Service\TweetTransformChain;


use App\Entity\TweetCollection;

class CsvTweetHandler extends AbstractTweetTransform
{
    public function handle(TweetTransformRequest $tweetTransformRequest): string
    {
        if ($tweetTransformRequest->type() !== 'csv') {
            return parent::handle($tweetTransformRequest);
        }

        return $this->processResponse($tweetTransformRequest->tweetCollection());
    }

    /**
     * @param TweetCollection $tweets
     * @return string
     */
    private function processResponse(TweetCollection $tweets): string
    {
        $csvTempFile = fopen('php://memory', 'r+');
        fputcsv($csvTempFile, ['id', 'username', 'user_image', 'content', 'link', 'date']);
        foreach ($tweets AS $tweet) {
            fputcsv($csvTempFile, [$tweet->getTweetId(), $tweet->getUserName(), $tweet->getUserImage(), $tweet->getContent(), 'https://twitter.com/' . $tweet->getUserName() . '/status/' . $tweet->getTweetId(), $tweet->getCreatedAt()->format('c')]);
        }
        rewind($csvTempFile);
        $csv_line = stream_get_contents($csvTempFile);
        fclose($csvTempFile);
        return $csv_line;
    }
}