<?php

namespace Bugloos\ApiVersioningBundle\Service;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class ApiVersioningHandler
{
    private RouterInterface $router;

    private string $routeConfigs = '';

    private string $filePath = '';

    private array $previousVersionRoutes = [];

    private string $baseVersion;

    private array $baseVersionRoutes = [];

    private array $nextVersions;

    private array $nextVersionRoutes = [];

    private array $deletedRoutes;

    private array $routesThatShouldBeWrite = [];

    public function __construct(
        RouterInterface $router,
        string $baseVersion,
        array $nextVersions,
        array $deletedRoutes
    ) {
        $this->router = $router;
        $this->baseVersion = $baseVersion;
        $this->nextVersions = $nextVersions;
        $this->deletedRoutes = $deletedRoutes;
    }

    public function generateRouteFiles()
    {
        $routes = $this->router->getRouteCollection()->all();

        foreach ($routes as $routeName => $route) {
            if ($this->isNotAControllerRoute($routeName)) {
                continue;
            }

            if ($this->isBaseVersionRoute($routeName)) {
                $this->setAsBaseVersionRoute($routeName, $route);
            }

            $this->setAsOtherVersionRoutes($routeName, $route);
        }

        $this->setPreviousVersionRoutes($this->baseVersionRoutes);

        foreach ($this->nextVersions as $version) {
            $currentVersionRoutes = $this->getCurrentVersionRoute($version);

            $this->routesThatShouldBeWrite = array_diff_key($this->previousVersionRoutes, $currentVersionRoutes);

            $this->removeDeletedRoutes($version);

            $this->setConfigsFilePath($version);

            foreach ($this->routesThatShouldBeWrite as $routeName => $route) {
                $preparedRouteConfig = $this->getPreparedRouteConfig($routeName, $version, $route);

                $this->appendToRouteConfigs($preparedRouteConfig);
            }

            $this->addRouteConfigsToConfigFile();

            $this->setPreviousVersionRoutes(
                array_merge($this->routesThatShouldBeWrite, $currentVersionRoutes)
            );

            $this->clearRouteConfigs();

            $this->clearConfigsFilePath();

            $this->clearRoutesThatShouldBeWrite();
        }
    }

    private function appendToRouteConfigs(string $config): void
    {
        $this->routeConfigs .= $config;
    }

    private function clearRouteConfigs(): void
    {
        $this->routeConfigs = '';
    }

    private function addRouteConfigsToConfigFile(): void
    {
        file_put_contents($this->filePath, $this->routeConfigs);
    }

    private function clearConfigsFilePath()
    {
        $this->filePath = '';
    }

    private function setConfigsFilePath(string $version): void
    {
        $this->filePath = sprintf('%s/config/routes/routes_%s.yaml', getcwd(), $version);
    }

    private function getPreparedRouteConfig($routeName, $version, $route): string
    {
        $routeConfig = $this->getPreparedRouteName($routeName, $version);
        $routeConfig .= $this->getPreparedRoutePath($version, $route);
        $routeConfig .= $this->getPreparedControllerName($route);
        $routeConfig .= $this->getPreparedRouteMethods($route);

        if (!empty($route->getRequirements())) {
            $routeConfig .= $this->getPreparedRouteRequirements($route);
        }

        return $routeConfig;
    }

    private function getPreparedRouteName(string $routeName, string $version): string
    {
        return sprintf("%s_%s:\n", $routeName, $version);
    }

    private function getPreparedRoutePath(string $version, Route $route): string
    {
        $decoratedPath = str_replace($route->getOption('version'), $version, $route->getPath());
        return sprintf("    path: %s\n", $decoratedPath);
    }

    private function getPreparedControllerName(Route $route): string
    {
        return "    controller: " . $route->getDefault('_controller') . "\n";
    }

    private function getPreparedRouteMethods(Route $route): string
    {
        return "    methods: " . implode('|', $route->getMethods()) . "\n";
    }

    private function getPreparedRouteRequirements(Route $route): string
    {
        $routeRequirements = "    requirements:\n";
        foreach ($route->getRequirements() as $requirementName => $requirementValue) {
            $routeRequirements .= "        $requirementName: " . "'$requirementValue'\n";
        }

        return $routeRequirements;
    }

    private function isNotAControllerRoute(string $routeName): bool
    {
        return substr($routeName, -5, 2) !== '_v';
    }

    private function isBaseVersionRoute(string $routeName): bool
    {
        return str_ends_with($routeName, $this->baseVersion);
    }

    private function detectVersionFromRouteName(string $routeName): string
    {
        return substr($routeName, -4, 4);
    }

    private function detectRouteNameWithoutVersion(string $routeName): string
    {
        return substr($routeName, 0, -5);
    }

    private function setPreviousVersionRoutes(array $routes)
    {
        $this->previousVersionRoutes = $routes;
    }

    private function setAsBaseVersionRoute(string $routeName, Route $route): void
    {
        $routeNameWithoutVersion = $this->detectRouteNameWithoutVersion($routeName);

        $route->setOption('version', $this->baseVersion);

        $this->baseVersionRoutes[$routeNameWithoutVersion] = $route;

    }

    private function setAsOtherVersionRoutes(string $routeName, Route $route): void
    {
        $version = $this->detectVersionFromRouteName($routeName);

        $routeNameWithoutVersion = $this->detectRouteNameWithoutVersion($routeName);

        $route->setOption('version', $version);

        $this->nextVersionRoutes[$version][$routeNameWithoutVersion] = $route;
    }

    private function getCurrentVersionRoute(string $version): array
    {
        return $this->nextVersionRoutes[$version] ?? [];
    }

    private function removeDeletedRoutes(string $version): void
    {
        if (isset($this->deletedRoutes[$version]) && !empty($this->deletedRoutes[$version])) {
            $deletableRoutesInCurrentVersion = array_combine(
                $this->deletedRoutes[$version],
                $this->deletedRoutes[$version]
            );
            $this->routesThatShouldBeWrite = array_diff_key(
                $this->routesThatShouldBeWrite,
                $deletableRoutesInCurrentVersion
            );
        }
    }

    private function clearRoutesThatShouldBeWrite(): void
    {
        $this->routesThatShouldBeWrite = [];
    }
}
