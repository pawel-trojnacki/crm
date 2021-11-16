<?php

namespace App\Factory;

use App\Entity\Industry;
use App\Repository\IndustryRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Industry>
 *
 * @method static Industry|Proxy createOne(array $attributes = [])
 * @method static Industry[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Industry|Proxy find(object|array|mixed $criteria)
 * @method static Industry|Proxy findOrCreate(array $attributes)
 * @method static Industry|Proxy first(string $sortedField = 'id')
 * @method static Industry|Proxy last(string $sortedField = 'id')
 * @method static Industry|Proxy random(array $attributes = [])
 * @method static Industry|Proxy randomOrCreate(array $attributes = [])
 * @method static Industry[]|Proxy[] all()
 * @method static Industry[]|Proxy[] findBy(array $attributes)
 * @method static Industry[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Industry[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static IndustryRepository|RepositoryProxy repository()
 * @method Industry|Proxy create(array|callable $attributes = [])
 */
final class IndustryFactory extends ModelFactory
{
    public const industries = ['transport', 'IT', 'education', 'construction', 'food'];

    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->word()
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Industry::class;
    }
}
