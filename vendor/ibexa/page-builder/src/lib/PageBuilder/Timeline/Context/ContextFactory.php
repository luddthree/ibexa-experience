<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\PageBuilder\Timeline\Context;

use Exception;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use function in_array;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContextFactory
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
     * @return \Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContextInterface
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \LogicException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function build(string $intent, array $parameters): ContextInterface
    {
        if (ContentEditContext::INTENT === $intent) {
            return $this->createContentEditContext($parameters);
        }

        if (ContentCreateContext::INTENT === $intent) {
            return $this->createContentCreateContext($parameters);
        }

        if (ContentViewContext::INTENT === $intent) {
            return $this->createContentViewContext($parameters);
        }

        $translationIntents = [
            ContentTranslateContext::INTENT,
            ContentTranslateContext::INTENT_WITHOUT_BASE_LANGUAGE,
        ];

        if (in_array($intent, $translationIntents, true)) {
            return $this->createContentTranslateContext($parameters);
        }

        throw new InvalidArgumentException('intent', 'Missing or invalid intent passed.');
    }

    /**
     * @param array $parameters
     *
     * @return \Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContentEditContext
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     * @throws \LogicException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function createContentEditContext(array $parameters): ContentEditContext
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setRequired(['languageCode', 'versionNo', 'contentId']);
        $optionsResolver->setDefined(['locationId']);
        try {
            $optionsResolver->resolve($parameters);
        } catch (Exception $e) {
            throw new InvalidArgumentException(
                '$parameters',
                sprintf(
                    'Invalid parameters provided. Make sure the $parameters array contains: %s',
                    $optionsResolver->getRequiredOptions()
                )
            );
        }

        $languageCode = $parameters['languageCode'];
        $versionNo = (int)$parameters['versionNo'];
        $locationId = (int)$parameters['locationId'];
        $contentId = (int)$parameters['contentId'];

        $location = !empty($parameters['locationId'])
            ? $this->locationService->loadLocation($locationId)
            : null;

        $content = $this->contentService->loadContent(
            $contentId,
            [$languageCode],
            !empty($versionNo) ? $versionNo : null
        );

        return new ContentEditContext(
            $location,
            $content,
            $content->getVersionInfo(),
            $languageCode
        );
    }

    /**
     * @param array $parameters
     *
     * @return \Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContentCreateContext
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     */
    private function createContentCreateContext(array $parameters): ContentCreateContext
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setRequired(['languageCode', 'contentTypeIdentifier', 'parentLocationId']);
        try {
            $optionsResolver->resolve($parameters);
        } catch (Exception $e) {
            throw new InvalidArgumentException(
                '$parameters',
                sprintf(
                    'Invalid parameters provided. Make sure the $parameters array contains: %s',
                    $optionsResolver->getRequiredOptions()
                )
            );
        }

        $languageCode = $parameters['languageCode'];
        $contentTypeIdentifier = $parameters['contentTypeIdentifier'];
        $parentLocationId = (int)$parameters['parentLocationId'];

        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
        $parentLocation = $this->locationService->loadLocation($parentLocationId);

        return new ContentCreateContext(
            $contentType,
            $parentLocation,
            $languageCode
        );
    }

    /**
     * @param array $parameters
     *
     * @return \Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContentViewContext
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     */
    private function createContentViewContext(array $parameters): ContentViewContext
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setRequired(['languageCode', 'versionNo', 'locationId']);
        try {
            $optionsResolver->resolve($parameters);
        } catch (Exception $e) {
            throw new InvalidArgumentException(
                '$parameters',
                sprintf(
                    'Invalid parameters provided. Make sure the $parameters array contains: %s',
                    $optionsResolver->getRequiredOptions()
                )
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

        return new ContentViewContext(
            $location,
            $content,
            $content->versionInfo,
            $languageCode
        );
    }

    /**
     * @param array $parameters
     *
     * @return \Ibexa\PageBuilder\PageBuilder\Timeline\Context\ContentTranslateContext
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     */
    private function createContentTranslateContext(array $parameters): ContentTranslateContext
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver
            ->setRequired(['languageCode', 'locationId'])
            ->setDefined('baseLanguageCode');

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

        return new ContentTranslateContext(
            $location,
            $content,
            $languageCode,
            $baseLanguageCode
        );
    }
}

class_alias(ContextFactory::class, 'EzSystems\EzPlatformPageBuilder\PageBuilder\Timeline\Context\ContextFactory');
