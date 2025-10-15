<?php

namespace Swag\Security\Fixes\NEXT37397;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Parser\AggregationParser;
use Swag\Security\Components\AbstractSecurityFix;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AggregationInjectionFix extends AbstractSecurityFix
{
    public static function getTicket(): string
    {
        return 'NEXT-37397';
    }

    public static function getMinVersion(): string
    {
        return '6.1.0';
    }

    public static function getMaxVersion(): string
    {
        return '6.6.10.3';
    }

    public static function buildContainer(ContainerBuilder $container): void
    {
        $container->getDefinition(AggregationParser::class)->setClass(PatchedAggregationParser::class);
    }
}
