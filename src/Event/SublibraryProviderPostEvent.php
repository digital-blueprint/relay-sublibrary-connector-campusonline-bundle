<?php

declare(strict_types=1);

namespace Dbp\Relay\SublibraryConnectorCampusonlineBundle\Event;

use Dbp\Relay\BaseOrganizationBundle\Entity\Organization;
use Dbp\Relay\SublibraryBundle\Entity\Sublibrary;
use Symfony\Contracts\EventDispatcher\Event;

class SublibraryProviderPostEvent extends Event
{
    /** @var string */
    private $organizationId;

    /** @var Sublibrary|null */
    private $sublibrary;

    /** @var Organization|null */
    private $organization;

    /** @var array */
    private $options;

    public function __construct(string $organizationId, ?Sublibrary $sublibrary,
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
