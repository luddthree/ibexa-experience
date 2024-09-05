<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Migrations\Generator\Company;

use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Migration\Generator\MigrationGeneratorInterface;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use function in_array;

final class CompanyMigrationGenerator implements MigrationGeneratorInterface
{
    private const TYPE = 'company';

    private CompanyService $companyService;

    private StepFactoryInterface $stepFactory;

    private int $chunkSize;

    private Mode $mode;

    public function __construct(
        CompanyService $companyService,
        StepFactoryInterface $stepFactory,
        int $chunkSize
    ) {
        $this->companyService = $companyService;
        $this->stepFactory = $stepFactory;
        $this->chunkSize = $chunkSize;
    }

    public function supports(string $migrationType, Mode $migrationMode): bool
    {
        return $migrationType === $this->getSupportedType()
            && in_array($migrationMode->getMode(), $this->getSupportedModes(), true);
    }

    public function getSupportedType(): string
    {
        return self::TYPE;
    }

    /**
     * @return string[]
     */
    public function getSupportedModes(): array
    {
        return $this->stepFactory->getSupportedModes();
    }

    /**
     * @phpstan-return iterable<\Ibexa\Migration\ValueObject\Step\StepInterface>
     *
     * @return \Ibexa\Migration\ValueObject\Step\StepInterface[]
     */
    public function generate(Mode $migrationMode, array $context): iterable
    {
        $this->mode = $migrationMode;

        yield from $this->generateSteps(
            new BatchIterator(
                new CompanyGetAdapter($this->companyService),
                $this->chunkSize
            )
        );
    }

    /**
     * @param iterable<\Ibexa\Contracts\CorporateAccount\Values\Company> $companies
     *
     * @return \Ibexa\Migration\ValueObject\Step\StepInterface[]
     */
    private function generateSteps(iterable $companies): iterable
    {
        $steps = [];
        foreach ($companies as $company) {
            $steps[] = $this->createStep($company);
        }

        return $steps;
    }

    private function createStep(Company $company): StepInterface
    {
        return $this->stepFactory->create($company, $this->mode);
    }
}
