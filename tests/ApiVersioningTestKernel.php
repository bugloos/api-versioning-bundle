<?php

/**
 * This file is part of the bugloos/api-versioning-bundle project.
 * (c) Bugloos <https://bugloos.com/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Bugloos\ApiVersioningBundle\Tests;

use Bugloos\ApiVersioningBundle\ApiVersioningBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Exception;

/**
 * @author Mojtaba Gheytasi <mjgheytasi@gmail.com>
 */
class ApiVersioningTestKernel extends Kernel
{
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new ApiVersioningBundle(),
        ];
    }

    /**
     * @throws Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/../src/Resources/config/services.yaml');
        $loader->load(__DIR__ . '/Fixtures/Config/config.yaml');
    }
}
