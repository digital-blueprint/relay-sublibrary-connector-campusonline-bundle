<?php

declare(strict_types=1);

namespace Dbp\Relay\SublibraryConnectorCampusonlineBundle\Event;

use Dbp\Relay\BaseOrganizationBundle\Entity\Organization;
use Symfony\Contracts\EventDispatcher\Event;

class SublibraryProviderPostEvent extends Event
{
    /** @var string */
    private $organizationId;

    /** @var CoSublibrary|null */
    private $sublibrary;

    /** @var Organization|null */
    private $organization;

    /** @var array */
    private $options;

    public function __construct(string $organizationId, ?CoSublibrary $sublibrary,
        ?Organization $organization, array $options = [])
    {
        $this->organizationId = $organizationId;
        $this->organization = $organization;
        $this->options = $options;
        $this->sublibrary = $sublibrary;
    }

    public function getOrganizationId(): string
    {
        return $this->organizationId;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setSublibrary(CoSublibrary $sublibrary)
    {
        $this->sublibrary = $sublibrary;
    }

    public function getSublibrary(): ?CoSublibrary
    {
        return $this->sublibrary;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
