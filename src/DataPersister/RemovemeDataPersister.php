<?php

declare(strict_types=1);

namespace Dbp\Relay\SublibraryConnectorCampusonlineBundle\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use Dbp\Relay\SublibraryConnectorCampusonlineBundle\Entity\Removeme;
use Dbp\Relay\SublibraryConnectorCampusonlineBundle\Service\RemovemeProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RemovemeDataPersister extends AbstractController implements DataPersisterInterface
{
    private $api;

    public function __construct(RemovemeProviderInterface $api)
    {
        $this->api = $api;
    }

    public function supports($data): bool
    {
        return $data instanceof Removeme;
    }

    public function persist($data): void
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // TODO
    }

    public function remove($data)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // TODO
    }
}
