<?php declare(strict_types=1);

namespace HatslogicPageOptimisation\Twig;

use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\Page;
use Twig\Compiler;
use Twig\Environment;
use Twig\Extension\ExtensionInterface;
use Twig\Lexer;
use Twig\Loader\LoaderInterface;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;
use Twig\Parser;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;
use Twig\TokenParser\TokenParserInterface;
use Twig\TokenStream;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;
use HatslogicPageOptimisation\Processor\CriticalCssProcessor;

class TwigEnvDecorator extends Environment
{
    public function __construct(
        private Environment $decorated,
        private CriticalCssProcessor $criticalCssProcessor
    ) {
        
    }

    public function render($name, array $context = []): string
    {
        
        $renderedHtml = $this->decorated->render($name, $context);

        if (!key_exists('page', $context)) {
            return $renderedHtml;
        }

        if (!method_exists($context['page'], 'getCmsPage') || !$context['page'] instanceof Page) {
            return $renderedHtml;
        }

        if (!method_exists($context['context'], 'getSalesChannel') || !$context['context'] instanceof SalesChannelContext) {
            return $renderedHtml;
        }

        $cmsPageId = $context['page']->getCmsPage() ? $context['page']->getCmsPage()->getId() : null;

        if (empty($cmsPageId)) {
            return $renderedHtml;
        }

        $salesChannelId = $context['context']->getSalesChannel()->getId();

        return $this->criticalCssProcessor->process($renderedHtml, $salesChannelId . $cmsPageId, $salesChannelId);
    }

    public function getTemplateData(): array
    {
        return $this->decorated->getTemplateData();
    }

    public function enableDebug()
    {
        $this->decorated->enableDebug();
    }
    public function disableDebug()
    {
        $this->decorated->disableDebug();
    }
    public function isDebug()
    {
        return $this->decorated->isDebug();
    }
    public function enableAutoReload()
    {
        $this->decorated->enableAutoReload();
    }
    public function disableAutoReload()
    {
        $this->decorated->disableAutoReload();
    }
    public function isAutoReload()
    {
        return $this->decorated->isAutoReload();
    }
    public function enableStrictVariables()
    {
        $this->decorated->enableStrictVariables();
    }
    public function disableStrictVariables()
    {
        $this->decorated->disableStrictVariables();
    }
    public function isStrictVariables()
    {
        return $this->decorated->isStrictVariables();
    }
    public function getCache($original = true)
    {
        return $this->decorated->getCache(...func_get_args());
    }
    public function setCache($cache)
    {
        $this->decorated->setCache(...func_get_args());
    }
    public function getTemplateClass(string $name, int $index = null): string
    {
        return $this->decorated->getTemplateClass(...func_get_args());
    }
    public function display($name, array $context = []): void
    {
        $this->decorated->display(...func_get_args());
    }
    public function load($name): TemplateWrapper
    {
        return $this->decorated->load(...func_get_args());
    }
    public function loadTemplate(string $cls, string $name, int $index = null): Template
    {
        return $this->decorated->loadTemplate(...func_get_args());
    }
    public function createTemplate(string $template, string $name = null): TemplateWrapper
    {
        return $this->decorated->createTemplate(...func_get_args());
    }
    public function isTemplateFresh(string $name, int $time): bool
    {
        return $this->decorated->isTemplateFresh(...func_get_args());
    }
    public function resolveTemplate($names): TemplateWrapper
    {
        return $this->decorated->resolveTemplate(...func_get_args());
    }
    public function setLexer(Lexer $lexer)
    {
        $this->decorated->setLexer(...func_get_args());
    }
    public function tokenize(Source $source): TokenStream
    {
        return $this->decorated->tokenize(...func_get_args());
    }
    public function setParser(Parser $parser)
    {
        $this->decorated->setParser(...func_get_args());
    }
    public function parse(TokenStream $stream): ModuleNode
    {
        return $this->decorated->parse(...func_get_args());
    }
    public function setCompiler(Compiler $compiler)
    {
        $this->decorated->setCompiler(...func_get_args());
    }
    public function compile(Node $node): string
    {
        return $this->decorated->compile(...func_get_args());
    }
    public function compileSource(Source $source): string
    {
        return $this->decorated->compileSource(...func_get_args());
    }
    public function setLoader(LoaderInterface $loader)
    {
        $this->decorated->setLoader(...func_get_args());
    }
    public function getLoader(): LoaderInterface
    {
        return $this->decorated->getLoader();
    }
    public function setCharset(string $charset)
    {
        $this->decorated->setCharset(...func_get_args());
    }
    public function getCharset(): string
    {
        return $this->decorated->getCharset();
    }
    public function hasExtension(string $class): bool
    {
        return $this->decorated->hasExtension(...func_get_args());
    }
    public function addRuntimeLoader(RuntimeLoaderInterface $loader)
    {
        $this->decorated->addRuntimeLoader(...func_get_args());
    }
    public function getExtension(string $class): ExtensionInterface
    {
        return $this->decorated->getExtension(...func_get_args());
    }
    public function getRuntime(string $class)
    {
        return $this->decorated->getRuntime(...func_get_args());
    }
    public function addExtension(ExtensionInterface $extension)
    {
        $this->decorated->addExtension(...func_get_args());
    }
    public function setExtensions(array $extensions)
    {
        $this->decorated->setExtensions(...func_get_args());
    }
    public function getExtensions(): array
    {
        return $this->decorated->getExtensions();
    }
    public function addTokenParser(TokenParserInterface $parser)
    {
        $this->decorated->addTokenParser(...func_get_args());
    }
    public function getTokenParsers(): array
    {
        return $this->decorated->getTokenParsers();
    }
    public function getTokenParser(string $name): ?TokenParserInterface
    {
        return $this->decorated->getTokenParser(...func_get_args());
    }
    public function registerUndefinedTokenParserCallback(callable $callable): void
    {
        $this->decorated->registerUndefinedTokenParserCallback(...func_get_args());
    }
    public function addNodeVisitor(NodeVisitorInterface $visitor)
    {
        $this->decorated->addNodeVisitor(...func_get_args());
    }
    public function getNodeVisitors(): array
    {
        return $this->decorated->getNodeVisitors();
    }
    public function addFilter(TwigFilter $filter)
    {
        $this->decorated->addFilter(...func_get_args());
    }
    public function getFilter(string $name): ?TwigFilter
    {
        return $this->decorated->getFilter(...func_get_args());
    }
    public function registerUndefinedFilterCallback(callable $callable): void
    {
        $this->decorated->registerUndefinedFilterCallback(...func_get_args());
    }
    public function getFilters(): array
    {
        return $this->decorated->getFilters();
    }
    public function addTest(TwigTest $test)
    {
        $this->decorated->addTest(...func_get_args());
    }
    public function getTests(): array
    {
        return $this->decorated->getTests();
    }
    public function getTest(string $name): ?TwigTest
    {
        return $this->decorated->getTest(...func_get_args());
    }
    public function addFunction(TwigFunction $function)
    {
        $this->decorated->addFunction(...func_get_args());
    }
    public function getFunction(string $name): ?TwigFunction
    {
        return $this->decorated->getFunction(...func_get_args());
    }
    public function registerUndefinedFunctionCallback(callable $callable): void
    {
        $this->decorated->registerUndefinedFunctionCallback(...func_get_args());
    }
    public function getFunctions(): array
    {
        return $this->decorated->getFunctions();
    }
    public function addGlobal(string $name, $value)
    {
        $this->decorated->addGlobal(...func_get_args());
    }
    public function getGlobals(): array
    {
        return $this->decorated->getGlobals();
    }
    public function mergeGlobals(array $context): array
    {
        return $this->decorated->mergeGlobals(...func_get_args());
    }
    public function getUnaryOperators(): array
    {
        return $this->decorated->getUnaryOperators();
    }
    public function getBinaryOperators(): array
    {
        return $this->decorated->getBinaryOperators();
    }
}
