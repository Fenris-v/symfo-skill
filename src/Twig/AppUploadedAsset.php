<?php

namespace App\Twig;

use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\RuntimeExtensionInterface;

class AppUploadedAsset implements RuntimeExtensionInterface
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
        private Packages $packages
    ) {
    }

    /**
     * @param string $config
     * @param string $path
     * @return string
     */
    public function asset(string $config, string $path): string
    {
        return $this->packages->getUrl($this->parameterBag->get($config) . '/' . $path);
    }
}
