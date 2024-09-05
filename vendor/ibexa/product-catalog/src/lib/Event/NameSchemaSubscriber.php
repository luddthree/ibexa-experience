<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Event;

use Exception;
use Ibexa\Contracts\Core\Event\NameSchema\AbstractNameSchemaEvent;
use Ibexa\Contracts\Core\Event\NameSchema\AbstractSchemaEvent;
use Ibexa\Contracts\Core\Event\NameSchema\ResolveContentNameSchemaEvent;
use Ibexa\Contracts\Core\Event\NameSchema\ResolveNameSchemaEvent;
use Ibexa\Contracts\Core\Event\NameSchema\ResolveUrlAliasSchemaEvent;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\NameSchema\NameSchemaStrategyInterface;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Value;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
final class NameSchemaSubscriber implements EventSubscriberInterface
{
    public const ATTRIBUTE_KEY = 'attribute';
    public const SEPARATOR = ':';

    private AttributeDefinitionServiceInterface $definitionService;

    private NameSchemaStrategyInterface $nameSchemaStrategy;

    private LoggerInterface $logger;

    public function __construct(
        AttributeDefinitionServiceInterface $definitionService,
        NameSchemaStrategyInterface $nameSchemaStrategy,
        LoggerInterface $logger
    ) {
        $this->definitionService = $definitionService;
        $this->nameSchemaStrategy = $nameSchemaStrategy;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ResolveNameSchemaEvent::class => [
                'onResolveNameSchema',
            ],
            ResolveContentNameSchemaEvent::class => [
                'onResolveContentNameSchema',
            ],
            ResolveUrlAliasSchemaEvent::class => [
                'onResolveUrlAliasSchema',
            ],
        ];
    }

    public function onResolveNameSchema(ResolveNameSchemaEvent $event): void
    {
        $this->processSchema($event);
    }

    public function onResolveContentNameSchema(ResolveContentNameSchemaEvent $event): void
    {
        $this->processSchema($event);
    }

    public function onResolveUrlAliasSchema(ResolveUrlAliasSchemaEvent $event): void
    {
        if (!$this->isEventValid($event)) {
            return;
        }

        $content = $event->getContent();
        $productSpecificationField = $this->getProductSpecificationField($content);

        if ($productSpecificationField === null) {
            return;
        }

        $event->setTokenValues(
            $this->processTokens(
                $event->getSchemaIdentifiers()[self::ATTRIBUTE_KEY],
                array_map(
                    static function (Language $language): string {
                        return $language->getLanguageCode();
                    },
                    (array) $content->getVersionInfo()->getLanguages()
                ),
                $productSpecificationField->getAttributes(),
                $event->getTokenValues()
            )
        );
    }

    private function processSchema(AbstractNameSchemaEvent $event): void
    {
        if (!$this->isEventValid($event)) {
            return;
        }

        $contentType = $event->getContentType();

        /** @var \Ibexa\Core\Repository\Values\ContentType\FieldDefinition $specificationDefinition */
        $specificationDefinition = $contentType->getFirstFieldDefinitionOfType(Type::FIELD_TYPE_IDENTIFIER);

        $productSpecificationField = $this->getProductSpecificationFieldFromEvent(
            $event,
            $specificationDefinition->identifier
        );

        if ($productSpecificationField === null) {
            return;
        }

        $tokenValues = $this->processTokens(
            $event->getSchemaIdentifiers()[self::ATTRIBUTE_KEY],
            $event->getLanguageCodes(),
            $productSpecificationField->getAttributes(),
            $event->getTokenValues()
        );

        $tokenValues = array_merge($event->getTokenValues(), $tokenValues);

        $event->setTokenValues($tokenValues);
    }

    private function getProductSpecificationFieldFromEvent(AbstractNameSchemaEvent $event, string $identifier): ?Value
    {
        /** @var array<\Ibexa\ProductCatalog\FieldType\ProductSpecification\Value> $productSpecificationField */
        $productSpecificationField = $event->getFieldMap()[$identifier] ?? [];

        return reset($productSpecificationField) ?: null;
    }

    /**
     * @param array<int, mixed> $attributes
     * @param array<string, mixed> $tokenValues
     */
    private function processToken(string $token, string $languageCode, array $attributes, array &$tokenValues): void
    {
        try {
            $attributeDefinition = $this->definitionService->getAttributeDefinition($token);
        } catch (NotFoundException|UnauthorizedException $e) {
            $this->handleAttributeDefinitionException($e, $token, $languageCode, $tokenValues);

            return;
        }

        $attributeId = $attributeDefinition->getId();
        if (isset($attributes[$attributeId])) {
            $key = self::ATTRIBUTE_KEY . self::SEPARATOR . $token;
            $value = $this->nameSchemaStrategy->resolve(
                $attributeDefinition,
                $attributes[$attributeId],
                $languageCode
            );

            $tokenValues[$languageCode][$key] = $value;
        } else {
            $this->logger->warning(
                'Attribute value does not exist for given attribute definition id',
                [
                    'attributes' => $attributes,
                    'attribute_definition_id' => $attributeDefinition->getIdentifier(),
                ]
            );
        }
    }

    /**
     * @param array<string, mixed> $tokenValues
     */
    private function handleAttributeDefinitionException(
        Exception $exception,
        string $token,
        string $languageCode,
        array &$tokenValues
    ): void {
        $this->logger->warning('Attribute definition does not exist for the given identifier', [
            'exception' => $exception,
        ]);

        $key = self::ATTRIBUTE_KEY . $token;
        $tokenValues[$languageCode][$key] = $token;
    }

    private function getProductSpecificationField(Content $content): ?Value
    {
        $specificationDefinition = $content->getContentType()
            ->getFirstFieldDefinitionOfType(Type::FIELD_TYPE_IDENTIFIER);

        if ($specificationDefinition === null) {
            return null;
        }

        /** @var \Ibexa\ProductCatalog\FieldType\ProductSpecification\Value $field */
        $field = $content->getFieldValue($specificationDefinition->identifier);

        return $field;
    }

    private function isEventValid(AbstractSchemaEvent $event): bool
    {
        return array_key_exists(self::ATTRIBUTE_KEY, $event->getSchemaIdentifiers());
    }

    /**
     * @param array<string> $tokens
     * @param array<string> $languageCodes
     * @param array<int, mixed> $attributes
     * @param array<string, array<string>> $tokenValues
     *
     * @return array<string, array<string>>
     */
    public function processTokens(
        array $tokens,
        array $languageCodes,
        array $attributes,
        array $tokenValues
    ): array {
        foreach ($languageCodes as $languageCode) {
            foreach ($tokens as $token) {
                $this->processToken($token, $languageCode, $attributes, $tokenValues);
            }
        }

        return $tokenValues;
    }
}
