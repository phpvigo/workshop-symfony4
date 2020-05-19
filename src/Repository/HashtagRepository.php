<?php

namespace App\Repository;

use App\Entity\Hashtag;
use App\Entity\NotFoundHashtagException;
use App\ValueObject\HashtagCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;


/**
 * @method Hashtag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hashtag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hashtag[]    findAll()
 * @method Hashtag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HashtagRepository extends ServiceEntityRepository implements \App\Entity\HashtagRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hashtag::class);
    }

    public function findOneByName(string $value) : ? Hashtag
    {
        return $this->findOneBy(['name' => $value]);
    }

    public function save(Hashtag $hashtag, bool $flush = true) : void
    {
        $this->_em->persist($hashtag);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function loadAllAndReturn() : HashtagCollection
    {
        return new HashtagCollection(... $this->findAll());
    }

    public function bySlugOrFail(string $slug): Hashtag
    {
        $hashtag = $this->find($slug);

        if ($hashtag === null) {
            throw new NotFoundHashtagException($slug);
        }

        return $hashtag;
    }
}
