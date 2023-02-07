<?php

declare(strict_types=1);

namespace Dbp\Relay\SublibraryConnectorCampusonlineBundle\Tests\Service;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Dbp\Relay\BasePersonBundle\Entity\Person;
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

    public function testGetSublibraryIdsByLibraryManager(): void
    {
        $functions = [
            'ANG:D:95300:34886',
            'F_BIB:F:1490:681',
            'F_BIB:F:1610:14128',
        ];

        $person = new Person();
        $person->setExtraData('tug-functions', $functions);

        $sublibraryIds = $this->sublibraryProvider->getSublibraryIdsByLibraryManager($person);
        $this->assertCount(2, $sublibraryIds);
        $this->assertSame('681', $sublibraryIds[0]);
        $this->assertSame('14128', $sublibraryIds[1]);
    }

    public function testGetSublibraryCodesByLibraryManager(): void
    {
        $functions = [
            'ANG:D:95300:34886',
            'F_BIB:F:1490:681',
            'F_BIB:F:1610:14128',
        ];

        $person = new Person();
        $person->setExtraData('tug-functions', $functions);

        $sublibraryCodes = $this->sublibraryProvider->getSublibraryCodesByLibraryManager($person);
        $this->assertCount(2, $sublibraryCodes);
        $this->assertSame('F1490', $sublibraryCodes[0]);
        $this->assertSame('F1610', $sublibraryCodes[1]);
    }

    public function testGetSublibrariesByPersonNoFunctions()
    {
        $person = new Person();
        $person->setExtraData('tug-functions', []);
        $result = $this->sublibraryProvider->getSublibraryIdsByLibraryManager($person);
        $this->assertSame([], $result);
        $result = $this->sublibraryProvider->getSublibraryCodesByLibraryManager($person);
        $this->assertSame([], $result);
    }

    public function testGetSublibrariesByPersonUnknownFunction()
    {
        $person = new Person();
        $person->setExtraData('tug-functions', ['nope']);
        $result = $this->sublibraryProvider->getSublibraryIdsByLibraryManager($person);
        $this->assertSame([], $result);
        $result = $this->sublibraryProvider->getSublibraryCodesByLibraryManager($person);
        $this->assertSame([], $result);
    }

    public function testGetSublibrariesByPersonOtherFunction()
    {
        $person = new Person();
        $person->setExtraData('tug-functions', ['ANG:D:4370:2322']);

        $result = $this->sublibraryProvider->getSublibraryIdsByLibraryManager($person);
        $this->assertSame([], $result);
        $result = $this->sublibraryProvider->getSublibraryCodesByLibraryManager($person);
        $this->assertSame([], $result);
    }
}
