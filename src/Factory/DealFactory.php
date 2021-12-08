<?php

namespace App\Factory;

use App\Entity\Deal;
use App\Repository\DealRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Deal>
 *
 * @method static Deal|Proxy createOne(array $attributes = [])
 * @method static Deal[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Deal|Proxy find(object|array|mixed $criteria)
 * @method static Deal|Proxy findOrCreate(array $attributes)
 * @method static Deal|Proxy first(string $sortedField = 'id')
 * @method static Deal|Proxy last(string $sortedField = 'id')
 * @method static Deal|Proxy random(array $attributes = [])
 * @method static Deal|Proxy randomOrCreate(array $attributes = [])
 * @method static Deal[]|Proxy[] all()
 * @method static Deal[]|Proxy[] findBy(array $attributes)
 * @method static Deal[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Deal[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static DealRepository|RepositoryProxy repository()
 * @method Deal|Proxy create(array|callable $attributes = [])
 */
final class DealFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->sentence(5),
            'stage' => self::faker()->randomElement(Deal::STAGES),
            'description' => self::faker()->text(),
            'createdAt' => self::faker()->dateTimeBetween('-1 year', '-2 weeks'),
            'updatedAt' => self::faker()->dateTimeBetween('-2 weeks', 'now'),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Deal::class;
    }
}
