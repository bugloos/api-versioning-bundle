<?php

/**
 * This file is part of the bugloos/api-versioning-bundle project.
 * (c) Bugloos <https://bugloos.com/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Bugloos\ApiVersioningBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Mojtaba Gheytasi <mjgheytasi@gmail.com>
 */
class ApiVersioningExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('base_version', $config['base_version']);
        $container->setParameter('next_versions', $config['next_versions']);
        $container->setParameter('deleted_routes', $config['deleted_routes']);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.yaml');
    }

    public function getAlias(): string
    {
        return 'bugloos_api_versioning';
    }
}
