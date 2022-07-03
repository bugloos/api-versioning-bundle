<?php

namespace Bugloos\ApiVersioningBundle\Command;

use Bugloos\ApiVersioningBundle\Service\ApiVersioningHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddRouteConfigsToRouteFilesCommand extends Command
{
    protected static $defaultName = 'api-versioning:generate-route-configs';

    private ApiVersioningHandler $apiVersioningHandler;

    public function __construct(
        ApiVersioningHandler $apiVersioningHandler,
        string $name = null
    ) {
        parent::__construct($name);
        $this->apiVersioningHandler = $apiVersioningHandler;
    }

    protected function configure(): void
    {
        $this->setDescription('Generate route configs');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->apiVersioningHandler->generateRouteFiles();

        return 0;
    }
}
