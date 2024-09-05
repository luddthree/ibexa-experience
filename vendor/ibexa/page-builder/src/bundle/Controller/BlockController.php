<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\PageBuilder\Controller;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface as RepositoryConfigResolver;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model as PageData;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface;
use Ibexa\PageBuilder\Block\Mapper\BlockValueMapper;
use Ibexa\PageBuilder\Data\Attribute\Attribute;
use Ibexa\PageBuilder\Data\Block\BlockConfiguration;
use Ibexa\PageBuilder\Event\BlockConfigurationViewEvent;
use Ibexa\PageBuilder\Event\BlockConfigurationViewEvents;
use Ibexa\PageBuilder\Form\Type\Block\BlockConfigurationType;
use Ibexa\PageBuilder\Form\Type\Block\RequestBlockConfigurationType;
use Ibexa\PageBuilder\View\BlockConfigurationUpdatedView;
use Ibexa\PageBuilder\View\BlockConfigurationView;
use Psr\Log\LoggerInterface;
use ScssPhp\ScssPhp\Compiler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;

class BlockController extends Controller
{
    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface */
    private $blockDefinitionFactory;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    /** @var \Ibexa\PageBuilder\Block\Mapper\BlockValueMapper */
    private $blockValueMapper;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    public function __construct(
        BlockDefinitionFactoryInterface $blockDefinitionFactory,
        EventDispatcherInterface $eventDispatcher,
        RepositoryConfigResolver $configResolver,
        BlockValueMapper $blockValueMapper,
        LoggerInterface $logger
    ) {
        $this->blockDefinitionFactory = $blockDefinitionFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->configResolver = $configResolver;
        $this->blockValueMapper = $blockValueMapper;
        $this->logger = $logger;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Ibexa\PageBuilder\View\BlockConfigurationView
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function requestBlockConfigurationAction(Request $request): BlockConfigurationView
    {
        $requestBlockConfigurationForm = $this->createForm(RequestBlockConfigurationType::class);
        $requestBlockConfigurationForm->handleRequest($request);

        $isStylingEnabled = $this->configResolver->getParameter('page_builder.block_styling_enabled');

        if ($requestBlockConfigurationForm->isSubmitted() && $requestBlockConfigurationForm->isValid()) {
            /** @var \Ibexa\PageBuilder\Data\RequestBlockConfiguration $data */
            $data = $requestBlockConfigurationForm->getData();
            $page = $data->getPage();
            $blockValue = $page->getBlockById($data->getBlockId());
            $blockDefinition = $this->blockDefinitionFactory->getBlockDefinition($blockValue->getType());
            $blockConfiguration = $this->blockValueMapper->mapToBlockConfiguration($blockValue);

            $blockConfigurationForm = $this->createForm(
                BlockConfigurationType::class,
                $blockConfiguration,
                [
                    'action' => $this->generateUrl('ibexa.page_builder.block.configure', [
                        'blockType' => $blockValue->getType(),
                        'languageCode' => $data->getLanguage()->languageCode,
                    ]),
                    'method' => Request::METHOD_POST,
                    'block_type' => $blockDefinition,
                    'language_code' => $data->getLanguage()->languageCode,
                ]
            );

            $attributesPerCategory = $this->getAttributesPerCategory($blockDefinition->getAttributes());

            $formView = $blockConfigurationForm->createView();

            $blockConfigurationView = new BlockConfigurationView(
                '@IbexaPageBuilder/page_builder/block/config.html.twig',
                [
                    'block_type' => $blockDefinition,
                    'form' => $formView,
                    'attributes_per_category' => $attributesPerCategory,
                    'is_styling_enabled' => $isStylingEnabled,
                    'language_code' => $data->getLanguage()->languageCode,
                ]
            );

            $blockConfigurationView
                ->setBlockType($blockDefinition)
                ->setForm($formView)
                ->setBlockTypeIdentifier($blockValue->getType());

            $blockConfigurationViewEvent = $this->dispatchBlockConfigurationViewEvent(
                $blockConfigurationView,
                $blockDefinition,
                $blockConfiguration,
                $blockConfigurationForm,
                $blockValue->getType()
            );

            return $blockConfigurationViewEvent->getBlockConfigurationView();
        }

        foreach ($requestBlockConfigurationForm->getErrors(true, true) as $formError) {
            $this->logger->error($formError->getMessage());
        }

        throw new BadRequestException();
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition[] $attributes
     *
     * @return array<string, array<\Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition>>
     */
    private function getAttributesPerCategory(array $attributes): array
    {
        $attributesPerCategory = [];

        foreach ($attributes as $attribute) {
            $attributesPerCategory[$attribute->getCategory()][] = $attribute->getIdentifier();
        }

        return $attributesPerCategory;
    }

    /**
     * @param \Ibexa\PageBuilder\View\BlockConfigurationView $blockConfigurationView
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition $blockDefinition
     * @param \Ibexa\PageBuilder\Data\Block\BlockConfiguration $blockConfiguration
     * @param \Symfony\Component\Form\FormInterface $blockConfigurationForm
     * @param string $blockType
     *
     * @return \Ibexa\PageBuilder\Event\BlockConfigurationViewEvent
     */
    private function dispatchBlockConfigurationViewEvent(
        BlockConfigurationView $blockConfigurationView,
        BlockDefinition $blockDefinition,
        BlockConfiguration $blockConfiguration,
        FormInterface $blockConfigurationForm,
        string $blockType
    ): BlockConfigurationViewEvent {
        $blockConfigurationViewEvent = new BlockConfigurationViewEvent(
            $blockConfigurationView,
            $blockDefinition,
            $blockConfiguration,
            $blockConfigurationForm
        );

        /** @var \Ibexa\PageBuilder\Event\BlockConfigurationViewEvent $blockConfigurationViewEvent */
        $blockConfigurationViewEvent = $this->eventDispatcher->dispatch($blockConfigurationViewEvent, BlockConfigurationViewEvents::GLOBAL_BLOCK_CONFIGURATION_VIEW);

        /** @var \Ibexa\PageBuilder\Event\BlockConfigurationViewEvent $blockConfigurationViewEvent */
        $blockConfigurationViewEvent = $this->eventDispatcher->dispatch($blockConfigurationViewEvent, BlockConfigurationViewEvents::getBlockConfigurationViewEventName($blockType));

        return $blockConfigurationViewEvent;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $blockType
     * @param string|null $languageCode
     *
     * @return \Ibexa\PageBuilder\View\BlockConfigurationUpdatedView|\Ibexa\PageBuilder\View\BlockConfigurationView
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function configureBlockAction(Request $request, string $blockType, ?string $languageCode = null)
    {
        if ($languageCode === null) {
            @trigger_error('Calling ' . self::class . '::configureBlockAction without languageCode is deprecated since ezplatform-page-builder 1.2 and will be not possible in 2.0.', E_USER_DEPRECATED);
        }

        $blockDefinition = $this->blockDefinitionFactory->getBlockDefinition($blockType);

        $blockConfigurationForm = $this->createForm(
            BlockConfigurationType::class,
            null,
            [
                'block_type' => $blockDefinition,
                'language_code' => $languageCode,
            ]
        );
        $blockConfigurationForm->handleRequest($request);

        $isStylingEnabled = $this->configResolver->getParameter('page_builder.block_styling_enabled');

        $blockConfiguration = $blockConfigurationForm->getData();

        if ($blockConfigurationForm->isSubmitted() && $blockConfigurationForm->isValid()) {
            /** @var \Ibexa\PageBuilder\Data\Block\BlockConfiguration $data */
            $data = $blockConfigurationForm->getData();

            $blockValue = new PageData\BlockValue(
                $data->getId(),
                $data->getType(),
                $data->getName(),
                $data->getView(),
                $data->getClass(),
                $data->getStyle(),
                // @todo: Move to ScssType with data transformer
                (new Compiler())->compile(sprintf(
                    '[data-ez-block-id="%s"] { %s }',
                    $data->getId(),
                    $data->getStyle()
                )),
                $data->getSince(),
                $data->getTill(),
                array_map(
                    static function (Attribute $attribute) {
                        return new PageData\Attribute(
                            $attribute->getId(),
                            $attribute->getName(),
                            $attribute->getValue()
                        );
                    },
                    $data->getAttributes()
                )
            );

            $blockConfigurationUpdatedView = new BlockConfigurationUpdatedView(
                '@IbexaPageBuilder/page_builder/block/config_updated.html.twig',
                [
                    'block_value' => $blockValue,
                    'block_type' => $blockDefinition,
                    'is_styling_enabled' => $isStylingEnabled,
                    'block_visible' => $blockValue->isVisible(),
                ]
            );

            $blockConfigurationUpdatedView
                ->setBlockValue($blockValue)
                ->setBlockType($blockDefinition);

            return $blockConfigurationUpdatedView;
        }

        $attributesPerCategory = $this->getAttributesPerCategory($blockDefinition->getAttributes());

        $formView = $blockConfigurationForm->createView();
        $blockConfigurationView = new BlockConfigurationView(
            '@IbexaPageBuilder/page_builder/block/config.html.twig',
            [
                'block_type' => $blockDefinition,
                'form' => $formView,
                'is_styling_enabled' => $isStylingEnabled,
                'attributes_per_category' => $attributesPerCategory,
                'language_code' => $languageCode,
            ]
        );

        $blockConfigurationView
            ->setBlockType($blockDefinition)
            ->setForm($formView)
            ->setBlockTypeIdentifier($blockType);

        $blockConfigurationViewEvent = $this->dispatchBlockConfigurationViewEvent(
            $blockConfigurationView,
            $blockDefinition,
            $blockConfiguration,
            $blockConfigurationForm,
            $blockType
        );

        return $blockConfigurationViewEvent->getBlockConfigurationView();
    }
}

class_alias(BlockController::class, 'EzSystems\EzPlatformPageBuilderBundle\Controller\BlockController');
