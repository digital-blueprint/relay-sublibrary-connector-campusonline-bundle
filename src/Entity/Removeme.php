<?php

declare(strict_types=1);

namespace Dbp\Relay\SublibraryConnectorCampusonlineBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Dbp\Relay\SublibraryConnectorCampusonlineBundle\Controller\LoggedInOnly;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={
 *         "get" = {
 *             "path" = "/sublibrary-connector-campusonline/removemes",
 *             "openapi_context" = {
 *                 "tags" = {"Sublibrary Connector for the Campusonline API"},
 *             },
 *         }
 *     },
 *     itemOperations={
 *         "get" = {
 *             "path" = "/sublibrary-connector-campusonline/removemes/{identifier}",
 *             "openapi_context" = {
 *                 "tags" = {"Sublibrary Connector for the Campusonline API"},
 *             },
 *         },
 *         "put" = {
 *             "path" = "/sublibrary-connector-campusonline/removemes/{identifier}",
 *             "openapi_context" = {
 *                 "tags" = {"Sublibrary Connector for the Campusonline API"},
 *             },
 *         },
 *         "delete" = {
 *             "path" = "/sublibrary-connector-campusonline/removemes/{identifier}",
 *             "openapi_context" = {
 *                 "tags" = {"Sublibrary Connector for the Campusonline API"},
 *             },
 *         },
 *         "loggedin_only" = {
 *             "security" = "is_granted('IS_AUTHENTICATED_FULLY')",
 *             "method" = "GET",
 *             "path" = "/sublibrary-connector-campusonline/removemes/{identifier}/loggedin-only",
 *             "controller" = LoggedInOnly::class,
 *             "openapi_context" = {
 *                 "summary" = "Only works when logged in.",
 *                 "tags" = {"Sublibrary Connector for the Campusonline API"},
 *             },
 *         }
 *     },
 *     iri="https://schema.org/Removeme",
 *     shortName="SublibraryConnectorCampusonlineRemoveme",
 *     normalizationContext={
 *         "groups" = {"SublibraryConnectorCampusonlineRemoveme:output"},
 *         "jsonld_embed_context" = true
 *     },
 *     denormalizationContext={
 *         "groups" = {"SublibraryConnectorCampusonlineRemoveme:input"},
 *         "jsonld_embed_context" = true
 *     }
 * )
 */
class Removeme
{
    /**
     * @ApiProperty(identifier=true)
     */
    private $identifier;

    /**
     * @ApiProperty(iri="https://schema.org/name")
     * @Groups({"SublibraryConnectorCampusonlineRemoveme:output", "SublibraryConnectorCampusonlineRemoveme:input"})
     *
     * @var string
     */
    private $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }
}
