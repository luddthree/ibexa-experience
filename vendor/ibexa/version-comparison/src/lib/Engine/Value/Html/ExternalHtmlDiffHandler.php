<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Engine\Value\Html;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

final class ExternalHtmlDiffHandler implements HtmlDiffHandler
{
    private const TMP_FILE_PREFIX = 'diff_html_';

    /** @var \Symfony\Component\Filesystem\Filesystem */
    private $filesystem;

    /** @var string */
    private $tmpDir;

    /** @var string */
    private $diffBinaryPath;

    /** @var int */
    private $timeout;

    /** @var array */
    private $additionalArguments;

    public function __construct(
        Filesystem $filesystem,
        string $tmpDir,
        string $diffBinaryPath,
        int $timeout,
        array $additionalArguments = []
    ) {
        $this->filesystem = $filesystem;
        $this->tmpDir = $tmpDir;
        $this->diffBinaryPath = $diffBinaryPath;
        $this->timeout = $timeout;
        $this->additionalArguments = $additionalArguments;
    }

    public function getHtmlDiff(string $htmlA, string $htmlB): string
    {
        $fileA = $this->filesystem->tempnam($this->tmpDir, self::TMP_FILE_PREFIX);
        $fileB = $this->filesystem->tempnam($this->tmpDir, self::TMP_FILE_PREFIX);

        $this->filesystem->dumpFile($fileA, $htmlA);
        $this->filesystem->dumpFile($fileB, $htmlB);

        $process = new Process(
            array_merge([$this->diffBinaryPath, $fileA, $fileB], $this->additionalArguments)
        );

        $process->setTimeout($this->timeout);

        try {
            $process->run();
        } catch (ProcessTimedOutException $timedOutException) {
            throw $timedOutException;
        } finally {
            $this->filesystem->remove([$fileA, $fileB]);
        }

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }
}

class_alias(ExternalHtmlDiffHandler::class, 'EzSystems\EzPlatformVersionComparison\Engine\Value\Html\ExternalHtmlDiffHandler');
