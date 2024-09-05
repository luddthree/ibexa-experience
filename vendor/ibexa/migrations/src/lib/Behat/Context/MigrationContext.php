<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Migration\Repository\Migration;

class MigrationContext implements Context
{
    /** @var \Ibexa\Contracts\Migration\MigrationService */
    private $migrationService;

    public function __construct(MigrationService $migrationService)
    {
        $this->migrationService = $migrationService;
    }

    /**
     * @Given I execute a migration
     * @Given I execute a migration named :name
     */
    public function executeMigration(PyStringNode $migrationFileContent, string $name = null): void
    {
        if ($name === null) {
            $name = uniqid('behat_', true);
        }

        $migration = new Migration($name, $this->getMigrationContent($migrationFileContent));
        $this->migrationService->executeOne($migration);
    }

    private function getMigrationContent(PyStringNode $migrationFileContent): string
    {
        $lines = $migrationFileContent->getStrings();

        $firstLine = $migrationFileContent->getStrings()[0];
        $firstLineIndent = $this->calculateLineIndent($firstLine);

        $linesWithRemovedIndent = array_map(static function (string $line) use ($firstLineIndent) {
            return substr($line, $firstLineIndent);
        }, $lines);

        return implode(PHP_EOL, $linesWithRemovedIndent);
    }

    private function calculateLineIndent(string $line): int
    {
        return strlen($line) - strlen(ltrim($line));
    }
}
