<?php

namespace WebLa_ContactFormFileUpload;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OptionalServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // Check if the plugin's service exists
        if ($container->has('Swag\\CmsExtensions\\SwagCmsExtensions')) {
            // If the service exists, you can modify the container here
            // For example, add or replace services
        } else {
            // If the service does not exist, handle accordingly
            // You can log this information or perform other actions
        }
    }
}
