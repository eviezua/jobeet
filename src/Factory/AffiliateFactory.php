<?php

namespace App\Factory;

use App\Entity\Affiliate;
use App\Repository\AffiliateRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Affiliate>
 *
 * @method        Affiliate|Proxy                     create(array|callable $attributes = [])
 * @method static Affiliate|Proxy                     createOne(array $attributes = [])
 * @method static Affiliate|Proxy                     find(object|array|mixed $criteria)
 * @method static Affiliate|Proxy                     findOrCreate(array $attributes)
 * @method static Affiliate|Proxy                     first(string $sortedField = 'id')
 * @method static Affiliate|Proxy                     last(string $sortedField = 'id')
 * @method static Affiliate|Proxy                     random(array $attributes = [])
 * @method static Affiliate|Proxy                     randomOrCreate(array $attributes = [])
 * @method static AffiliateRepository|RepositoryProxy repository()
 * @method static Affiliate[]|Proxy[]                 all()
 * @method static Affiliate[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Affiliate[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Affiliate[]|Proxy[]                 findBy(array $attributes)
 * @method static Affiliate[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Affiliate[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class AffiliateFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'active' => self::faker()->boolean(),
            'email' => self::faker()->text(255),
            'token' => self::faker()->text(255),
            'url' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Affiliate $affiliate): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Affiliate::class;
    }
}
