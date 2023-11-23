<?php

declare(strict_types=1);

namespace Dbp\Relay\SublibraryConnectorCampusonlineBundle\Tests\Service;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Dbp\Relay\SublibraryConnectorCampusonlineBundle\Service\SublibraryProvider;
use Symfony\Component\EventDispatcher\EventDispatcher;

class SublibraryProviderTest extends ApiTestCase
{
    /** @var SublibraryProvider */
    private $sublibraryProvider;

    protected function setUp(): void
    {
        $eventDispatcher = new EventDispatcher();
        $this->sublibraryProvider = new SublibraryProvider(new TestOrganizationProvider(), $eventDispatcher);
    }

    public function testGetSublibrary(): void
    {
        $sublibraryId = '123';
        $sublibrary = $this->sublibraryProvider->getSublibrary($sublibraryId);
        $this->assertSame($sublibraryId, $sublibrary->getIdentifier());
        $this->assertSame(TestOrganizationProvider::toOrganizationName($sublibraryId), $sublibrary->getName());
        $this->assertSame(TestOrganizationProvider::toOrganizationCode($sublibraryId), $sublibrary->getCode());
    }
}
