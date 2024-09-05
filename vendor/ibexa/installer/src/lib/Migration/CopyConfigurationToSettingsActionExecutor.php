<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Installer\Migration;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter\ContentFilteringAdapter;
use Ibexa\Contracts\Core\Repository\SettingService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Ibexa\Contracts\Core\Repository\Values\Setting\Setting;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\FieldType\TextBlock\Value;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject\Step\Action;
use Symfony\Component\Yaml\Yaml;

final class CopyConfigurationToSettingsActionExecutor implements ExecutorInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\SettingService */
    private $settingService;

    /** @var int */
    private $batchSize;

    public function __construct(
        ContentService $contentService,
        SettingService $settingService,
        int $batchSize = BatchIterator::DEFAULT_BATCH_SIZE
    ) {
        $this->contentService = $contentService;
        $this->settingService = $settingService;
        $this->batchSize = $batchSize;
    }

    /**
     * @param \Ibexa\Installer\Migration\CopyConfigurationToSettingsAction $action
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     */
    public function handle(Action $action, ValueObject $contentType): void
    {
        $settings = $this->getCommerceSettings();

        $mergedConfiguration = $settings->value;
        foreach ($this->findContentObjectByType($contentType) as $content) {
            $fieldValue = $content->getFieldValue($action->getValue());

            if ($fieldValue instanceof Value) {
                $yaml = $fieldValue->text;
                if (empty($yaml)) {
                    continue;
                }

                $configurationList = Yaml::parse($yaml);
                $configurationValidList = $configurationList['parameters'] ?? $configurationList;

                foreach ($configurationValidList as $key => $value) {
                    $mergedConfiguration[$key] = $value;
                }
            }
        }

        if (!empty($mergedConfiguration)) {
            $updateStruct = $this->settingService->newSettingUpdateStruct();
            $updateStruct->setValue($mergedConfiguration);

            $this->settingService->updateSetting($settings, $updateStruct);
        }
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content[]
     */
    private function findContentObjectByType(ContentType $contentType): iterable
    {
        $filter = new Filter();
        $filter->withCriterion(new ContentTypeIdentifier($contentType->identifier));

        return new BatchIterator(new ContentFilteringAdapter($this->contentService, $filter), $this->batchSize);
    }

    private function getCommerceSettings(): Setting
    {
        try {
            return $this->settingService->loadSetting('commerce', 'config');
        } catch (NotFoundException $e) {
            $createStruct = $this->settingService->newSettingCreateStruct();
            $createStruct->setGroup('commerce');
            $createStruct->setIdentifier('config');
            $createStruct->setValue([]);

            return $this->settingService->createSetting($createStruct);
        }
    }
}

class_alias(CopyConfigurationToSettingsActionExecutor::class, 'Ibexa\Platform\Installer\Migration\CopyConfigurationToSettingsActionExecutor');
