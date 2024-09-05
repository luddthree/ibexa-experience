<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\BlockAttribute;

use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Personalization\Form\DataTransformer\TargetedScenarioAttributeMapTransformer;
use Ibexa\Personalization\PageBlock\DataProvider\OutputType\OutputTypeDataProviderInterface;
use Ibexa\Personalization\Security\Service\SecurityServiceInterface;
use Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface;
use Ibexa\Personalization\Value\Content\ItemTypeList;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Serializer\SerializerInterface;

final class TargetedScenarioMapAttributeType extends AbstractType implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private OutputTypeDataProviderInterface $outputTypeDataProvider;

    private ScenarioServiceInterface $scenarioService;

    private SecurityServiceInterface $securityService;

    private SegmentationServiceInterface $segmentationService;

    private SerializerInterface $serializer;

    public function __construct(
        OutputTypeDataProviderInterface $outputTypeDataProvider,
        ScenarioServiceInterface $scenarioService,
        SecurityServiceInterface $securityService,
        SegmentationServiceInterface $segmentationService,
        SerializerInterface $serializer,
        ?LoggerInterface $logger = null
    ) {
        $this->outputTypeDataProvider = $outputTypeDataProvider;
        $this->scenarioService = $scenarioService;
        $this->securityService = $securityService;
        $this->segmentationService = $segmentationService;
        $this->serializer = $serializer;
        $this->logger = $logger ?? new NullLogger();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(
            new TargetedScenarioAttributeMapTransformer(
                $this->scenarioService,
                $this->securityService,
                $this->segmentationService,
                $this->serializer
            )
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attr']['data-segments'] = json_encode($this->getSegmentsMap(), JSON_THROW_ON_ERROR);
        $view->vars['attr']['data-scenario-list'] = json_encode($this->getScenarioListMap(), JSON_THROW_ON_ERROR);
        $view->vars['attr']['data-output-type-list'] = $this->serializer->serialize($this->getOutputTypeListMap(), 'json');
    }

    public function getParent(): ?string
    {
        return TextType::class;
    }

    public function getBlockPrefix(): ?string
    {
        return 'personalization_segment_scenario_map';
    }

    /**
     * @return array<array{
     *     'id': int,
     *     'name': string,
     *     'segments': array<array{
     *          'id': int,
     *          'name': string,
     *      }>,
     * }>
     */
    private function getSegmentsMap(): array
    {
        $segmentsMap = [];
        foreach ($this->segmentationService->loadSegmentGroups() as $group) {
            $groupData = [
                'id' => $group->id,
                'name' => $group->name,
                'segments' => [],
            ];

            foreach ($this->segmentationService->loadSegmentsAssignedToGroup($group) as $segment) {
                $groupData['segments'][] = [
                    'id' => $segment->id,
                    'name' => $segment->name,
                ];
            }

            $segmentsMap[] = $groupData;
        }

        return $segmentsMap;
    }

    /**
     * @return array<array{
     *     'referenceCode': string,
     *     'title': string,
     * }>
     */
    private function getScenarioListMap(): array
    {
        $customerId = $this->getCustomerId();
        if (null === $customerId) {
            return [];
        }

        $scenarioListMap = [];
        $scenarioList = $this->scenarioService->getScenarioList($customerId);

        /** @var \Ibexa\Personalization\Value\Scenario\Scenario $scenario */
        foreach ($scenarioList as $scenario) {
            $scenarioListMap[] = [
                'referenceCode' => $scenario->getReferenceCode(),
                'title' => $scenario->getTitle(),
                'supportedOutputTypes' => $scenario->getOutputItemTypes()->getItemTypesDescriptions(),
            ];
        }

        return $scenarioListMap;
    }

    private function getOutputTypeListMap(): ItemTypeList
    {
        $customerId = $this->getCustomerId();
        if (null === $customerId) {
            return new ItemTypeList([]);
        }

        $providedOutputTypes = $this->outputTypeDataProvider->getOutputTypes($customerId);
        $outputTypeList = [];
        foreach ($providedOutputTypes as $outputType) {
            $outputTypeList[] = $outputType;
        }

        return new ItemTypeList($outputTypeList);
    }

    private function getCustomerId(): ?int
    {
        $customerId = $this->securityService->getCurrentCustomerId();
        if (null === $customerId) {
            $this->logger->warning('Customer id is not configured');

            return null;
        }

        return $customerId;
    }
}
