<?php
namespace App\ApiPlatform;
use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Job;
use Doctrine\ORM\QueryBuilder;
class JobsWithActiveAffiliatesExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }
    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }
    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass)
    {
        if (Job::class !== $resourceClass) {
            return;
        }
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->join(sprintf('%s.category', $rootAlias), 'c')
            ->join('c.affilities', 'a')
            ->andWhere('a.active = :active')
            ->setParameter('active', true)
            ->andWhere(sprintf('%s.activated = :activated', $rootAlias))
            ->setParameter('activated', true)
            ->andWhere(sprintf('%s.public = :public', $rootAlias))
            ->setParameter('public', true)
        ;
    }
}
