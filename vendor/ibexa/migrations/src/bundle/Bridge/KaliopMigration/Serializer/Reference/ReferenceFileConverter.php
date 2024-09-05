<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\Reference;

use League\Flysystem\FilesystemOperator;
use Symfony\Component\Yaml\Yaml;
use Webmozart\Assert\Assert;

class ReferenceFileConverter
{
    private FilesystemOperator $filesystem;

    /** @var string */
    private $referencesFilesDirName;

    public function __construct(FilesystemOperator $filesystem, string $referencesFilesDirName)
    {
        $this->filesystem = $filesystem;
        $this->referencesFilesDirName = $referencesFilesDirName;
    }

    public function convert(string $file, string $output): string
    {
        if (!$this->filesystem->fileExists($file)) {
            return $file;
        }

        $inputFile = pathinfo($file, PATHINFO_FILENAME) . '.' . pathinfo($file, PATHINFO_EXTENSION);
        $outputDir = pathinfo($output, PATHINFO_DIRNAME);
        $referencesOutputDir = $outputDir . \DIRECTORY_SEPARATOR . $this->referencesFilesDirName . \DIRECTORY_SEPARATOR;
        $convertedFileNameWithPath = $referencesOutputDir . $inputFile;
        $convertedFileNameWithRelativePath = $this->referencesFilesDirName . \DIRECTORY_SEPARATOR . $inputFile;

        if ($this->convertedFileExists($convertedFileNameWithPath)) {
            return $convertedFileNameWithRelativePath;
        }

        $convertedReferencesYaml = $this->convertReferences($file);

        $this->filesystem->write($convertedFileNameWithPath, $convertedReferencesYaml);

        return $convertedFileNameWithRelativePath;
    }

    private function convertedFileExists(string $convertedFileNameWithPath): bool
    {
        return $this->filesystem->fileExists($convertedFileNameWithPath);
    }

    private function convertReferences(string $file): string
    {
        $content = $this->filesystem->read($file);
        Assert::notFalse($content, sprintf('File %s is not readable', $file));

        $references = Yaml::parse($content);
        $convertedReferences = [];
        foreach ($references as $key => $value) {
            $convertedReferences[] = [
                'name' => $key,
                'value' => $value,
            ];
        }

        return Yaml::dump($convertedReferences);
    }
}

class_alias(ReferenceFileConverter::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\Reference\ReferenceFileConverter');
