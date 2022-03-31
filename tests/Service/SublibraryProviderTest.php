<?php

declare(strict_types=1);

namespace Dbp\Relay\SublibraryConnectorCampusonlineBundle\Tests\Service;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Dbp\Relay\BaseOrganizationConnectorCampusonlineBundle\Service\OrganizationApi;
use Dbp\Relay\SublibraryConnectorCampusonlineBundle\Service\SublibraryProvider;
use Symfony\Component\EventDispatcher\EventDispatcher;

class SublibraryProviderTest extends ApiTestCase
{
    /** @var SublibraryProvider */
    private $api;

    protected function setUp(): void
    {
        $organizationApi = new OrganizationApi();
        $eventDispatcher = new EventDispatcher();
        $this->api = new SublibraryProvider($organizationApi, $eventDispatcher);
    }

    public function test(): void
    {
        self::assertNotNull($this->api);
    }
}
