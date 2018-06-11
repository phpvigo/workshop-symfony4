<?php

namespace App\Repository;

use App\Entity\Hashtag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Hashtag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hashtag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hashtag[]    findAll()
 * @method Hashtag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HashtagRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Hashtag::class);
    }

    public function findOneByName(string $value) : ? Hashtag
    {
        return $this->findOneBy(['name' => $value]);
    }

    public function save(Hashtag $hashtag) : void
    {
        $this->_em->persist($hashtag);
        $this->_em->flush();
    }

//    /**
//     * @return Hashtag[] Returns an array of Hashtag objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Hashtag
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
