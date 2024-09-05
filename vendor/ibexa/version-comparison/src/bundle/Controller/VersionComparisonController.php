<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\VersionComparison\Controller;

use Ibexa\Bundle\AdminUi\ParamConverter\LanguageParamConverter;
use Ibexa\Bundle\VersionComparison\Form\Data\VersionComparisonData;
use Ibexa\Bundle\VersionComparison\Form\Type\VersionComparisonType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\VersionComparison\Service\VersionComparisonService;
use Ibexa\VersionComparison\UI\FieldDefinitionGroups;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class VersionComparisonController extends Controller
{
    private const ROUTE_PARAMETER_VERSION_NO_A = 'versionNoA';
    private const ROUTE_PARAMETER_VERSION_NO_A_LANG = 'versionALanguageCode';
    private const ROUTE_PARAMETER_CONTENT_INFO_ID = 'contentInfoId';
    private const ROUTE_PARAMETER_VERSION_NO_B = 'versionNoB';
    private const ROUTE_PARAMETER_VERSION_NO_B_LANG = 'versionBLanguageCode';
    private const ROUTE_COMPARISON_SPLIT = 'ibexa.version.compare.split';
    private const ROUTE_COMPARISON_UNIFY = 'ibexa.version.compare.unified';

    /** @var \Ibexa\VersionComparison\Service\VersionComparisonService; */
    private $comparisonService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\VersionComparison\UI\FieldDefinitionGroups */
    private $fieldDefinitionGroups;

    /** @var \Symfony\Component\Form\FormFactoryInterface */
    private $formFactory;

    public function __construct(
        VersionComparisonService $comparisonService,
        ContentService $contentService,
        FieldDefinitionGroups $fieldDefinitionGroups,
        FormFactoryInterface $formFactory
    ) {
        $this->comparisonService = $comparisonService;
        $this->contentService = $contentService;
        $this->fieldDefinitionGroups = $fieldDefinitionGroups;
        $this->formFactory = $formFactory;
    }

    public function sideBySideCompareAction(
        ContentInfo $contentInfo,
        int $versionNoA,
        ?int $versionNoB = null,
        ?Language $language = null
    ): RedirectResponse {
        return $this->redirectToRoute(
            self::ROUTE_COMPARISON_SPLIT,
            $this->getRedirectParams($contentInfo, $versionNoA, $language, $versionNoB),
            Response::HTTP_MOVED_PERMANENTLY
        );
    }

    public function splitAction(
        ContentInfo $contentInfo,
        int $versionNoA,
        string $versionALanguageCode,
        ?int $versionNoB = null,
        ?string $versionBLanguageCode = null,
        ?Language $language = null
    ): Response {
        if ($versionNoA === $versionNoB && $versionALanguageCode === $versionBLanguageCode) {
            return $this->redirectToRoute(
                self::ROUTE_COMPARISON_SPLIT,
                [
                    self::ROUTE_PARAMETER_CONTENT_INFO_ID => $contentInfo->id,
                    self::ROUTE_PARAMETER_VERSION_NO_A => $versionNoA,
                    self::ROUTE_PARAMETER_VERSION_NO_A_LANG => $versionALanguageCode,
                    LanguageParamConverter::PARAMETER_LANGUAGE_CODE => $this->getLanguageCode($language),
                ]
            );
        }

        $versionInfoA = $this->contentService->loadVersionInfo($contentInfo, $versionNoA);

        if (!in_array($versionALanguageCode, $versionInfoA->languageCodes, true)) {
            throw $this->createNotFoundException(
                sprintf(
                    'LanguageCode: %s not found for VersionNo: %s',
                    $versionALanguageCode,
                    $versionInfoA->versionNo
                )
            );
        }

        $contentA = $this->contentService->loadContent(
            $contentInfo->id,
            [$versionALanguageCode],
            $versionNoA
        );

        if (
            $language !== null && (($language->getLanguageCode() !== $versionALanguageCode)
            || ($versionBLanguageCode !== null && $language->getLanguageCode() !== $versionBLanguageCode))
        ) {
            $correctLanguageVersionNo = $this->getCorrectLanguageVersionNo($contentInfo, $language);

            return $this->redirectToRoute(
                self::ROUTE_COMPARISON_SPLIT,
                [
                    self::ROUTE_PARAMETER_CONTENT_INFO_ID => $contentInfo->id,
                    self::ROUTE_PARAMETER_VERSION_NO_A => $correctLanguageVersionNo,
                    self::ROUTE_PARAMETER_VERSION_NO_A_LANG => $language->languageCode,
                    LanguageParamConverter::PARAMETER_LANGUAGE_CODE => $language->languageCode,
                ]
            );
        }

        $contentB = null;
        $versionInfoB = null;
        $contentAFieldDefinitionsByGroup = null;
        $versionDiff = null;
        if ($versionNoB) {
            $versionInfoB = $this->contentService->loadVersionInfo($contentInfo, $versionNoB);
            $contentB = $this->contentService->loadContent(
                $contentInfo->id,
                [$versionBLanguageCode],
                $versionNoB
            );

            if ($versionBLanguageCode === $versionALanguageCode) {
                $versionDiff = $this->comparisonService->compare(
                    $versionInfoB,
                    $versionInfoA,
                    $versionALanguageCode
                );
                $contentAFieldDefinitionsByGroup = $this->fieldDefinitionGroups->groupFieldDefinitionsDiff(
                    $versionDiff
                );
            }
        }

        $selectVersionsForm = $this->getForm(
            $contentInfo,
            $versionInfoA,
            $versionALanguageCode,
            $versionInfoB,
            $versionBLanguageCode,
            $language,
        );

        return $this->render(
            '@ibexadesign/version_comparison/side_by_side.html.twig',
            [
                'version_diff' => $versionDiff,
                'content_a' => $contentA,
                'content_b' => $contentB,
                'field_definitions_by_group' => $contentAFieldDefinitionsByGroup,
                'select_versions_form' => $selectVersionsForm->createView(),
            ]
        );
    }

    public function compareAction(
        ContentInfo $contentInfo,
        int $versionNoA,
        ?int $versionNoB = null,
        ?Language $language = null
    ): RedirectResponse {
        return $this->redirectToRoute(
            self::ROUTE_COMPARISON_UNIFY,
            $this->getRedirectParams($contentInfo, $versionNoA, $language, $versionNoB),
            Response::HTTP_MOVED_PERMANENTLY
        );
    }

    public function unifiedAction(
        ContentInfo $contentInfo,
        int $versionNoA,
        string $versionALanguageCode,
        ?int $versionNoB = null,
        ?string $versionBLanguageCode = null,
        ?Language $language = null
    ) {
        if ($versionNoA === $versionNoB && $versionALanguageCode === $versionBLanguageCode) {
            return $this->redirectToRoute(
                self::ROUTE_COMPARISON_UNIFY,
                [
                    self::ROUTE_PARAMETER_CONTENT_INFO_ID => $contentInfo->id,
                    self::ROUTE_PARAMETER_VERSION_NO_A => $versionNoA,
                    self::ROUTE_PARAMETER_VERSION_NO_A_LANG => $versionALanguageCode,
                    LanguageParamConverter::PARAMETER_LANGUAGE_CODE => $this->getLanguageCode($language),
                ]
            );
        }

        $versionInfoA = $this->contentService->loadVersionInfo($contentInfo, $versionNoA);

        if (!in_array($versionALanguageCode, $versionInfoA->languageCodes, true)) {
            throw $this->createNotFoundException(
                sprintf(
                    'LanguageCode: %s not found for VersionNo: %s',
                    $versionALanguageCode,
                    $versionInfoA->versionNo
                )
            );
        }

        if (
            $language !== null && (($language->getLanguageCode() !== $versionALanguageCode)
            || ($versionBLanguageCode !== null && $language->getLanguageCode() !== $versionBLanguageCode))
        ) {
            $correctLanguageVersionNo = $this->getCorrectLanguageVersionNo($contentInfo, $language);

            return $this->redirectToRoute(
                self::ROUTE_COMPARISON_UNIFY,
                [
                    self::ROUTE_PARAMETER_CONTENT_INFO_ID => $contentInfo->id,
                    self::ROUTE_PARAMETER_VERSION_NO_A => $correctLanguageVersionNo,
                    self::ROUTE_PARAMETER_VERSION_NO_A_LANG => $language->languageCode,
                    LanguageParamConverter::PARAMETER_LANGUAGE_CODE => $language->languageCode,
                ]
            );
        }

        $contentB = null;
        $versionInfoB = null;

        if ($versionNoB !== null) {
            $versionInfoB = $this->contentService->loadVersionInfo($contentInfo, $versionNoB);
            $contentB = $this->contentService->loadContent(
                $contentInfo->id,
                [$versionBLanguageCode],
                $versionNoB
            );

            if ($versionALanguageCode !== $versionBLanguageCode) {
                return $this->redirectToRoute(
                    self::ROUTE_COMPARISON_SPLIT,
                    [
                        self::ROUTE_PARAMETER_CONTENT_INFO_ID => $contentInfo->id,
                        self::ROUTE_PARAMETER_VERSION_NO_A => $versionNoA,
                        self::ROUTE_PARAMETER_VERSION_NO_A_LANG => $versionALanguageCode,
                        self::ROUTE_PARAMETER_VERSION_NO_B => $versionNoB,
                        self::ROUTE_PARAMETER_VERSION_NO_B_LANG => $versionBLanguageCode,
                        LanguageParamConverter::PARAMETER_LANGUAGE_CODE => $this->getLanguageCode($language),
                    ]
                );
            }
        }

        $versionDiff = $this->comparisonService->compare(
            $versionInfoA,
            $versionInfoB ?? $versionInfoA,
            $versionALanguageCode
        );

        $contentA = $this->contentService->loadContentByVersionInfo($versionInfoA, [$versionALanguageCode]);
        $contentAFieldDefinitionsByGroup = $this->fieldDefinitionGroups->groupFieldDefinitionsDiff($versionDiff);

        $selectVersionsForm = $this->getForm(
            $contentInfo,
            $versionInfoA,
            $versionALanguageCode,
            $versionInfoB,
            $versionBLanguageCode,
            $language,
        );

        return $this->render(
            '@ibexadesign/version_comparison/single.html.twig',
            [
                'version_diff' => $versionDiff,
                'content_a' => $contentA,
                'content_b' => $contentB,
                'field_definitions_by_group' => $contentAFieldDefinitionsByGroup,
                'select_versions_form' => $selectVersionsForm->createView(),
            ]
        );
    }

    private function getForm(
        ContentInfo $contentInfo,
        VersionInfo $versionInfoA,
        string $versionALanguageCode,
        ?VersionInfo $versionInfoB,
        ?string $versionBLanguageCode,
        ?Language $language
    ): FormInterface {
        $data = new VersionComparisonData(
            $versionInfoA,
            $versionALanguageCode,
            $versionInfoB,
            $versionBLanguageCode,
            $language
        );

        return $this->formFactory->create(
            VersionComparisonType::class,
            $data,
            [
                'content_info' => $contentInfo,
            ]
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function getCorrectLanguageVersionNo(
        ContentInfo $contentInfo,
        Language $language
    ): int {
        $versions = $this->contentService->loadVersions($contentInfo);
        foreach ($versions as $version) {
            foreach ($version->getLanguages() as $versionLanguage) {
                if ($versionLanguage->getLanguageCode() === $language->getLanguageCode()) {
                    return $version->versionNo;
                }
            }
        }

        throw $this->createNotFoundException();
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function getRedirectParams(
        ContentInfo $contentInfo,
        int $versionNoA,
        ?Language $language,
        ?int $versionNoB
    ): array {
        $versionInfoA = $this->contentService->loadVersionInfo($contentInfo, $versionNoA);

        $routeParams = [
            self::ROUTE_PARAMETER_CONTENT_INFO_ID => $contentInfo->id,
            self::ROUTE_PARAMETER_VERSION_NO_A => $versionNoA,
            self::ROUTE_PARAMETER_VERSION_NO_A_LANG => $versionInfoA->initialLanguageCode,
            LanguageParamConverter::PARAMETER_LANGUAGE_CODE => $this->getLanguageCode($language),
        ];

        if (null !== $versionNoB) {
            $versionInfoB = $this->contentService->loadVersionInfo($contentInfo, $versionNoB);

            $routeParams = array_merge($routeParams, [
                self::ROUTE_PARAMETER_VERSION_NO_B => $versionNoB,
                self::ROUTE_PARAMETER_VERSION_NO_B_LANG => $versionInfoB->initialLanguageCode,
            ]);
        }

        return $routeParams;
    }

    private function getLanguageCode(?Language $language): ?string
    {
        return $language ? $language->languageCode : null;
    }
}

class_alias(VersionComparisonController::class, 'EzSystems\EzPlatformVersionComparisonBundle\Controller\VersionComparisonController');
