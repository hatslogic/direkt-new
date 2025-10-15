<?php declare(strict_types=1);

namespace Swag\Security\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Shopware\Core\Framework\DependencyInjection\Configuration as CoreConfiguration;

class Configuration extends CoreConfiguration
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = parent::getConfigTreeBuilder();

        if (self::hasSearchSection()) {
            return $treeBuilder;
        }

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->append($this->createSearchSection())
            ->end();

        return $treeBuilder;
    }

    public static function hasSearchSection(): bool
    {
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = (new CoreConfiguration())->getConfigTreeBuilder()->getRootNode();

        return !empty($rootNode->getChildNodeDefinitions()['search']);
    }

    private function createSearchSection(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('search');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->integerNode('term_max_length')->defaultValue(300)->end()
            ->end();

        return $rootNode;
    }
}
