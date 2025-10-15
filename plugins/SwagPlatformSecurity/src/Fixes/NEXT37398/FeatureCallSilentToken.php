<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Adapter\Twig\Node;

use Twig\Attribute\YieldReady;
use Twig\Compiler;
use Twig\Node\Node;

#[YieldReady]
class FeatureCallSilentToken extends Node
{
    public function __construct(
        private readonly string $flag,
        Node $body,
        int $line
    ) {
        parent::__construct(['body' => $body], [], $line, 'sw_silent_feature_call');
    }

    public function compile(Compiler $compiler): void
    {
        $compiler
            ->addDebugInfo($this)
            ->raw('\Shopware\Core\Framework\Feature::callSilentIfInactive(')
            ->string($this->flag)
            ->raw(', function () use(&$context) { ')
            ->subcompile($this->getNode('body'))
            ->raw('});');
    }
}

