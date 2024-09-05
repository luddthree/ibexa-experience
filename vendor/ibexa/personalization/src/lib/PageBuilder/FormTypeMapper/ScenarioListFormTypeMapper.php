<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\PageBuilder\FormTypeMapper;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Attribute\FormTypeMapper\AttributeFormTypeMapperInterface;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\Personalization\Form\Type\BlockAttribute\ScenarioListAttributeType;
use Ibexa\Personalization\PageBlock\DataProvider\Scenario\ScenarioDataProviderInterface;
use Ibexa\Personalization\Security\Service\SecurityServiceInterface;
use Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;

final class ScenarioListFormTypeMapper implements AttributeFormTypeMapperInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private ScenarioDataProviderInterface $scenarioDataProvider;

    private ScenarioServiceInterface $scenarioService;

    private SecurityServiceInterface $securityService;

    public function __construct(
        ScenarioDataProviderInterface $scenarioDataProvider,
        ScenarioServiceInterface $scenarioService,
        SecurityServiceInterface $securityService,
        ?LoggerInterface $logger = null
    ) {
        $this->scenarioDataProvider = $scenarioDataProvider;
        $this->scenarioService = $scenarioService;
        $this->securityService = $securityService;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param array<\Symfony\Component\Validator\Constraint> $constraints
     */
    public function map(
        FormBuilderInterface $formBuilder,
        BlockDefinition $blockDefinition,
        BlockAttributeDefinition $blockAttributeDefinition,
        array $constraints = []
    ): FormBuilderInterface {
        $customerId = $this->securityService->getCurrentCustomerId();
        if (null === $customerId) {
            $this->logger->warning('Customer id is not configured');

            return $formBuilder->create(
                'value',
                ScenarioListAttributeType::class,
            );
        }

        $scenarioType = $blockAttributeDefinition->getOptions()['scenario_type'] ?? null;
        $scenarioList = null !== $scenarioType
            ? $this->scenarioService->getScenarioListByScenarioType($customerId, $scenarioType)
            : $this->scenarioService->getScenarioList($customerId);

        return $formBuilder->create(
            'value',
            ScenarioListAttributeType::class,
            [
                'constraints' => $constraints,
                'choice_loader' => ChoiceList::lazy(
                    new ScenarioListAttributeType(),
                    function () use ($customerId, $scenarioType): array {
                        return $this->scenarioDataProvider->getScenarios($customerId, $scenarioType);
                    },
                    $customerId
                ),
                'choice_attr' => static function (string $referenceCode) use ($scenarioList): array {
                    $scenario = $scenarioList->findByReferenceCode($referenceCode);

                    return [
                        'data-supported-output-types' => json_encode(
                            $scenario->getOutputItemTypes()->getItemTypesDescriptions(),
                            JSON_THROW_ON_ERROR
                        ),
                    ];
                },
            ]
        );
    }
}
