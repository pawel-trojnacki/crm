<?php

namespace App\Factory;

use App\Entity\Workspace;
use App\Repository\WorkspaceRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Workspace>
 *
 * @method static Workspace|Proxy createOne(array $attributes = [])
 * @method static Workspace[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Workspace|Proxy find(object|array|mixed $criteria)
 * @method static Workspace|Proxy findOrCreate(array $attributes)
 * @method static Workspace|Proxy first(string $sortedField = 'id')
 * @method static Workspace|Proxy last(string $sortedField = 'id')
 * @method static Workspace|Proxy random(array $attributes = [])
 * @method static Workspace|Proxy randomOrCreate(array $attributes = [])
 * @method static Workspace[]|Proxy[] all()
 * @method static Workspace[]|Proxy[] findBy(array $attributes)
 * @method static Workspace[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Workspace[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static WorkspaceRepository|RepositoryProxy repository()
 * @method Workspace|Proxy create(array|callable $attributes = [])
 */
final class WorkspaceFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->sentence(2),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Workspace::class;
    }
}
