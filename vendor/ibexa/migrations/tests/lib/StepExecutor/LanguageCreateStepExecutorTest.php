<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\StepExecutor\LanguageCreateStepExecutor;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\ResolverInterface;
use Ibexa\Migration\ValueObject\Language\Metadata;
use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\Migration\ValueObject\Step\LanguageCreateStep;

/**
 * @covers \Ibexa\Migration\StepExecutor\LanguageCreateStepExecutor
 */
final class LanguageCreateStepExecutorTest extends AbstractInitializedStepExecutorTest
{
    public function testHandle(): void
    {
        $languageService = self::getLanguageService();

        $found = true;
        try {
            $languageService->loadLanguage('pol-PL');
        } catch (NotFoundException $e) {
            $found = false;
        }

        self::assertFalse($found);

        $step = new LanguageCreateStep(
            new Metadata(
                'pol-PL',
                'Polski',
                false
            ),
            [],
            [
                $this->createMock(Action::class),
            ]
        );

        $executor = new LanguageCreateStepExecutor(
            self::getLanguageService(),
            $this->createMock(ExecutorInterface::class),
        );

        $this->configureExecutor($executor, [
            ResolverInterface::class => self::getReferenceResolver('language'),
        ]);

        self::assertTrue($executor->canHandle($step));

        $executor->handle($step);

        $language = $languageService->loadLanguage('pol-PL');

        self::assertSame('Polski', $language->name);
        self::assertSame('pol-PL', $language->languageCode);
        self::assertFalse($language->enabled);
    }
}

class_alias(LanguageCreateStepExecutorTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\LanguageCreateStepExecutorTest');
