<?php

namespace App\Command;

use App\Entity\Tweet;
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

    public function __construct(TwitterClient $twitterClient, EntityManagerInterface $entityManager)
    {
        $this->twitterClient = $twitterClient;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Fetch and persist tweets')
            ->addArgument('text', InputArgument::REQUIRED, 'Text to search')
            ->addArgument('count', InputArgument::OPTIONAL, 'Max. number of tweets to fetch', 4)
            ->addArgument('result_type', InputArgument::OPTIONAL,
                <<<'EOD'
Specifies what type of search results you would prefer to receive. The current default is "mixed." Valid values include:

- mixed : Include both popular and real time results in the response.

- recent : return only the most recent results in the response

- popular : return only the most popular results in the response.

EOD
                ,
                'recent')
            ->addOption('include-entities', 'i', InputOption::VALUE_NONE, 'Include entities in twitter fetch')
            ->addOption('no-persist', null, InputOption::VALUE_NONE, 'Disable tweet persistence');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $text = $input->getArgument('text');
        $result_type = $input->getArgument('result_type');
        $count = $input->getArgument('count');

        $include_entities = $input->getOption('include-entities');

        $io->title(sprintf('Searching tweets for: %s', $text));

        $tweets = $this->twitterClient->findTweetsWith($text, $include_entities, $result_type, $count);

        if ($tweets) {

            foreach ($tweets->statuses as $index => $tweet) {
                $io->section(sprintf("Tweet #%d", $index + 1));
                $io->writeln(sprintf("%s by @%s",
                    $tweet->text,
                    $tweet->user->screen_name
                ));
            }

            if (!$input->getOption('no-persist')) {
                $this->saveTweets($tweets);

                $io->note(sprintf("Saved %d tweets!", count($tweets->statuses)));
            }

        } else {
            $io->error('No tweets found!');
        }

        $io->success('Operation finished!');
    }

    private function saveTweets($tweets)
    {
        foreach ($tweets->statuses as $tweet) {

            $aTweet = new Tweet();

            $aTweet
                ->setTweetId($tweet->id)
                ->setContent($tweet->text)
                ->setUserName($tweet->user->name)
                ->setUserImage($tweet->user->profile_image_url)
                ->setCreatedAt(new \DateTime($tweet->created_at));

            $this->entityManager->persist($aTweet);

        }

        $this->entityManager->flush();
    }

}