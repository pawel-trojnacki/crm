<?php

namespace App\Factory;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Contact>
 *
 * @method static Contact|Proxy createOne(array $attributes = [])
 * @method static Contact[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Contact|Proxy find(object|array|mixed $criteria)
 * @method static Contact|Proxy findOrCreate(array $attributes)
 * @method static Contact|Proxy first(string $sortedField = 'id')
 * @method static Contact|Proxy last(string $sortedField = 'id')
 * @method static Contact|Proxy random(array $attributes = [])
 * @method static Contact|Proxy randomOrCreate(array $attributes = [])
 * @method static Contact[]|Proxy[] all()
 * @method static Contact[]|Proxy[] findBy(array $attributes)
 * @method static Contact[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Contact[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static ContactRepository|RepositoryProxy repository()
 * @method Contact|Proxy create(array|callable $attributes = [])
 */
final class ContactFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        $positions = ['manager', 'ceo', null];

        return [
            'firstName' => self::faker()->firstName(),
            'lastName' => self::faker()->lastName(),
            'email' => self::faker()->email(),
            'phone' => self::faker()->phoneNumber(),
            'position' => self::faker()->randomElement($positions),
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
        return Contact::class;
    }
}
