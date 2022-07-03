<?php

/**
 * This file is part of the bugloos/api-versioning-bundle project.
 * (c) Bugloos <https://bugloos.com/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Bugloos\ApiVersioningBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Mojtaba Gheytasi <mjgheytasi@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('bugloos_api_versioning');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('base_version')
                    ->defaultValue('v1.0')
                ->end()
                ->arrayNode('next_versions')
                    ->scalarPrototype()
                    ->end()
                ->defaultValue([])
                ->end()
                ->arrayNode('deleted_routes')
                    ->variablePrototype()
                    ->end()
                ->defaultValue([])
            ->end();

        return $treeBuilder;
    }
}
