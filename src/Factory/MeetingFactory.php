<?php

namespace App\Factory;

use App\Entity\Meeting;
use App\Repository\MeetingRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Meeting>
 *
 * @method static Meeting|Proxy createOne(array $attributes = [])
 * @method static Meeting[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Meeting|Proxy find(object|array|mixed $criteria)
 * @method static Meeting|Proxy findOrCreate(array $attributes)
 * @method static Meeting|Proxy first(string $sortedField = 'id')
 * @method static Meeting|Proxy last(string $sortedField = 'id')
 * @method static Meeting|Proxy random(array $attributes = [])
 * @method static Meeting|Proxy randomOrCreate(array $attributes = [])
 * @method static Meeting[]|Proxy[] all()
 * @method static Meeting[]|Proxy[] findBy(array $attributes)
 * @method static Meeting[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Meeting[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static MeetingRepository|RepositoryProxy repository()
 * @method Meeting|Proxy create(array|callable $attributes = [])
 */
final class MeetingFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->sentence(3),
            'importance' => self::faker()->randomElement([
                Meeting::IMORTANCE_HIGH,
                Meeting::IMPORTANCE_LOW,
                Meeting::IMPORTANCE_NORMAL,
            ]),
            'beginAt' => self::faker()->dateTimeBetween('now', '+3 months'),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Meeting::class;
    }
}
