<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Language;

use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Migration\Generator\Exception\UnknownMatchPropertyException;
use Ibexa\Migration\Generator\MigrationGeneratorInterface;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface;
use Webmozart\Assert\Assert;

final class LanguageMigrationGenerator implements MigrationGeneratorInterface
{
    private const TYPE_LANGUAGE = 'language';

    private const WILDCARD = '*';
    private const SUPPORTED_MATCH_PROPERTY = [
        'language_code' => true,
    ];

    /** @var \Ibexa\Contracts\Core\Repository\LanguageService */
    private $languageService;

    /** @var \Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface */
    private $languageStepFactory;

    public function __construct(
        LanguageService $languageService,
        StepFactoryInterface $languageStepFactory
    ) {
        $this->languageService = $languageService;
        $this->languageStepFactory = $languageStepFactory;
    }

    public function supports(string $migrationType, Mode $migrationMode): bool
    {
        return $migrationType === $this->getSupportedType()
            && \in_array($migrationMode->getMode(), $this->getSupportedModes(), true);
    }

    public function getSupportedType(): string
    {
        return self::TYPE_LANGUAGE;
    }

    /**
     * @return string[]
     */
    public function getSupportedModes(): array
    {
        return $this->languageStepFactory->getSupportedModes();
    }

    /**
     * @return \Ibexa\Migration\ValueObject\Step\StepInterface[]
     */
    public function generate(Mode $migrationMode, array $context): iterable
    {
        Assert::keyExists($context, 'value');
        Assert::notEmpty($context['value']);
        Assert::keyExists($context, 'match-property');

        $matchProperty = $context['match-property'];
        $value = $context['value'];

        Assert::allString($value);

        $languages = $this->getLanguages($matchProperty, $value);
        Assert::allIsInstanceOf($languages, Language::class);

        foreach ($languages as $language) {
            yield $this->languageStepFactory->create($language, $migrationMode);
        }
    }

    /**
     * @param array<string> $value
     *
     * @return iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Language>
     */
    private function getLanguages(?string $matchProperty, array $value): iterable
    {
        if (in_array(self::WILDCARD, $value, true)) {
            return $this->languageService->loadLanguages();
        }

        if ($matchProperty !== null && array_key_exists($matchProperty, self::SUPPORTED_MATCH_PROPERTY) === false) {
            throw new UnknownMatchPropertyException($matchProperty, self::SUPPORTED_MATCH_PROPERTY);
        }

        return $this->languageService->loadLanguageListByCode($value);
    }
}

class_alias(LanguageMigrationGenerator::class, 'Ibexa\Platform\Migration\Generator\Language\LanguageMigrationGenerator');
