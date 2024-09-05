<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\Reference\CollectorInterface;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\ResolverInterface;
use Ibexa\Migration\ValueObject\Step\ActionsAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\LanguageCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Webmozart\Assert\Assert;

final class LanguageCreateStepExecutor extends AbstractStepExecutor implements StepExecutorInterface, LoggerAwareInterface, ServiceSubscriberInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Contracts\Core\Repository\LanguageService */
    private $languageService;

    /** @var \Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface */
    private $actionExecutor;

    public function __construct(
        LanguageService $languageService,
        ExecutorInterface $actionExecutor,
        ?LoggerInterface $logger = null
    ) {
        $this->languageService = $languageService;
        $this->actionExecutor = $actionExecutor;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof LanguageCreateStep;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\LanguageCreateStep $step
     */
    public function doHandle(StepInterface $step): Language
    {
        Assert::isInstanceOf($step, LanguageCreateStep::class);
        $languageCreateStruct = $this->languageService->newLanguageCreateStruct();

        $languageCreateStruct->languageCode = $step->metadata->languageCode;
        $languageCreateStruct->name = $step->metadata->name;
        $languageCreateStruct->enabled = $step->metadata->enabled;

        $language = $this->languageService->createLanguage($languageCreateStruct);

        $this->doCollectReferences($step, $language);

        $this->getLogger()->notice(sprintf(
            'Added language: "%s" (ID: %s)',
            $language->languageCode,
            $language->id,
        ));

        return $language;
    }

    public static function getSubscribedServices(): array
    {
        return [
            TransactionHandler::class,
            '?' . ResolverInterface::class,
            CollectorInterface::class,
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function handleActions(ActionsAwareStepInterface $step, $executionResult): void
    {
        Assert::isInstanceOf($executionResult, Language::class);

        foreach ($step->getActions() as $action) {
            $this->actionExecutor->handle($action, $executionResult);
        }
    }
}

class_alias(LanguageCreateStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\LanguageCreateStepExecutor');
