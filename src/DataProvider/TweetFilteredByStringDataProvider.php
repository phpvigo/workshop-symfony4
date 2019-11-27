<?php

namespace App\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use ApiPlatform\Core\Exception\RuntimeException;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\Tweet;

final class TweetFilteredByStringDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $requestStack;
    private $managerRegistry;
    private $collectionExtensions;

    /**
     * TweetFilteredByStringDataProvider constructor.
     * @param ManagerRegistry $managerRegistry
     * @param iterable $collectionExtensions
     * @param RequestStack $requestStack
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        iterable $collectionExtensions = [],
        RequestStack $requestStack
    ) {
        $this->managerRegistry = $managerRegistry;
        $this->collectionExtensions = $collectionExtensions;
        $this->requestStack = $requestStack;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Tweet::class && $operationName === 'get-filtered-by-string';
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $searchString = $this->requestStack->getCurrentRequest()->get('search');

        $manager = $this->managerRegistry->getManagerForClass($resourceClass);

        $repository = $manager->getRepository($resourceClass);
        if (!method_exists($repository, 'createQueryBuilder')) {
            throw new RuntimeException('The repository class must have a "createQueryBuilder" method.');
        }

        $queryBuilder = $repository->createQueryBuilder('t')
            ->join('t.hashtag', 'h')
            ->where('t.userName LIKE :searchString')
            ->orWhere('t.content LIKE :searchString')
            ->orWhere('h.name LIKE :searchString')
            ->setParameter('searchString', '%'.$searchString.'%');

        $queryNameGenerator = new QueryNameGenerator();

        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operationName, $context);

            if ($extension instanceof QueryResultCollectionExtensionInterface && $extension->supportsResult(
                $resourceClass,
                $operationName
                )) {
                return $extension->getResult($queryBuilder, $resourceClass, $operationName, $context);
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
