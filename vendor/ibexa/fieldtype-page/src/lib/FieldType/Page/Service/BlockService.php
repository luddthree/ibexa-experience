<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Service;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Service\BlockServiceInterface;
use Ibexa\Core\Base\Exceptions\BadStateException;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\FieldTypePage\Event\BlockContextEvent;
use Ibexa\FieldTypePage\Event\BlockContextEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Context\BlockContextInterface;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Attribute\Validator\ConstraintFactory;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PostRenderEvent;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\RendererInterface;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig\TwigRenderRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class BlockService implements BlockServiceInterface
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\RendererInterface */
    private $renderer;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface */
    private $blockDefinitionFactory;

    /** @var \Symfony\Component\Validator\ConstraintValidatorFactoryInterface */
    private $constraintValidatorFactory;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Attribute\Validator\ConstraintFactory */
    private $constraintFactory;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        RendererInterface $renderer,
        BlockDefinitionFactoryInterface $blockDefinitionFactory,
        ConstraintValidatorFactoryInterface $constraintValidatorFactory,
        ConstraintFactory $constraintFactory
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->renderer = $renderer;
        $this->blockDefinitionFactory = $blockDefinitionFactory;
        $this->constraintValidatorFactory = $constraintValidatorFactory;
        $this->constraintFactory = $constraintFactory;
    }

    private function getValidator()
    {
        $validatorBuilder = Validation::createValidatorBuilder();
        $validatorBuilder->setConstraintValidatorFactory(
            $this->constraintValidatorFactory
        );

        return $validatorBuilder->getValidator();
    }

    public function validateBlock(BlockValue $block, BlockDefinition $blockDefinition): array
    {
        $validator = $this->getValidator();
        $validationErrors = $this->validateBlockName($validator, $block, $blockDefinition);

        $blockDefinitionAttributes = $blockDefinition->getAttributes();

        foreach ($block->getAttributes() as $attribute) {
            $attributeName = $attribute->getName();
            if (!isset($blockDefinitionAttributes[$attributeName])) {
                continue;
            }

            $blockDefinitionAttribute = $blockDefinitionAttributes[$attributeName];
            $constraints = [];
            foreach ($blockDefinitionAttribute->getConstraints() as $constraintIdentifier => $constraint) {
                $constraints[] = $this->constraintFactory->getConstraint($constraintIdentifier, $constraint);
            }

            $violations = $validator->validate($attribute->getValue(), $constraints);

            /** @var \Symfony\Component\Validator\ConstraintViolation $violation */
            foreach ($violations as $violation) {
                $validationErrors[] = new ValidationError(
                    "Value '%value%' for attribute '%attributeName%' in block '%blockName%' is invalid: %reason%",
                    null,
                    [
                        '%value%' => $violation->getInvalidValue(),
                        '%attributeName%' => $attribute->getName(),
                        '%blockName%' => $blockDefinition->getName(),
                        '%reason%' => $violation->getMessage(),
                    ]
                );
            }
        }

        return $validationErrors;
    }

    public function render(BlockContextInterface $blockContext, BlockValue $blockValue): string
    {
        $blockIdentifier = $blockValue->getType();

        $blockDefinition = $this->blockDefinitionFactory->getBlockDefinition($blockIdentifier);

        $errors = $this->validateBlock($blockValue, $blockDefinition);

        if (!empty($errors)) {
            return (string)array_shift($errors)->getTranslatableMessage();
        }

        $view = $blockDefinition->getViews()[$blockValue->getView()];

        $renderRequest = new TwigRenderRequest($view['template'], []);

        // Dispatching pre render event
        $preRenderEvent = new PreRenderEvent($blockContext, $blockValue, $renderRequest);
        $this->eventDispatcher->dispatch($preRenderEvent, BlockRenderEvents::GLOBAL_BLOCK_RENDER_PRE);
        $this->eventDispatcher->dispatch($preRenderEvent, BlockRenderEvents::getBlockPreRenderEventName($blockIdentifier));

        $renderedBlock = $this->renderer->render($preRenderEvent->getRenderRequest());

        // Dispatching post render event
        $postRenderEvent = new PostRenderEvent($blockContext, $blockValue, $renderedBlock);
        $this->eventDispatcher->dispatch($postRenderEvent, BlockRenderEvents::GLOBAL_BLOCK_RENDER_POST);
        $this->eventDispatcher->dispatch($postRenderEvent, BlockRenderEvents::getBlockPostRenderEventName($blockIdentifier));

        return $postRenderEvent->getRenderedBlock();
    }

    public function createBlockContextFromRequest(Request $request): BlockContextInterface
    {
        $blockContextEvent = new BlockContextEvent($request);
        $this->eventDispatcher->dispatch($blockContextEvent, BlockContextEvents::CREATE);

        $blockContext = $blockContextEvent->getBlockContext();

        if (null === $blockContext) {
            throw new BadStateException(
                '$request',
                sprintf(
                    "Couldn't create BlockContext from the Request."
                    . "Did you register the '%s' EventListener?",
                    BlockContextEvents::CREATE
                )
            );
        }

        return $blockContext;
    }

    /**
     * @return \Ibexa\Core\FieldType\ValidationError[]
     */
    private function validateBlockName(
        ValidatorInterface $validator,
        BlockValue $block,
        BlockDefinition $blockDefinition
    ): array {
        $validationErrors = [];
        $violations = $validator->validate(
            $block->getName(),
            [new Length(['max' => BlockServiceInterface::BLOCK_NAME_MAX_LENGTH])]
        );
        foreach ($violations as $violation) {
            $validationErrors[] = new ValidationError(
                "Value '%value%' in block '%blockName%' is invalid: %reason%",
                null,
                [
                    '%value%' => $violation->getInvalidValue(),
                    '%blockName%' => $blockDefinition->getName(),
                    '%reason%' => $violation->getMessage(),
                ]
            );
        }

        return $validationErrors;
    }
}

class_alias(BlockService::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Service\BlockService');
