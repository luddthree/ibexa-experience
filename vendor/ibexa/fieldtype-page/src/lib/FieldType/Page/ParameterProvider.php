<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page;

use DateTimeImmutable;
use DateTimeInterface;
use Ibexa\Bundle\PageBuilder\Controller\PageController;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Core\MVC\Symfony\FieldType\View\ParameterProviderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ParameterProvider implements ParameterProviderInterface
{
    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    public function __construct(
        RequestStack $requestStack,
        LocationService $locationService
    ) {
        $this->requestStack = $requestStack;
        $this->locationService = $locationService;
    }

    /**
     * Returns a hash of parameters to inject to the associated fieldtype's view template.
     * Returned parameters will only be available for associated field type.
     *
     * Key is the parameter name (the variable name exposed in the template, in the 'parameters' array).
     * Value is the parameter's value.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Field $field The field parameters are provided for.
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getViewParameters(Field $field)
    {
        $masterRequest = $this->requestStack->getMainRequest();
        $pagePreviewParameters = $masterRequest->query->get('page_preview', []);
        $inEditorialMode = $this->inEditorialMode();

        $referenceDateTime = $inEditorialMode && isset($pagePreviewParameters['reference_timestamp'])
            ? DateTimeImmutable::createFromFormat('U', $pagePreviewParameters['reference_timestamp'])
            : null;

        $this->filterInvisibleBlocks($field, $inEditorialMode, $referenceDateTime);

        return array_merge(
            $this->getLocationViewParameters(),
            $this->getEditorialModeViewParameters($referenceDateTime)
        );
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Field $field
     * @param bool $isEditorialMode
     * @param \DateTimeInterface|null $referenceDateTime
     *
     * @throws \Exception
     *
     * @todo This is really dangerous as we are modifying living object by removing blocks.
     *       It leads to unexpected results if other parts of the application are relying on the Page object.
     */
    private function filterInvisibleBlocks(
        Field $field,
        bool $isEditorialMode,
        ?DateTimeInterface $referenceDateTime
    ): void {
        /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Value $field->value */
        $page = $field->value->getPage();

        /** @var \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page $page */
        foreach ($page->getZones() as $zone) {
            $blocks = [];
            foreach ($zone->getBlocks() as $block) {
                $isVisible = $block->isVisible($referenceDateTime);

                if ($isVisible || $isEditorialMode) {
                    $blocks[] = $block;
                }
            }
            $zone->setBlocks($blocks);
        }
    }

    /**
     * @return array<string, \Ibexa\Contracts\Core\Repository\Values\Content\Location|null>
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function getLocationViewParameters(): array
    {
        $mainRequest = $this->requestStack->getMainRequest();
        $location = $mainRequest->attributes->get('location');
        $locationId = $mainRequest->attributes->get('locationId');
        $locationId = $locationId !== null ? (int)$locationId : null;

        $location = $location === null && $locationId !== null
            ? $this->locationService->loadLocation($locationId, Language::ALL)
            : $location;

        return [
            'location' => $location,
        ];
    }

    /**
     * @param \DateTimeInterface|null $referenceDateTime
     *
     * @return array
     *
     * @throws \Exception
     */
    private function getEditorialModeViewParameters(?DateTimeInterface $referenceDateTime): array
    {
        return [
            'editorial_mode' => $this->inEditorialMode(),
            'reference_date_time' => $referenceDateTime,
        ];
    }

    /**
     * @return bool
     *
     * @throws \Exception
     */
    private function inEditorialMode(): bool
    {
        $masterRequest = $this->requestStack->getMainRequest();

        return (bool)$masterRequest->attributes->get(PageController::EDITORIAL_MODE_PARAMETER, false);
    }
}

class_alias(ParameterProvider::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\ParameterProvider');
