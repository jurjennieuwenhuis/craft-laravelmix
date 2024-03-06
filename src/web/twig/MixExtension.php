<?php

namespace juni\laravelmix\web\twig;

use Craft;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension
 */
class MixExtension extends AbstractExtension
{
    public string $publicDir = '';
    public string $manifestName = '';
    public array|null $manifest = null;

    public function __construct(string $publicDir, string $manifestName)
    {
        $alias = Craft::getAlias($publicDir, false);

        if ($alias && is_string($alias)) {
            $publicDir = $alias;
        }

        $this->publicDir = rtrim($publicDir, '/');
        $this->manifestName = $manifestName;
    }

    public function getFunctions(): array
    {
        // Define custom Twig functions
        // (see https://twig.symfony.com/doc/3.x/advanced.html#functions)

        // Implementation of the Blade mix function
        // https://github.com/laravel/framework/blob/10.x/src/Illuminate/Foundation/Mix.php
        return [
            new TwigFunction('mix', function(string $path) {

                static $manifests = [];

                $path = '/' . ltrim($path, '/');

                if (is_file($this->publicDir . '/hot')) {
                    $url = rtrim(file_get_contents($this->publicDir . '/hot'));

                    $customUrl = ''; //app('config')->get('app.mix_hot_proxy_url');

                    if (!empty($customUrl)) {
                        return rtrim($customUrl, '/') . $path;
                    }

                    if (str_starts_with($url, 'http')) {
                        return $url . $path;
                    }

                    return '//localhost:8080' . $path;
                }

                $manifest = $this->getManifest();
                if (!isset($manifest[$path])) {
                    Craft::error(Craft::t('laravelmix', "File {$path} not defined in asset manifest."));
                }

                // Only return the file path relative to the public folder (e.g css/style.css) and not (/public/css/style)
                return $manifest[$path];
            }),
        ];
    }

    protected function getManifest(): array
    {
        if (null === $this->manifest) {
            $manifestPath = $this->publicDir . '/' . $this->manifestName;
            $this->manifest = json_decode(file_get_contents($manifestPath), true);
        }

        return $this->manifest;
    }
}
