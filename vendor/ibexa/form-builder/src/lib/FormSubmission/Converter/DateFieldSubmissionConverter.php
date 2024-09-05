<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FormSubmission\Converter;

use DateTime;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Form;
use Ibexa\User\UserSetting\UserSettingService;
use IntlDateFormatter;
use IntlTimeZone;
use Locale;
use Twig\Environment;

class DateFieldSubmissionConverter extends GenericFieldSubmissionConverter
{
    /** @var \Ibexa\AdminUi\UserSetting\UserSettingService */
    private $userSettingService;

    /** @var \Twig\Environment */
    private $environment;

    /**
     * @param string $typeIdentifier
     * @param \Twig\Environment  $twig
     * @param \Ibexa\User\UserSetting\UserSettingService $userSettingService
     * @param \Twig\Environment $environment
     */
    public function __construct(
        string $typeIdentifier,
        Environment $twig,
        UserSettingService $userSettingService,
        Environment $environment
    ) {
        parent::__construct($typeIdentifier, $twig);

        $this->userSettingService = $userSettingService;
        $this->environment = $environment;
    }

    /**
     * @param string $persistenceValue
     *
     * @return mixed
     */
    public function fromPersistenceValue(string $persistenceValue)
    {
        if (empty($persistenceValue)) {
            return null;
        }

        return DateTime::createFromFormat('U', $persistenceValue);
    }

    /**
     * @param \DateTime $fieldValue
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Field $field
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Form $form
     *
     * @return string
     */
    public function toPersistenceValue($fieldValue, Field $field, Form $form): string
    {
        if (empty($fieldValue)) {
            return '';
        }

        return (string)$fieldValue->getTimestamp();
    }

    /**
     * @param \DateTime $fieldValue
     * @param string|null $languageCode
     *
     * @return string
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function toDisplayValue($fieldValue, string $languageCode = null): string
    {
        if (empty($fieldValue)) {
            return '';
        }

        $timezone = $this->userSettingService->getUserSetting('timezone');
        $date = twig_date_converter($this->environment, $fieldValue, $timezone->value);

        $formatter = IntlDateFormatter::create(
            Locale::getDefault(),
            IntlDateFormatter::MEDIUM,
            IntlDateFormatter::NONE,
            IntlTimeZone::createTimeZone($date->getTimezone()->getName()),
            IntlDateFormatter::GREGORIAN
        );

        return $formatter->format($date->getTimestamp());
    }

    /**
     * @param \DateTime $fieldValue
     *
     * @return string
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function toExportValue($fieldValue): string
    {
        return (string)$this->toDisplayValue($fieldValue);
    }
}

class_alias(DateFieldSubmissionConverter::class, 'EzSystems\EzPlatformFormBuilder\FormSubmission\Converter\DateFieldSubmissionConverter');
