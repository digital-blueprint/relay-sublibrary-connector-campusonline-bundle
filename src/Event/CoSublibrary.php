<?php

declare(strict_types=1);

namespace Dbp\Relay\SublibraryConnectorCampusonlineBundle\Event;

use Dbp\Relay\SublibraryBundle\API\SublibraryInterface;

class CoSublibrary implements SublibraryInterface
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $code;

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function setCode(string $identifier): void
    {
        $this->code = $identifier;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
