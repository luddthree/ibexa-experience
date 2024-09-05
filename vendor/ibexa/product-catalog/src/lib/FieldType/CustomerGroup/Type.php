<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\FieldType\CustomerGroup;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue as PersistenceValue;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\FieldType\Value as BaseValue;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\HandlerInterface;
use Ibexa\ProductCatalog\Values\ProxyFactory\CustomerGroupProxyFactoryInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class Type extends FieldType implements TranslationContainerInterface
{
    public const FIELD_TYPE_IDENTIFIER = 'ibexa_customer_group';

    public const FIELD_ID_KEY = 'customer_group_id';

    private HandlerInterface $handler;

    private CustomerGroupProxyFactoryInterface $proxyFactory;

    public function __construct(
        HandlerInterface $handler,
        CustomerGroupProxyFactoryInterface $proxyFactory
    ) {
        $this->handler = $handler;
        $this->proxyFactory = $proxyFactory;
    }

    protected function checkValueStructure(BaseValue $value): void
    {
        // Value is self-contained and strong typed
    }

    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        return (string)$value;
    }

    public function getFieldTypeIdentifier(): string
    {
        return self::FIELD_TYPE_IDENTIFIER;
    }

    public function isSingular(): bool
    {
        return true;
    }

    public function getEmptyValue(): Value
    {
        return new Value(null);
    }

    public function fromHash($hash): Value
    {
        if (!empty($hash) && isset($hash[self::FIELD_ID_KEY])) {
            $customerGroupId = (int) $hash[self::FIELD_ID_KEY];

            return new Value(
                $customerGroupId,
                $this->proxyFactory->createCustomerGroupProxy($customerGroupId, null)
            );
        }

        return $this->getEmptyValue();
    }

    /**
     * @param \Ibexa\ProductCatalog\FieldType\CustomerGroup\Value $value
     *
     * @phpstan-return array{customer_group_id: ?int}|null
     */
    public function toHash(SPIValue $value): ?array
    {
        if ($this->isEmptyValue($value)) {
            return null;
        }

        return [
            self::FIELD_ID_KEY => $value->getId(),
        ];
    }

    public function fromPersistenceValue(PersistenceValue $fieldValue): Value
    {
        if ($fieldValue->externalData === null) {
            return $this->getEmptyValue();
        }

        return $this->fromHash($fieldValue->externalData);
    }

    /**
     * @param \Ibexa\ProductCatalog\FieldType\CustomerGroup\Value $value
     */
    public function toPersistenceValue(SPIValue $value): FieldValue
    {
        if ($this->isEmptyValue($value)) {
            return new FieldValue([
                'data' => null,
                'externalData' => null,
                'sortKey' => null,
            ]);
        }

        return new FieldValue([
            'data' => null,
            'externalData' => [
                self::FIELD_ID_KEY => $value->getId(),
            ],
            'sortKey' => $this->getSortInfo($value),
        ]);
    }

    /**
     * @param \Ibexa\ProductCatalog\FieldType\CustomerGroup\Value $value
     */
    public function validate(FieldDefinition $fieldDefinition, SPIValue $value): array
    {
        if ($this->isEmptyValue($value)) {
            return [];
        }

        $errors = [];

        $id = $value->getId();
        if ($id === null) {
            $errors[] = new ValidationError(
                'Missing ID for Customer Group',
                null,
                [],
                'id'
            );
        } elseif (!$this->handler->exists($id)) {
            $errors[] = new ValidationError(
                'Customer group with ID :id does not exist',
                null,
                [
                   ':id' => $value->getId(),
               ],
                'id'
            );
        }

        return $errors;
    }

    /**
     * @param \Ibexa\ProductCatalog\FieldType\CustomerGroup\Value $value
     */
    public function isEmptyValue(SPIValue $value): bool
    {
        return $value->getId() === null;
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    protected function createValueFromInput($inputValue)
    {
        if (!is_array($inputValue) || !isset($inputValue[self::FIELD_ID_KEY])) {
            return $inputValue;
        }

        $customerGroupId = (int) $inputValue[self::FIELD_ID_KEY];

        return new Value(
            $customerGroupId,
            $this->proxyFactory->createCustomerGroupProxy($customerGroupId, null)
        );
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(self::FIELD_TYPE_IDENTIFIER . '.name', 'ibexa_fieldtypes')
                ->setDesc('Customer group'),
        ];
    }
}
