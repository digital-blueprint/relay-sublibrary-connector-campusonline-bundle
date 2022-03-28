<?php

declare(strict_types=1);

namespace Dbp\Relay\SublibraryConnectorCampusonlineBundle\Service;

use Dbp\Relay\SublibraryConnectorCampusonlineBundle\Entity\Removeme;

interface RemovemeProviderInterface
{
    public function getRemovemeById(string $identifier): ?Removeme;

    public function getRemovemes(): array;
}
