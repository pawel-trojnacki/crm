<?php

namespace App\Factory;

use App\Entity\DealNote;
use App\Repository\DealNoteRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<DealNote>
 *
 * @method static DealNote|Proxy createOne(array $attributes = [])
 * @method static DealNote[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static DealNote|Proxy find(object|array|mixed $criteria)
 * @method static DealNote|Proxy findOrCreate(array $attributes)
 * @method static DealNote|Proxy first(string $sortedField = 'id')
 * @method static DealNote|Proxy last(string $sortedField = 'id')
 * @method static DealNote|Proxy random(array $attributes = [])
 * @method static DealNote|Proxy randomOrCreate(array $attributes = [])
 * @method static DealNote[]|Proxy[] all()
 * @method static DealNote[]|Proxy[] findBy(array $attributes)
 * @method static DealNote[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static DealNote[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static DealNoteRepository|RepositoryProxy repository()
 * @method DealNote|Proxy create(array|callable $attributes = [])
 */
final class DealNoteFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'content' => self::faker()->text(500),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return DealNote::class;
    }
}
