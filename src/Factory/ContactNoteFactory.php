<?php

namespace App\Factory;

use App\Entity\ContactNote;
use App\Repository\ContactNoteRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ContactNote>
 *
 * @method static ContactNote|Proxy createOne(array $attributes = [])
 * @method static ContactNote[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ContactNote|Proxy find(object|array|mixed $criteria)
 * @method static ContactNote|Proxy findOrCreate(array $attributes)
 * @method static ContactNote|Proxy first(string $sortedField = 'id')
 * @method static ContactNote|Proxy last(string $sortedField = 'id')
 * @method static ContactNote|Proxy random(array $attributes = [])
 * @method static ContactNote|Proxy randomOrCreate(array $attributes = [])
 * @method static ContactNote[]|Proxy[] all()
 * @method static ContactNote[]|Proxy[] findBy(array $attributes)
 * @method static ContactNote[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ContactNote[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static ContactNoteRepository|RepositoryProxy repository()
 * @method ContactNote|Proxy create(array|callable $attributes = [])
 */
final class ContactNoteFactory extends ModelFactory
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
        return ContactNote::class;
    }
}
