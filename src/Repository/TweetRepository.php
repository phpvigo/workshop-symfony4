<?php

namespace App\Repository;

use App\Entity\Hashtag;
use App\Entity\Tweet;
use App\Entity\TweetCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Tweet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tweet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tweet[]    findAll()
 * @method Tweet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TweetRepository extends ServiceEntityRepository implements \App\Entity\TweetRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tweet::class);
    }

    public function getRandomTweet(?Hashtag $hashtag) : ?Tweet
    {
        $tweets = $this->findBy(['hashtag' => $hashtag]);
        shuffle($tweets);
        return $tweets[0];
    }

    public function save(Tweet $tweet, bool $flush = true) : void
    {
        $this->_em->persist($tweet);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function multipleSave(Tweet ...$tweets) : void
    {
        foreach ($tweets as $tweet) {
            $this->save($tweet, false);
        }
        $this->_em->flush();
    }

    public function allByHashtag(Hashtag $hashtag): TweetCollection
    {
        $tweets = $this->findBy(['hashtag' => $hashtag]);

        return new TweetCollection(... $tweets);
    }
}
