<?php declare(strict_types=1);

namespace Swag\Security\Fixes\NEXT37140;

use Shopware\Core\Content\Product\SearchKeyword\ProductSearchBuilderInterface;
use Swag\Security\Components\AbstractSecurityFix;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class SecurityFix extends AbstractSecurityFix
{
    public static function getTicket(): string
    {
        return 'NEXT-37140';
    }

    public static function getMinVersion(): string
    {
        return '6.6.0.0';
    }

    public static function getMaxVersion(): ?string
    {
        return '6.6.4.0';
    }

    public static function buildContainer(ContainerBuilder $container): void
    {
        $decorated = new Definition(ProductSearchBuilderDecorator::class);
        $decorated->setArguments([
            new Reference('.inner'),
            new Reference('logger'),
            new Reference('parameter_bag'),
        ]);

        $decorated->setDecoratedService(id: ProductSearchBuilderInterface::class, priority: -60001);
        $container->setDefinition(ProductSearchBuilderDecorator::class, $decorated);
    }
}
