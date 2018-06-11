<?php

namespace App\Command;

use App\Entity\Hashtag;
use App\Entity\Tweet;
use App\Repository\HashtagRepository;
use App\Service\TwitterClient;
use Doctrine\ORM\EntityManagerInterface;
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
    private $entityManager;
    private $hashtagRepository;

    public function __construct(TwitterClient $twitterClient, EntityManagerInterface $entityManager, HashtagRepository $hashtagRepository)
    {
        $this->twitterClient = $twitterClient;
        $this->entityManager = $entityManager;
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

        $text = $input->getArgument('text');
        $result_type = $input->getArgument('result_type');
        $count = $input->getArgument('count');
        $persist = !$input->getOption('no-persist');

        $include_entities = $input->getOption('include-entities');

        $io->title(sprintf('Searching tweets for: %s', $text));

        $hashtag = $this->obtainHashtagFromText($text, $persist);
        $tweetSearch = $this->twitterClient->findTweetsWith($hashtag, $include_entities, $result_type, $count);
        $this->buildAndShowTweets($tweetSearch->statuses, $hashtag, $io, $persist);

        $io->success('Operation finished!');
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
        $lastIndex = count($tweets) - 1;
        $hashtag->setLastTweet($tweets[$lastIndex]->getTweetId());
        $this->hashtagRepository->save($hashtag);
    }

    private function saveTweets(Tweet ...$tweets)
    {
        if (empty($tweets))
        {
            return;
        }

        foreach ($tweets as $tweet) {
            $this->entityManager->persist($tweet);
        }

        $this->entityManager->flush();
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
