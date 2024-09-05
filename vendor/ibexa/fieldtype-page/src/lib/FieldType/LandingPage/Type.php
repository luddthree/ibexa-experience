<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\FieldType\LandingPage;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue as PersistenceValue;
use Ibexa\Contracts\Core\Repository\Values\Content\Relation;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Zone;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Service\BlockServiceInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\FieldType\Value as BaseValue;
use Ibexa\Core\Repository\ProxyFactory\ProxyGeneratorInterface;
use Ibexa\FieldTypePage\Event\PageEvents;
use Ibexa\FieldTypePage\Event\PageFromPersistenceEvent;
use Ibexa\FieldTypePage\Event\PageToPersistenceEvent;
use Ibexa\FieldTypePage\FieldType\LandingPage\Converter\PageConverter;
use Ibexa\FieldTypePage\FieldType\LandingPage\Definition\LayoutDefinition;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface;
use Ibexa\FieldTypePage\FieldType\Page\Block\Relation\RelationCollector;
use Ibexa\FieldTypePage\Registry\LayoutDefinitionRegistry;
use JMS\Serializer\SerializationContext;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use ProxyManager\Proxy\LazyLoadingInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @method \Ibexa\FieldTypePage\FieldType\LandingPage\Value acceptValue($inputValue)
 */
class Type extends FieldType implements TranslationContainerInterface
{
    public const IDENTIFIER = 'ezlandingpage';
    public const COMPARABLE_SERIALIZATION_CONTEXT_GROUP = 'comparable';

    protected $settingsSchema = [
        'availableBlocks' => [
            'type' => 'array',
            'default' => null,
        ],
        'availableLayouts' => [
            'type' => 'array',
            'default' => null,
        ],
    ];

    /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Converter\PageConverter */
    private $converter;

    /** @var \Ibexa\FieldTypePage\Registry\LayoutDefinitionRegistry */
    private $layoutDefinitionRegistry;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface */
    private $blockDefinitionFactory;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Relation\RelationCollector */
    private $relationCollector;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Service\BlockService */
    private $blockService;

    /** @var \Ibexa\Core\Repository\ProxyFactory\ProxyGeneratorInterface */
    private $proxyGenerator;

    public function __construct(
        PageConverter $converter,
        LayoutDefinitionRegistry $layoutDefinitionRegistry,
        BlockDefinitionFactoryInterface $blockDefinitionFactory,
        RelationCollector $relationCollector,
        EventDispatcherInterface $eventDispatcher,
        BlockServiceInterface $blockService,
        ProxyGeneratorInterface $proxyGenerator
    ) {
        $this->converter = $converter;
        $this->layoutDefinitionRegistry = $layoutDefinitionRegistry;
        $this->blockDefinitionFactory = $blockDefinitionFactory;
        $this->relationCollector = $relationCollector;
        $this->eventDispatcher = $eventDispatcher;
        $this->blockService = $blockService;
        $this->proxyGenerator = $proxyGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldTypeIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    /**
     * {@inheritdoc}
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition
     * @param \Ibexa\Contracts\Core\FieldType\Value|\Ibexa\FieldTypePage\FieldType\LandingPage\Value $value
     *
     * @return array|\Ibexa\Contracts\Core\FieldType\ValidationError[]
     *
     * @throws \Exception
     */
    public function validate(FieldDefinition $fieldDefinition, SPIValue $value): array
    {
        $page = $value->getPage();
        $pageZones = $page->getZones();

        $validationErrors = [];

        if ($this->isEmptyValue($value)) {
            return $validationErrors;
        }

        $availableBlocks = $fieldDefinition->getFieldSettings()['availableBlocks'];

        foreach ($pageZones as $zone) {
            foreach ($zone->getBlocks() as $block) {
                if ($availableBlocks !== null && !in_array($block->getType(), $availableBlocks, true)) {
                    $validationErrors[] = new ValidationError(
                        "Block type '%block_type%' was disabled for '%landing_page_fd_name%'.",
                        null,
                        [
                            '%block_name%' => $block->getType(),
                            '%landing_page_fd_name%' => $fieldDefinition->getName(),
                        ],
                        'value'
                    );
                }
                $blockDefinition = $this->blockDefinitionFactory->getBlockDefinition($block->getType());

                $validationErrors += $this->blockService->validateBlock($block, $blockDefinition);
            }
        }

        $availableLayouts = $fieldDefinition->getFieldSettings()['availableLayouts'];
        if ($availableLayouts !== null && !in_array($page->getLayout(), $availableLayouts, true)) {
            $validationErrors[] = new ValidationError(
                "Layout '%layout_id%' was disabled for '%landing_page_fd_name%'.",
                null,
                [
                    '%layout_id%' => $page->getLayout(),
                    '%landing_page_fd_name%' => $fieldDefinition->getName(),
                ],
                'value'
            );
        }

        return $validationErrors;
    }

    /**
     * {@inheritdoc}
     */
    protected function checkValueStructure(BaseValue $value)
    {
        $page = $value->getPage();
        $layoutZones = $this->layoutDefinitionRegistry->getLayoutDefinitionById($page->getLayout())->getZones();
        $pageZones = $page->getZones();

        if (\count($pageZones) !== \count($layoutZones)) {
            throw new InvalidArgumentException('$value->pageZones', 'Number of page zones is different from the number of zones in the layout.');
        }
    }

    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        return ''; // @todo proper name
    }

    /**
     * {@inheritdoc}
     */
    public function getEmptyValue()
    {
        $defaultLayoutDefinition = $this->layoutDefinitionRegistry->getDefaultLayout();
        $zones = [];

        foreach ($defaultLayoutDefinition->getZones() as $identifier => $zoneDefinition) {
            $zones[] = new Zone($identifier, $zoneDefinition['name']);
        }

        return new Value(
            new Page(
                $defaultLayoutDefinition->getId(),
                $zones
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function fromHash($hash)
    {
        if ($hash === null) {
            return $this->getEmptyValue();
        }

        return new Value($this->converter->fromArray($hash));
    }

    /**
     * @param \Ibexa\FieldTypePage\FieldType\LandingPage\Value $value
     *
     * {@inheritdoc}
     */
    public function toHash(SPIValue $value)
    {
        $page = $value->getPage();

        return $this->converter->toArray($page);
    }

    /**
     * {@inheritdoc}
     */
    protected function createValueFromInput($inputValue)
    {
        if (\is_string($inputValue)) {
            $inputValue = new Value($this->converter->decode($inputValue));
        }

        return $inputValue;
    }

    /**
     * {@inheritdoc}
     */
    public function isSingular(): bool
    {
        return true;
    }

    /**
     * Converts a $value to a persistence value.
     *
     * @param \Ibexa\Contracts\Core\FieldType\Value $value
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\FieldValue
     */
    public function toPersistenceValue(SPIValue $value): PersistenceValue
    {
        $externalData = $this->toHash($value);

        $event = new PageToPersistenceEvent($value, $externalData);
        $this->eventDispatcher->dispatch($event, PageEvents::PERSISTENCE_TO);

        return new PersistenceValue([
            'data' => null,
            'externalData' => $event->getValue(),
            'sortKey' => null,
        ]);
    }

    /**
     * Converts a persistence $fieldValue to a Value.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\FieldValue $fieldValue
     *
     * @return \Ibexa\Core\FieldType\Value
     */
    public function fromPersistenceValue(PersistenceValue $fieldValue): Value
    {
        $externalData = $fieldValue->externalData;

        if (empty($externalData)) {
            return $this->getEmptyValue();
        }

        return $this->createValueProxy($fieldValue);
    }

    private function createValueProxy(PersistenceValue $fieldValue): Value
    {
        $initializer = function (
            &$value,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            &$initializer
        ) use ($fieldValue): bool {
            $initializer = null;

            $value = $this->fromHash($fieldValue->externalData);

            $event = new PageFromPersistenceEvent($fieldValue, $value);
            $this->eventDispatcher->dispatch($event, PageEvents::PERSISTENCE_FROM);

            $value = $event->getValue();

            return true;
        };

        return $this->proxyGenerator->createProxy(Value::class, $initializer);
    }

    /**
     * @param \Ibexa\FieldTypePage\FieldType\LandingPage\Value|\Ibexa\Contracts\Core\FieldType\Value $fieldValue
     *
     * @return array
     */
    public function getRelations(SPIValue $fieldValue): array
    {
        $page = $fieldValue->getPage();

        $relations = [];
        foreach ($page->getZones() as $zone) {
            foreach ($zone->getBlocks() as $blockValue) {
                $relations[] = $this->relationCollector->collect($fieldValue, $blockValue);
            }
        }

        if (empty($relations)) {
            return [];
        }

        return [Relation::FIELD => array_unique(array_merge(...$relations))];
    }

    public function validateFieldSettings($fieldSettings)
    {
        $validationErrors = [];
        if (array_key_exists('availableBlocks', $fieldSettings) && !empty($fieldSettings['availableBlocks'])) {
            $blocksIdentifiers = $this->blockDefinitionFactory->getBlockIdentifiers();
            $nonExistingBlocks = array_diff($fieldSettings['availableBlocks'], $blocksIdentifiers);

            if (!empty($nonExistingBlocks)) {
                return [
                    new ValidationError(
                        "Disabled blocks with identifiers: '%blockIdList%' does not exist in Page builder configuration",
                        null,
                        [
                            'blockIdList' => implode(', ', $nonExistingBlocks),
                        ],
                        'fieldType'
                    ),
                ];
            }
        }

        if (array_key_exists('availableLayouts', $fieldSettings) && !empty($fieldSettings['availableLayouts'])) {
            $layoutsIdentifiers = array_map(static function (LayoutDefinition $layoutDefinition): string {
                return $layoutDefinition->getId();
            }, $this->layoutDefinitionRegistry->getLayoutDefinitions());

            $nonExistingLayouts = array_diff($fieldSettings['availableLayouts'], $layoutsIdentifiers);

            if (!empty($nonExistingLayouts)) {
                return [
                    new ValidationError(
                        "Disabled layouts with identifiers: '%layoutsIdList%' does not exist in Page builder configuration",
                        null,
                        [
                            'layoutsIdList' => implode(', ', $nonExistingLayouts),
                        ],
                        'fieldType'
                    ),
                ];
            }
        }

        if (array_key_exists('availableLayouts', $fieldSettings) && $fieldSettings['availableLayouts'] === []) {
            $validationErrors[] = new ValidationError(
                'At least one Layout must remain active',
                null,
                [],
                'value'
            );
        }

        return $validationErrors;
    }

    /**
     * @param \Ibexa\Contracts\Core\FieldType\Value $value1
     * @param \Ibexa\Contracts\Core\FieldType\Value $value2
     */
    public function valuesEqual(SPIValue $value1, SPIValue $value2): bool
    {
        $arrayValue1 = $this->converter->toArray(
            $value1->getPage(),
            SerializationContext::create()->setGroups([self::COMPARABLE_SERIALIZATION_CONTEXT_GROUP])
        );
        $arrayValue2 = $this->converter->toArray(
            $value2->getPage(),
            SerializationContext::create()->setGroups([self::COMPARABLE_SERIALIZATION_CONTEXT_GROUP])
        );

        return $arrayValue1 === $arrayValue2;
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(self::IDENTIFIER . '.name', 'ibexa_fieldtypes')->setDesc('Landing Page'),
        ];
    }
}

class_alias(Type::class, 'EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Type');
