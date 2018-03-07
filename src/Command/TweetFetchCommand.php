<?php

namespace App\Command;

use App\Service\TwitterClient;
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

    public function __construct(TwitterClient $twitterClient)
    {
        $this->twitterClient = $twitterClient;
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

        if ($input->getOption('no-persist')) {
            // ...
        }

        $tweets = $this->twitterClient->findTweetsWith($text, $include_entities, $result_type, $count);

        if ($tweets) {

            foreach ($tweets->statuses as $index => $tweet) {
                $io->section(sprintf("Tweet #%d", $index + 1));
                $io->writeln(sprintf("%s by @%s",
                    $tweet->text,
                    $tweet->user->screen_name
                ));
            }

        } else {
            $io->error('No tweets found!');
        }

        $io->success('Operation finished!');
    }


}