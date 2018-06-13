<?php

namespace App\Command;

use App\Entity\Hashtag;
use App\Entity\Tweet;
use App\Repository\HashtagRepository;
use App\Repository\TweetRepository;
use App\Service\TwitterClient;
use App\ValueObject\TwitterSearch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TweetFetchCommand extends Command
{
    protected static $defaultName = 'tweet:fetch';

    private $twitterClient;
    private $tweetRepository;
    private $hashtagRepository;

    public function __construct(TwitterClient $twitterClient, TweetRepository $tweetRepository, HashtagRepository $hashtagRepository)
    {
        $this->twitterClient = $twitterClient;
        $this->tweetRepository = $tweetRepository;
        $this->hashtagRepository = $hashtagRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Fetch and persist tweets')
            ->addArgument('text', InputArgument::REQUIRED, 'Text to search')
            ->addArgument('count', InputArgument::OPTIONAL, 'Max. number of tweets to fetch', 4)
            ->addArgument(
                'result_type',
                InputArgument::OPTIONAL,
                <<<'EOD'
Specifies what type of search results you would prefer to receive. The current default is "mixed." Valid values include:

- mixed : Include both popular and real time results in the response.

- recent : return only the most recent results in the response

- popular : return only the most popular results in the response.

EOD
                ,
                'recent'
            )
            ->addOption('include-entities', 'i', InputOption::VALUE_NONE, 'Include entities in twitter fetch')
            ->addOption('no-persist', null, InputOption::VALUE_NONE, 'Disable tweet persistence');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $persist = !$input->getOption('no-persist');
        $twitterSearch = $this->processInputAndReturnTwitterSearch($input, $persist);

        $io->title(sprintf('Searching tweets for: %s', $twitterSearch->hashtag()->getName()));

        $this->buildAndShowTweets(
            $this->twitterClient->findTweetsWith($twitterSearch)->statuses,
            $twitterSearch->hashtag(),
            $io,
            $persist
        );

        $io->success('Operation finished!');
    }

    private function processInputAndReturnTwitterSearch(InputInterface $input, bool $persist) : TwitterSearch
    {
        return new TwitterSearch(
            $this->obtainHashtagFromText($input->getArgument('text'), $persist),
            (bool) $input->getOption('include-entities'),
            (string) $input->getArgument('result_type'),
            (int) $input->getArgument('count')
        );
    }

    private function obtainHashtagFromText(string $text, bool $persist) : Hashtag
    {
        $hashtag = $this->hashtagRepository->findOneByName($text);
        return !empty($hashtag) ? $hashtag : $this->constructHashtagAndReturn($text, $persist);
    }

    private function constructHashtagAndReturn(string $text, bool $persist) : Hashtag
    {
        $hashtag = Hashtag::fromName($text);
        if ($persist === true) {
            $this->hashtagRepository->save($hashtag);
        }
        return $hashtag;
    }

    private function buildAndShowTweets(array $tweetSearch, Hashtag $hashtag, SymfonyStyle $io, bool $persist) : void
    {
        if (empty($tweetSearch)) {
            $io->error('No tweets found!');
            return;
        }

        $tweets = $this->buildTweets($tweetSearch, $hashtag, $io, $persist);
        $this->showTweets($io, ... $tweets);
    }

    private function buildTweets(array $tweets, Hashtag $hashtag, SymfonyStyle $io, bool $persist) : array
    {
        if (empty($tweets)) {
            return [];
        }

        $tweetsToSave = [];

        foreach ($tweets AS $tweet) {
            $tweetsToSave[] = Tweet::buildAndAttachToHashtag($tweet, $hashtag);
        }

        if ($persist) {
            $this->updateHashtagLastTweet($hashtag, ... $tweetsToSave);
            $this->saveTweets(... $tweetsToSave);
            $io->note(sprintf('Saved %d tweets!', count($tweetsToSave)));
        }

        return $tweetsToSave;
    }

    private function updateHashtagLastTweet(Hashtag $hashtag, Tweet ...$tweets)
    {
        $tweet = end($tweets);
        $hashtag->setLastTweet($tweet->getTweetId());
        $this->hashtagRepository->save($hashtag);
    }

    private function saveTweets(Tweet ...$tweets)
    {
        if (empty($tweets)) {
            return;
        }

        $this->tweetRepository->multipleSave(... $tweets);
    }

    private function showTweets(SymfonyStyle $io, Tweet ...$tweets) : void
    {
        if (empty($tweets)) {
            return;
        }

        foreach ($tweets as $index => $tweet) {
            $io->section(sprintf('Tweet #%d', $index + 1));
            $io->writeln(sprintf(
                '%s by @%s',
                $tweet->getContent(),
                $tweet->getUserName()
            ));
        }
    }
}
