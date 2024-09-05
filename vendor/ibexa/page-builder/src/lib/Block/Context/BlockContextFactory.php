<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Block\Context;

use Exception;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface;
use Ibexa\FieldTypePage\FieldType\Page\Block\Context\ContentViewBlockContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlockContextFactory
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /**
     * @param \Ibexa\Contracts\Core\Repository\LocationService $locationService
     * @param \Ibexa\Contracts\Core\Repository\ContentService $contentService
     * @param \Ibexa\Contracts\Core\Repository\ContentTypeService $contentTypeService
     */
    public function __construct(
        LocationService $locationService,
        ContentService $contentService,
        ContentTypeService $contentTypeService
    ) {
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @param string $intent
     * @param array $parameters
     *
     * @return \Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \LogicException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function build(string $intent, array $parameters): BlockContextInterface
    {
        if (ContentEditBlockContext::INTENT === $intent) {
            return $this->createContentEditBlockContext($parameters);
        }

        if (ContentCreateBlockContext::INTENT === $intent) {
            return $this->createContentCreateBlockContext($parameters);
        }

        if (ContentViewBlockContext::INTENT === $intent) {
            return $this->createContentViewBlockContext($parameters);
        }

        $translationIntents = [
            ContentTranslateBlockContext::INTENT,
            ContentTranslateBlockContext::INTENT_WITHOUT_BASE_LANGUAGE,
        ];
        if (in_array($intent, $translationIntents, true)) {
            return $this->createContentTranslateBlockContext($parameters);
        }

        throw new InvalidArgumentException('intent', 'Missing or invalid intent passed.');
    }

    /**
     * @param array $parameters
     *
     * @return \Ibexa\PageBuilder\Block\Context\ContentEditBlockContext
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     * @throws \LogicException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function createContentEditBlockContext(array $parameters): ContentEditBlockContext
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setRequired(['languageCode', 'versionNo', 'contentId', 'page']);
        $optionsResolver->setDefined(['locationId']);
        try {
            $optionsResolver->resolve($parameters);
        } catch (Exception $e) {
            throw new InvalidArgumentException(
                '$parameters',
                sprintf('Invalid parameters provided. Make sure the $parameters array contains: %s', $optionsResolver->getRequiredOptions())
            );
        }

        $languageCode = $parameters['languageCode'];
        $versionNo = (int)$parameters['versionNo'];
        $locationId = (int)$parameters['locationId'];
        $contentId = (int)$parameters['contentId'];

        $location = !empty($locationId)
            ? $this->locationService->loadLocation($locationId, Language::ALL)
            : null;

        $content = $this->contentService->loadContent(
            $contentId,
            [$languageCode],
            $versionNo
        );

        return new ContentEditBlockContext(
            $location,
            $content,
            $versionNo,
            $languageCode,
            $parameters['page']
        );
    }

    /**
     * @param array $parameters
     *
     * @return \Ibexa\PageBuilder\Block\Context\ContentCreateBlockContext
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \LogicException
     */
    private function createContentCreateBlockContext(array $parameters): ContentCreateBlockContext
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setRequired(['languageCode', 'contentTypeIdentifier', 'parentLocationId', 'page']);
        try {
            $optionsResolver->resolve($parameters);
        } catch (Exception $e) {
            throw new InvalidArgumentException(
                '$parameters',
                sprintf('Invalid parameters provided. Make sure the $parameters array contains: %s', $optionsResolver->getRequiredOptions())
            );
        }

        $languageCode = $parameters['languageCode'];
        $contentTypeIdentifier = $parameters['contentTypeIdentifier'];
        $parentLocationId = (int)$parameters['parentLocationId'];

        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
        $parentLocation = $this->locationService->loadLocation($parentLocationId);

        return new ContentCreateBlockContext(
            $languageCode,
            $contentType,
            $parentLocation,
            $parameters['page']
        );
    }

    /**
     * @param array $parameters
     *
     * @return \Ibexa\FieldTypePage\FieldType\Page\Block\Context\ContentViewBlockContext
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     * @throws \LogicException
     */
    private function createContentViewBlockContext(array $parameters): ContentViewBlockContext
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setRequired(['languageCode', 'versionNo', 'locationId', 'page']);
        try {
            $optionsResolver->resolve($parameters);
        } catch (Exception $e) {
            throw new InvalidArgumentException(
                '$parameters',
                sprintf('Invalid parameters provided. Make sure the $parameters array contains: %s', $optionsResolver->getRequiredOptions())
            );
        }

        $languageCode = $parameters['languageCode'];
        $versionNo = (int)$parameters['versionNo'];
        $locationId = (int)$parameters['locationId'];

        $location = $this->locationService->loadLocation($locationId);
        $content = $this->contentService->loadContent(
            $location->contentId,
            [$languageCode],
            $versionNo
        );

        return new ContentViewBlockContext(
            $location,
            $content,
            $versionNo,
            $languageCode,
            $parameters['page']
        );
    }

    /**
     * @param array $parameters
     *
     * @return \Ibexa\PageBuilder\Block\Context\ContentTranslateBlockContext
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     * @throws \LogicException
     */
    private function createContentTranslateBlockContext(array $parameters): ContentTranslateBlockContext
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver
            ->setRequired(['languageCode', 'locationId', 'page'])
            ->setDefined('baseLanguageCode')
        ;

        try {
            $optionsResolver->resolve($parameters);
        } catch (Exception $e) {
            throw new InvalidArgumentException(
                '$parameters',
                sprintf(
                    'Invalid parameters provided. Make sure the $parameters array contains: %s',
                    implode(', ', $optionsResolver->getRequiredOptions())
                )
            );
        }

        $languageCode = $parameters['languageCode'];
        $locationId = (int)$parameters['locationId'];
        $baseLanguageCode = !empty($parameters['baseLanguageCode']) ? $parameters['baseLanguageCode'] : null;

        $location = $this->locationService->loadLocation($locationId);
        $content = $this->contentService->loadContent(
            $location->contentId,
            null !== $baseLanguageCode ? [$baseLanguageCode] : null
        );

        return new ContentTranslateBlockContext(
            $location,
            $content,
            $languageCode,
            $baseLanguageCode,
            $parameters['page']
        );
    }
}

class_alias(BlockContextFactory::class, 'EzSystems\EzPlatformPageBuilder\Block\Context\BlockContextFactory');
