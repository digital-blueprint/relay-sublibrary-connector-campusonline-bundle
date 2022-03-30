<?php

declare(strict_types=1);

namespace Dbp\Relay\SublibraryConnectorCampusonlineBundle\Service;

use Dbp\Relay\BaseOrganizationConnectorCampusonlineBundle\Service\OrganizationApi;
use Dbp\Relay\BasePersonBundle\Entity\Person;
use Dbp\Relay\SublibraryBundle\API\SublibraryProviderInterface;
use Dbp\Relay\SublibraryBundle\Entity\Sublibrary;
use Dbp\Relay\SublibraryConnectorCampusonlineBundle\Event\SublibraryProviderPostEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SublibraryProvider implements SublibraryProviderInterface
{
    /** @var OrganizationApi */
    private $organizationApi;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(OrganizationApi $organizationApi, EventDispatcherInterface $eventDispatcher)
    {
        $this->organizationApi = $organizationApi;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getSublibrary(string $identifier, array $options = []): ?Sublibrary
    {
        return $this->getSublibraryInternal($identifier, $options);
    }

    /**
     * Gets the list of sublibraries the given person has access to.
     *
     * @return Sublibrary[]
     */
    public function getSublibrariesByPerson(Person $person, array $options = []): array
    {
        $sublibraries = [];

        foreach ($this->getSublibraryIdsByPerson($person) as $sublibraryId) {
            try {
                $sublibrary = $this->getSublibraryInternal($sublibraryId, $options);
                if ($sublibrary !== null) {
                    $sublibraries[] = $sublibrary;
                }
            } catch (\Exception $e) {
            }
        }

        return $sublibraries;
    }

    public function hasPersonSublibraryPermissions(Person $person, string $sublibraryIdentifier)
    {
        return in_array($sublibraryIdentifier, $this->getSublibraryIdsByPerson($person), true);
    }

    private function getSublibraryInternal(string $identifier, array $options): ?Sublibrary
    {
        $sublibrary = null;
        if (self::parseSublibraryIdentifier($identifier, $orgUnitId, $sublibraryCode)) {
            $organizationUnitData = $this->organizationApi->getOrganizationById($orgUnitId, $options);
            if ($organizationUnitData !== null) {
                $sublibrary = new Sublibrary();
                $sublibrary->setIdentifier($organizationUnitData->getIdentifier());
                $sublibrary->setName($organizationUnitData->getName());
                $sublibrary->setCode($organizationUnitData->getCode());
            }
            $postEvent = new SublibraryProviderPostEvent($orgUnitId, $sublibrary, $organizationUnitData, $options);
            $this->eventDispatcher->dispatch($postEvent, SublibraryProviderPostEvent::NAME);
            $sublibrary = $postEvent->getSublibrary();
        }

        return $sublibrary;
    }

    /**
     * Gets the list of sublibrary IDs the given person has access to. This function is currently TU Graz specific.
     *
     * @return string[]
     */
    private function getSublibraryIdsByPerson(Person $person): array
    {
        $sublibraryIds = [];
        $regex = "/^F_BIB:F:(\d+):([\d_]+)$/i";
        $functions = $person->getExtraData('tug-functions');

        if ($functions !== null) {
            foreach ($functions as $function) {
                if (preg_match($regex, $function, $matches)) {
                    $sublibraryId = self::createSublibraryIdentifier($matches[2], $matches[1]);
                    if (!in_array($sublibraryId, $sublibraryIds, true)) {
                        $sublibraryIds[] = $sublibraryId;
                    }
                }
            }
        }

        return $sublibraryIds;
    }

    /**
     * extracts the org unit ID and the sublibrary code from the sublibrary ID.
     */
    private static function parseSublibraryIdentifier(string $sublibraryIdentifier, string &$orgUnitId = null, string &$sublibraryCode = null): bool
    {
        $regex = "/^(\d+)-F([\d_]+)$/i";

        if (preg_match($regex, $sublibraryIdentifier, $matches)) {
            $orgUnitId = $matches[1];
            $sublibraryCode = $matches[2];

            return true;
        }

        return false;
    }

    /**
     * creates the sublibrary ID from the org unit ID and the sublibrary code.
     */
    private static function createSublibraryIdentifier(string $orgUnitId, string $sublibraryCode): string
    {
        return $orgUnitId.'-F'.$sublibraryCode;
    }
}
