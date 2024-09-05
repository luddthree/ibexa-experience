<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Elasticsearch\Command;

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Ibexa\Bundle\Core\Command\BackwardCompatibleCommand;
use Ibexa\Elasticsearch\ElasticSearch\Client\ClientConfigurationProviderInterface;
use Ibexa\Elasticsearch\ElasticSearch\Client\ClientFactoryInterface;
use Ibexa\Elasticsearch\ElasticSearch\IndexTemplate\IndexTemplateRegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class PutIndexTemplateCommand extends Command implements BackwardCompatibleCommand
{
    private const OPTION_CONNECTION_NAME = 'connection';
    private const OPTION_OVERWRITE = 'overwrite';

    /** @var \Ibexa\Elasticsearch\ElasticSearch\Client\ClientFactoryInterface */
    private $clientFactory;

    /** @var \Ibexa\Elasticsearch\ElasticSearch\Client\ClientConfigurationProviderInterface */
    private $clientConfigurationProvider;

    /** @var \Ibexa\Elasticsearch\ElasticSearch\IndexTemplate\IndexTemplateRegistryInterface */
    private $indexTemplateRegistry;

    public function __construct(
        ClientFactoryInterface $clientFactory,
        ClientConfigurationProviderInterface $clientConfigurationProvider,
        IndexTemplateRegistryInterface $indexTemplateRegistry
    ) {
        parent::__construct('ibexa:elasticsearch:put-index-template');

        $this->clientFactory = $clientFactory;
        $this->clientConfigurationProvider = $clientConfigurationProvider;
        $this->indexTemplateRegistry = $indexTemplateRegistry;
    }

    protected function configure(): void
    {
        $this->setAliases(['ezplatform:elasticsearch:put-index-template']);

        $this->addOption(
            self::OPTION_CONNECTION_NAME,
            'c',
            InputOption::VALUE_OPTIONAL,
            'elasticsearch connection name'
        );

        $this->addOption(
            self::OPTION_OVERWRITE,
            null,
            InputOption::VALUE_NONE,
            'overwrite existing index template'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $connectionName = $input->getOption(self::OPTION_CONNECTION_NAME);

        $config = $this->clientConfigurationProvider->getClientConfiguration($connectionName);
        $client = $this->clientFactory->create($connectionName);

        foreach ($config->getIndexTemplates() as $name) {
            if (!$input->getOption(self::OPTION_OVERWRITE) && $this->indexTemplateExists($client, $name)) {
                $io->warning(sprintf(
                    'Index template "%s" already exists. Use --%s option to overwrite it.',
                    $name,
                    self::OPTION_OVERWRITE
                ));

                continue;
            }

            $request = $this->buildRequest(
                $name,
                $this->indexTemplateRegistry->getIndexTemplate($name),
                $input->getOption(self::OPTION_OVERWRITE)
            );

            $client->indices()->putTemplate($request);

            $io->success(sprintf('Index template "%s" as has been successfully created', $name));
        }

        return self::SUCCESS;
    }

    private function buildRequest(string $name, array $template, bool $overwrite): array
    {
        $request = [
            'name' => $name,
            'create' => !$overwrite,
            'body' => [
                'index_patterns' => $template['patterns'],
            ],
        ];

        if (!empty($template['settings'])) {
            $request['body']['settings'] = $template['settings'];
        }

        if (!empty($template['mappings'])) {
            $request['body']['mappings'] = $template['mappings'];
        }

        return $request;
    }

    private function indexTemplateExists(Client $client, string $name): bool
    {
        // Replace impl. with Elasticsearch\Namespaces\IndicesNamespace::existsIndexTemplate
        // when https://github.com/elastic/elasticsearch-php/issues/1072 will be solved
        try {
            $client->indices()->getTemplate(['name' => $name]);

            return true;
        } catch (Missing404Exception $e) {
            return false;
        }
    }

    /**
     * @return string[]
     */
    public function getDeprecatedAliases(): array
    {
        return ['ezplatform:elasticsearch:put-index-template'];
    }
}

class_alias(PutIndexTemplateCommand::class, 'Ibexa\Platform\Bundle\ElasticSearchEngine\Command\PutIndexTemplateCommand');
