<?php

declare(strict_types=1);

namespace Dbp\Relay\SublibraryConnectorCampusonlineBundle\Service;

use DBP\API\AlmaBundle\Entity\Sublibrary;
use Dbp\Relay\BaseOrganizationBundle\API\OrganizationProviderInterface;
use Dbp\Relay\BasePersonBundle\Entity\Person;

class SublibraryProvider implements SublibraryProviderInterface
{
    /** @var OrganizationProviderInterface */
    private $organizationProvider;

    public function __construct(OrganizationProviderInterface $organizationProvider)
    {
        $this->organizationProvider = $organizationProvider;
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
            $organization = $this->organizationProvider->getOrganizationById($orgUnitId, $options);
            if ($organization !== null) {
                $sublibrary = new Sublibrary();
                $sublibrary->setIdentifier($identifier);
                $sublibrary->setName($organization->getName());
                $sublibrary->setCode($sublibraryCode);
            }
        }

        return $sublibrary;
    }

    /**
     * Gets the list of sublibrary IDs the given person has access to. This function is currently TU Graz specific
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
    private static function parseSublibraryIdentifier(string $sublibraryIdentifier, string& $orgUnitId, string& $sublibraryCode): bool
    {
        $regex = "/^(\d+)-F([\d_]+)$/i";

        if (preg_match($regex, $sublibraryIdentifier, $matches)) {
            $orgUnitId = $matches[2];
            $sublibraryCode = $matches[1];

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
