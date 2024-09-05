<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\UI\Config;

use Ibexa\AdminUi\UI\Service\ContentTypeIconResolver;
use Ibexa\Contracts\AdminUi\UI\Config\ProviderInterface;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CorporateAccountContentTypes implements ProviderInterface
{
    private ContentTypeService $contentTypeService;

    private ProviderInterface $innerProvider;

    private ContentTypeIconResolver $contentTypeIconResolver;

    private UrlGeneratorInterface $urlGenerator;

    private CorporateAccountConfiguration $corporateAccountConfiguration;

    public function __construct(
        ProviderInterface $innerProvider,
        ContentTypeService $contentTypeService,
        ContentTypeIconResolver $contentTypeIconResolver,
        UrlGeneratorInterface $urlGenerator,
        CorporateAccountConfiguration $corporateAccountConfiguration
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->innerProvider = $innerProvider;
        $this->contentTypeIconResolver = $contentTypeIconResolver;
        $this->urlGenerator = $urlGenerator;
        $this->corporateAccountConfiguration = $corporateAccountConfiguration;
    }

    /**
     * @return array<string,array{
     *     id: int,
     *     identifier: string,
     *     name: string,
     *     isContainer: bool,
     *     thumbnail: string,
     *     href: string,
     *     isHidden: bool
     * }>
     */
    public function getConfig(): array
    {
        $config = $this->innerProvider->getConfig();

        $groupIdentifier = $this->corporateAccountConfiguration->getContentTypeGroupIdentifier();
        $corporateAccountContentGroup =
            $this->contentTypeService->loadContentTypeGroupByIdentifier($groupIdentifier);

        $contentTypes = $this->contentTypeService->loadContentTypes($corporateAccountContentGroup);

        /** @var \Ibexa\Core\Repository\Values\ContentType\ContentType $contentType */
        foreach ($contentTypes as $contentType) {
            $config['corporate_account'][] = [
                'id' => $contentType->id,
                'identifier' => $contentType->identifier,
                'name' => $contentType->getName(),
                'isContainer' => $contentType->isContainer,
                'thumbnail' => $this->contentTypeIconResolver->getContentTypeIcon($contentType->identifier),
                'href' => $this->urlGenerator->generate(
                    'ibexa.rest.load_content_type',
                    [
                        'contentTypeId' => $contentType->id,
                    ]
                ),
                'isHidden' => true,
            ];
        }

        return $config;
    }
}
