<?php

namespace App\Tests;

use App\Entity\Industry;
use App\Repository\IndustryRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class IndustryTest extends KernelTestCase
{
    private IndustryRepository $industryRepository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        DatabasePrimer::prime($kernel);

        $this->industryRepository = self::getContainer()->get(IndustryRepository::class);
    }

    public function testIndustryIsCreatedInDatabse(): void
    {
        $industry = new Industry('transport');

        $this->industryRepository->save($industry);

        $savedIndustry = $this->industryRepository->findOneBy([
            'name' => 'transport',
        ]);

        $this->assertInstanceOf(Industry::class, $savedIndustry);
        $this->assertIsInt($savedIndustry->getId());
        $this->assertSame('transport', $savedIndustry->getName());
    }

    public function testIndustryIsRemovedFromDatabase(): void
    {
        $industry = new Industry('transport');

        $this->industryRepository->save($industry);

        $this->assertInstanceOf(
            Industry::class,
            $this->industryRepository->findOneBy(
                [
                    'name' => 'transport',
                ]
            )
        );

        $this->industryRepository->delete($industry);

        $this->assertNull(
            $this->industryRepository->findOneBy(
                [
                    'name' => 'transport',
                ]
            )
        );
    }
}
