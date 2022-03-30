<?php

declare(strict_types=1);

namespace Dbp\Relay\SublibraryConnectorCampusonlineBundle\Event;

use Dbp\CampusonlineApi\LegacyWebService\Organization\OrganizationUnitData;
use Dbp\Relay\SublibraryBundle\Entity\Sublibrary;
use Symfony\Contracts\EventDispatcher\Event;

class SublibraryProviderPostEvent extends Event
{
    public const NAME = 'dbp.relay_sublibrary_connector_campusonline.sublibrary_provider.post';

    /** @var string */
    private $organizationUnitId;

    /** @var Sublibrary|null */
    private $sublibrary;

    /** @var OrganizationUnitData|null */
    private $organizationUnitData;

    /** @var array */
    private $options;

    public function __construct(string $organizationUnitId, ?Sublibrary $sublibrary,
                                ?OrganizationUnitData $organizationUnitData, array $options = [])
    {
        $this->organizationUnitId = $organizationUnitId;
        $this->organizationUnitData = $organizationUnitData;
        $this->options = $options;
        $this->sublibrary = $sublibrary;
    }

    public function getOrganizationUnitDataId(): string
    {
        return $this->organizationUnitId;
    }

    public function getOrganizationUnitData(): ?OrganizationUnitData
    {
        return $this->organizationUnitData;
    }

    public function setSublibrary(Sublibrary $sublibrary)
    {
        $this->sublibrary = $sublibrary;
    }

    public function getSublibrary(): ?Sublibrary
    {
        return $this->sublibrary;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
