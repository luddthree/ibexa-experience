<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Calendar\ArgumentResolver;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use Generator;
use Ibexa\Contracts\Calendar\Cursor;
use Ibexa\Contracts\Calendar\DateRange;
use Ibexa\Contracts\Calendar\EventQuery;
use Ibexa\Contracts\Calendar\EventQueryBuilder;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Rest\Server\Exceptions\BadRequestException;
use Ibexa\User\UserSetting\UserSettingService;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class EventQueryResolver implements ArgumentValueResolverInterface
{
    private const PARAM_START = 'start';
    private const PARAM_END = 'end';
    private const PARAM_COUNT = 'count';
    private const PARAM_CURSOR = 'cursor';
    private const PARAM_TYPES = 'types';
    private const PARAM_LANGUAGES = 'languages';

    private const LIST_DELIMITER = ',';

    /** @var \Ibexa\Contracts\Core\Repository\LanguageService */
    private $languageService;

    /** @var \Ibexa\User\UserSetting\UserSettingService */
    private $userSettingService;

    public function __construct(
        LanguageService $languageService,
        UserSettingService $userSettingService
    ) {
        $this->languageService = $languageService;
        $this->userSettingService = $userSettingService;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === EventQuery::class;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        $parameters = $request->query;
        $timezone = new DateTimeZone($this->userSettingService->getUserSetting('timezone')->value);

        $query = new EventQueryBuilder();
        $query->withCount($parameters->getInt(self::PARAM_COUNT, EventQuery::DEFAULT_COUNT));
        $query->withDateRange(new DateRange(
            $this->getDateTimeParam($parameters, self::PARAM_START, $timezone),
            $this->getDateTimeParam($parameters, self::PARAM_END, $timezone)
        ));

        if ($parameters->has(self::PARAM_CURSOR)) {
            $query->withCursor(Cursor::fromString($parameters->get(self::PARAM_CURSOR)));
        }

        if ($parameters->has(self::PARAM_TYPES)) {
            $query->withTypes($this->getStringListParam($parameters, self::PARAM_TYPES));
        }

        if ($parameters->has(self::PARAM_LANGUAGES)) {
            $languages = $this->languageService->loadLanguageListByCode(
                $this->getStringListParam($parameters, self::PARAM_LANGUAGES)
            );

            $query->withLanguages($languages);
        }

        yield $query->getQuery();
    }

    /**
     * Returns the parameter value converted to \DateTimeInterface.
     *
     * Excepted format is timestamp.
     *
     * @throws \Ibexa\Rest\Server\Exceptions\BadRequestException when parameter is missing or format is invalid
     */
    private function getDateTimeParam(
        ParameterBag $parameters,
        string $name,
        ?DateTimeZone $timezone = null
    ): DateTimeInterface {
        if (!$parameters->has($name)) {
            throw new BadRequestException("'$name' parameter is required.");
        }

        try {
            $datetime = new DateTimeImmutable('@' . $parameters->getInt($name));

            if ($timezone !== null) {
                $datetime = $datetime->setTimezone($timezone);
            }

            return $datetime;
        } catch (Exception $e) {
            throw new BadRequestException("'$name' parameter format is invalid.");
        }
    }

    /**
     * Returns the parameter value converted to string list with comma as elements separator.
     *
     * @param \Symfony\Component\HttpFoundation\ParameterBag $parameters
     * @param string $name
     *
     * @return string[]
     */
    private function getStringListParam(ParameterBag $parameters, string $name): array
    {
        return explode(self::LIST_DELIMITER, $parameters->get($name));
    }
}

class_alias(EventQueryResolver::class, 'EzSystems\EzPlatformCalendarBundle\ArgumentResolver\EventQueryResolver');
