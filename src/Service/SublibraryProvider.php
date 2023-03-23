<?php

declare(strict_types=1);

namespace Dbp\Relay\SublibraryConnectorCampusonlineBundle\Service;

use Dbp\Relay\BaseOrganizationBundle\API\OrganizationProviderInterface;
use Dbp\Relay\BasePersonBundle\Entity\Person;
use Dbp\Relay\CoreBundle\Exception\ApiError;
use Dbp\Relay\CoreBundle\LocalData\LocalData;
use Dbp\Relay\SublibraryBundle\API\SublibraryProviderInterface;
use Dbp\Relay\SublibraryBundle\Entity\Sublibrary;
use Dbp\Relay\SublibraryConnectorCampusonlineBundle\Event\SublibraryProviderPostEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SublibraryProvider implements SublibraryProviderInterface
{
    public const ORGANIZATION_CODE_ATTRIBUTE_NAME = 'code';

    /** @var OrganizationProviderInterface */
    private $organizationProvider;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(OrganizationProviderInterface $organizationProvider, EventDispatcherInterface $eventDispatcher)
    {
        $this->organizationProvider = $organizationProvider;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getSublibrary(string $identifier, array $options = []): ?Sublibrary
    {
        return $this->getSublibraryInternal($identifier, $options);
    }

    /**
     * Returns the array of sub-library IDs the given Person is a library manager of.
     *
     * @return string[]
     */
    public function getSublibraryIdsByLibraryManager(Person $person): array
    {
        return $this->getSublibrariesByLibraryManagerInternal($person, true);
    }

    /**
     * Returns the array of sub-library codes the given Person is a library manager of.
     *
     * @return string[]
     */
    public function getSublibraryCodesByLibraryManager(Person $person): array
    {
        return $this->getSublibrariesByLibraryManagerInternal($person, false);
    }

    /*
     * Returns whether the given Person is a library manager of the Sublibrary with the given ID.
     *
     * @param string $sublibraryId The Sublibrary ID
     */
    public function isLibraryManagerById(Person $person, string $sublibraryId): bool
    {
        return in_array($sublibraryId, $this->getSublibrariesByLibraryManagerInternal($person, true), true);
    }

    /*
     * Returns whether the given Person is a library manager of the Sublibrary with the given code.
     *
     * @param string $sublibraryCode The Sublibrary code
     */
    public function isLibraryManagerByCode(Person $person, string $sublibraryCode): bool
    {
        return in_array($sublibraryCode, $this->getSublibrariesByLibraryManagerInternal($person, false), true);
    }

    private function getSublibraryInternal(string $identifier, array $options): ?Sublibrary
    {
        $organization = null;
        try {
            LocalData::requestLocalDataAttributes($options, [self::ORGANIZATION_CODE_ATTRIBUTE_NAME]);
            $organization = $this->organizationProvider->getOrganizationById($identifier, $options);
        } catch (ApiError $exception) {
        }

        $sublibrary = null;
        if ($organization !== null) {
            $sublibrary = new Sublibrary();
            $sublibrary->setIdentifier($organization->getIdentifier());
            $sublibrary->setName($organization->getName());
            $sublibrary->setCode($organization->getLocalDataValue(self::ORGANIZATION_CODE_ATTRIBUTE_NAME));
        }

        $postEvent = new SublibraryProviderPostEvent($identifier, $sublibrary, $organization, $options);
        $this->eventDispatcher->dispatch($postEvent);

        return $postEvent->getSublibrary();
    }

    /**
     * Gets the list of Sublibrary IDs/codes the given person is a library manager of. This function is currently TU Graz specific.
     *
     * @param bool $getIds If true, the Sublibrary IDs are returned, the Sublibrary codes otherwise
     *
     * @return string[]
     */
    private function getSublibrariesByLibraryManagerInternal(Person $person, bool $getIds): array
    {
        $sublibraries = [];
        $regex = "/^F_BIB:F:(\d+):([\d_]+)$/i";
        $functions = $person->getExtraData('tug-functions');

        if ($functions !== null) {
            foreach ($functions as $function) {
                if (preg_match($regex, $function, $matches)) {
                    $sublibrary = $getIds ? $matches[2] : 'F'.$matches[1];
                    if (!$getIds) {
                        if (strlen($sublibrary) === 6) {
                            $sublibrary = substr($sublibrary, 0, -1);
                        }
                    }
                    if (!in_array($sublibrary, $sublibraries, true)) {
                        $sublibraries[] = $sublibrary;
                    }
                }
            }
        }

        return $sublibraries;
    }
}
