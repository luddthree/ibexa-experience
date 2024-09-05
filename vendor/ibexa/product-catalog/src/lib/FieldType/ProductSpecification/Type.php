<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\FieldType\ProductSpecification;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue as PersistenceValue;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\FieldType\Value as BaseValue;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Product\HandlerInterface as ProductHandler;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class Type extends FieldType implements TranslationContainerInterface
{
    public const FIELD_TYPE_IDENTIFIER = 'ibexa_product_specification';

    public const PRODUCT_CODE_MAX_LENGTH = 64;
    public const PRODUCT_CODE_PATTERN = '/^[[:alnum:]_-]+$/';

    private const ERROR_MESSAGE_PRODUCT_CODE_PATTERN = 'ibexa.product.code.pattern';
    private const ERROR_MESSAGE_PRODUCT_CODE_REQUIRED = 'ibexa.product.code.required';
    private const ERROR_MESSAGE_PRODUCT_CODE_UNIQUE = 'ibexa.product.code.unique';
    private const ERROR_MESSAGE_PRODUCT_CODE_LENGTH = 'ibexa.product.code.length';

    protected $settingsSchema = [
        'attributes_definitions' => [
            'type' => 'hash',
            'default' => [],
        ],
        'regions' => [
            'type' => 'hash',
            'default' => [],
        ],
        'is_virtual' => [
            'type' => 'boolean',
            'default' => false,
        ],
    ];

    private ProductHandler $productHandler;

    public function __construct(ProductHandler $productHandler)
    {
        $this->productHandler = $productHandler;
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
        return new Value();
    }

    public function fromHash($hash): Value
    {
        if (!empty($hash)) {
            return new Value(
                $hash['id'] ?? null,
                $hash['code'] ?? null,
                $hash['attributes'] ?? [],
                $hash['is_virtual'] ?? false,
            );
        }

        return $this->getEmptyValue();
    }

    /**
     * @param \Ibexa\ProductCatalog\FieldType\ProductSpecification\Value $value
     *
     * @return array<mixed>|null
     */
    public function toHash(SPIValue $value): ?array
    {
        if ($this->isEmptyValue($value)) {
            return null;
        }

        return [
            'id' => $value->getId(),
            'code' => $value->getCode(),
            'attributes' => $value->getAttributes(),
            'is_virtual' => $value->isVirtual(),
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
     * @param \Ibexa\ProductCatalog\FieldType\ProductSpecification\Value $value
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
                'id' => $value->getId(),
                'code' => $value->getCode(),
                'attributes' => $value->getAttributes(),
                'is_virtual' => $value->isVirtual(),
            ],
            'sortKey' => $this->getSortInfo($value),
        ]);
    }

    /**
     * @param \Ibexa\ProductCatalog\FieldType\ProductSpecification\Value $value
     */
    public function validate(FieldDefinition $fieldDefinition, SPIValue $value): array
    {
        $errors = [];

        if ($this->isEmptyValue($value)) {
            return $errors;
        }

        $code = (string)$value->getCode();

        if ($code === '') {
            $errors[] = new ValidationError(
                self::ERROR_MESSAGE_PRODUCT_CODE_REQUIRED,
                null,
                [],
                'code'
            );
        } elseif (mb_strlen($code) > self::PRODUCT_CODE_MAX_LENGTH) {
            $errors[] = new ValidationError(
                self::ERROR_MESSAGE_PRODUCT_CODE_LENGTH,
                null,
                [
                    ':max_length' => self::PRODUCT_CODE_MAX_LENGTH,
                ],
                'code'
            );
        } elseif (!preg_match(self::PRODUCT_CODE_PATTERN, $code)) {
            $errors[] = new ValidationError(
                self::ERROR_MESSAGE_PRODUCT_CODE_PATTERN,
                null,
                [
                    ':code' => $code,
                ],
                'code'
            );
        } elseif ($value->isCodeChanged() && !$this->productHandler->isCodeUnique($code)) {
            $errors[] = new ValidationError(
                self::ERROR_MESSAGE_PRODUCT_CODE_UNIQUE,
                null,
                [
                    ':code' => $code,
                ],
                'code'
            );
        }

        return $errors;
    }

    public function validateFieldSettings($fieldSettings): array
    {
        $validationErrors = [];

        foreach ($fieldSettings as $name => $value) {
            if (!isset($this->settingsSchema[$name])) {
                $validationErrors[] = new ValidationError(
                    "Setting '%setting%' is unknown",
                    null,
                    [
                        '%setting%' => $name,
                    ],
                    "[$name]"
                );
            }
        }

        return $validationErrors;
    }

    /**
     * @param \Ibexa\ProductCatalog\FieldType\ProductSpecification\Value $value
     */
    public function isEmptyValue(SPIValue $value): bool
    {
        return $value->getCode() === null;
    }

    protected function createValueFromInput($inputValue)
    {
        if (is_array($inputValue)) {
            return new Value(
                $inputValue['id'] ?? null,
                $inputValue['code'] ?? null,
                $inputValue['attributes'] ?? [],
                $inputValue['is_virtual'] ?? false,
            );
        }

        return $inputValue;
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(self::ERROR_MESSAGE_PRODUCT_CODE_PATTERN, 'validators')
                ->setDesc('Product code may only contain letters from "a" to "z", numbers, underscores and dashes.'),
            Message::create(self::ERROR_MESSAGE_PRODUCT_CODE_LENGTH, 'validators')
                ->setDesc('Product code is too long. It should have :max_length characters or less'),
            Message::create(self::ERROR_MESSAGE_PRODUCT_CODE_REQUIRED, 'validators')
                ->setDesc('Product code is required'),
            Message::create(self::ERROR_MESSAGE_PRODUCT_CODE_UNIQUE, 'validators')
                ->setDesc('Product code must be unique'),
            Message::create(self::FIELD_TYPE_IDENTIFIER . '.name', 'ibexa_fieldtypes')
                ->setDesc('Product specification'),
        ];
    }
}
