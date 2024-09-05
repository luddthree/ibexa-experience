<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator;

use Ibexa\Migration\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Webmozart\Assert\Assert;

final class MigrationGeneratorsManager implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var iterable<\Ibexa\Migration\Generator\MigrationGeneratorInterface> */
    private $generators;

    /**
     * @param iterable<\Ibexa\Migration\Generator\MigrationGeneratorInterface> $generators
     */
    public function __construct(
        iterable $generators,
        LoggerInterface $logger
    ) {
        Assert::allImplementsInterface($generators, MigrationGeneratorInterface::class);
        $this->generators = $generators;
        $this->logger = $logger;
    }

    /**
     * @return array<string, string>
     */
    public function getSupportedTypes(): array
    {
        $supportedTypes = [];
        foreach ($this->generators as $generator) {
            $supportedType = $generator->getSupportedType();
            $supportedTypes[$supportedType] = $supportedType;
        }

        return $supportedTypes;
    }

    /**
     * @param string|null $type If used, limits resulting modes by generators that support a specific type only
     *
     * @return array<string, string>
     */
    public function getSupportedModes(?string $type = null): array
    {
        $supportedModes = [];
        foreach ($this->generators as $generator) {
            $supportedType = $generator->getSupportedType();
            if ($type !== null && $type !== $supportedType) {
                continue;
            }

            foreach ($generator->getSupportedModes() as $mode) {
                $supportedModes[$mode] = $mode;
            }
        }

        return $supportedModes;
    }

    /**
     * @param array<mixed> $context
     *
     * @return iterable<\Ibexa\Migration\ValueObject\Step\StepInterface>
     */
    public function generate(string $migrationType, Mode $migrationMode, array $context = []): iterable
    {
        foreach ($this->generators as $generator) {
            if ($generator->supports($migrationType, $migrationMode)) {
                $this->getLogger()->debug(sprintf('Generating data for %s', $migrationType), [
                    'context' => $context,
                ]);

                return $generator->generate($migrationMode, $context);
            }
        }

        throw new RuntimeException(sprintf('Unable to generate migration for type %s', $migrationType));
    }
}

class_alias(MigrationGeneratorsManager::class, 'Ibexa\Platform\Migration\Generator\MigrationGeneratorsManager');
