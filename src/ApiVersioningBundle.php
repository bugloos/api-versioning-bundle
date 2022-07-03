<?php

/**
 * This file is part of the bugloos/api-versioning-bundle project.
 * (c) Bugloos <https://bugloos.com/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Bugloos\ApiVersioningBundle;

use Bugloos\ApiVersioningBundle\DependencyInjection\ApiVersioningExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Mojtaba Gheytasi <mjgheytasi@gmail.com>
 */
class ApiVersioningBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new ApiVersioningExtension();
        }

        return $this->extension;
    }
}
