<?php

declare(strict_types=1);

namespace Dbp\Relay\SublibraryConnectorCampusonlineBundle\Service;

use Dbp\Relay\BaseOrganizationBundle\API\OrganizationProviderInterface;
use Dbp\Relay\CoreBundle\Exception\ApiError;
use Dbp\Relay\CoreBundle\Rest\Options;
use Dbp\Relay\SublibraryBundle\API\SublibraryInterface;
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

    public function getSublibrary(string $identifier, array $options = []): ?SublibraryInterface
    {
        $organization = null;
        try {
            Options::requestLocalDataAttributes($options, [self::ORGANIZATION_CODE_ATTRIBUTE_NAME]);
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
}
