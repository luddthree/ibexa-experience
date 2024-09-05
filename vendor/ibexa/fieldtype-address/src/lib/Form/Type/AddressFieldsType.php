<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypeAddress\Form\Type;

use Ibexa\Contracts\FieldTypeAddress\Event\Events;
use Ibexa\Contracts\FieldTypeAddress\Event\MapFieldEvent;
use Ibexa\FieldTypeAddress\FieldType\Resolver\FormatConfigurationResolver;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class AddressFieldsType extends BaseAddressType implements TranslationContainerInterface
{
    private const FIELD_TRANSLATION_PATTERN = 'ibexa.address.fields.%s';
    private const TRANSLATION_DOMAIN = 'ibexa_fieldtype_address';

    private FormatConfigurationResolver $formatConfigurationResolver;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        FormatConfigurationResolver $formatConfigurationResolver,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->formatConfigurationResolver = $formatConfigurationResolver;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $addressType = $options['type'];
        $country = $options['country'];

        $fields = $this->formatConfigurationResolver->resolveFields($addressType, $country);

        foreach ($fields as $field) {
            $mapFieldEvent = new MapFieldEvent($field, $addressType, $country);

            foreach (Events::getMapFieldEventNames($field, $addressType, $country) as $eventName) {
                $mapFieldEvent = $this->eventDispatcher->dispatch($mapFieldEvent, $eventName);
            }

            $type = $mapFieldEvent->getType() ?? TextType::class;
            $options = array_merge([
                'type' => $addressType,
                'country' => $country,
                'label' => $mapFieldEvent->getLabel() ?: self::getFieldTranslationKey($field),
            ], $mapFieldEvent->getOptions());

            $builder->add($field, $type, $options);
        }
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(self::getFieldTranslationKey('region'), self::TRANSLATION_DOMAIN)
                ->setDesc('Region'),
            Message::create(self::getFieldTranslationKey('locality'), self::TRANSLATION_DOMAIN)
                ->setDesc('City'),
            Message::create(self::getFieldTranslationKey('address'), self::TRANSLATION_DOMAIN)
                ->setDesc('Address'),
            Message::create(self::getFieldTranslationKey('postal_code'), self::TRANSLATION_DOMAIN)
                ->setDesc('Postal Code'),
            Message::create(self::getFieldTranslationKey('street'), self::TRANSLATION_DOMAIN)
                ->setDesc('Street'),
        ];
    }

    private static function getFieldTranslationKey(string $fieldIdentifier): string
    {
        return sprintf(self::FIELD_TRANSLATION_PATTERN, $fieldIdentifier);
    }
}
