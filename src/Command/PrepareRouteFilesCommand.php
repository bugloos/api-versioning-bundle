<?php

namespace Bugloos\ApiVersioningBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PrepareRouteFilesCommand extends Command
{
    protected static $defaultName = 'api-versioning:prepare-route-files';

    private array $nextVersions;

    public function __construct(array $nextVersions,string $name = null)
    {
        parent::__construct($name);
        $this->nextVersions = $nextVersions;
    }

    protected function configure(): void
    {
        $this->setDescription('Prepare route configs files');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->nextVersions as $version) {
            $filePath = sprintf('%s/config/routes/routes_%s.yaml', getcwd(), $version);
            fopen($filePath, 'w');
        }

        return 0;
    }
}
