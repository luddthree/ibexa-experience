<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Scenario;

use Ibexa\Contracts\Core\Repository\Exceptions\BadStateException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioExcludedCategoriesData;
use Ibexa\Personalization\Form\DataTransformer\CategoryPathDataTransformer;
use InvalidArgumentException;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ScenarioExcludedCategoriesType extends AbstractType
{
    private LocationService $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        /** @var \Ibexa\Personalization\Form\Data\Scenario\ScenarioExcludedCategoriesData|null $formData */
        $formData = $form->getData();
        if (null === $formData) {
            return;
        }

        $formPaths = $formData->getPaths();
        if (empty($formPaths)) {
            return;
        }

        $paths = [];
        foreach ($formPaths as $path) {
            $paths[$path] = $this->getLocationsPath($path) ?? $path;
        }

        $view->vars += [
            'paths' => $paths,
        ];
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('enabled', CheckboxType::class, [
                'translation_domain' => 'ibexa_personalization',
                'label' => /** @Desc("Exclude categories") */ 'scenario.exclusions.exclude_categories',
            ])
            ->add('paths', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'delete_empty' => true,
                'required' => false,
            ]);
        $builder->addModelTransformer(new CategoryPathDataTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ScenarioExcludedCategoriesData::class,
        ]);
    }

    private function getLocationsPath(string $categoryPath): ?string
    {
        if (!$this->isFromContentRepository($categoryPath)) {
            return null;
        }

        $path = '';
        $locationIds = $this->getLocationIds($categoryPath);
        $locationList = $this->locationService->loadLocationList($locationIds);
        foreach ($locationList as $location) {
            $path .= $location->contentInfo->name;

            if (next($locationList)) {
                $path .= ' / ';
            }
        }

        return $path;
    }

    private function isFromContentRepository(string $categoryPath): bool
    {
        try {
            $filter = new Filter(new Subtree([$categoryPath]));
            $filter->withLimit(1);
            $result = $this->locationService->find($filter);
            $location = current($result->locations);

            return false !== $location;
        } catch (InvalidArgumentException | BadStateException $exception) {
            return false;
        }
    }

    /**
     * @return array<int>
     */
    private function getLocationIds(string $categoryPath): array
    {
        $locationIds = array_map(
            'intval',
            explode('/', trim($categoryPath, '/'))
        );

        array_shift($locationIds);

        return $locationIds;
    }
}
