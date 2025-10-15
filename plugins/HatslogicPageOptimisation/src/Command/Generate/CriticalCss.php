<?php declare(strict_types = 1);

namespace HatslogicPageOptimisation\Command\Generate;

use Shopware\Core\Framework\Context;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use HatslogicPageOptimisation\Service\CriticalCssGenerationService;

class CriticalCss extends Command
{
    protected CriticalCssGenerationService $criticalCssGenerationService;

    /**
     * @param CriticalCssGenerationService $criticalCssGenerationService
     * @param null                         $name
     */
    public function __construct(
        CriticalCssGenerationService $criticalCssGenerationService,
        $name = null
    )
    {
        $this->criticalCssGenerationService = $criticalCssGenerationService;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('hatslogic:generate:critical-css');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($output->isVerbose()) {
            $output->writeln("<comment>Starting generation of critical CSS</comment>");
        }

        $context = Context::createDefaultContext();
        $result = $this->criticalCssGenerationService->generate($context, $output);

        if ($result === null) {
            $output->writeln("<comment>Process ended prematurely</comment>");
            return 1;
        }

        if (!empty($result['failed'])) {
            $output->writeln("<error>Failed to generate critical CSS for urls:</error>");
            foreach ($result['failed'] as $failed) {
                $output->writeln("<error>$failed</error>");
            }
        }

        if (!empty($result['success'])) {
            $output->writeln("<info>Successfully generated Critical CSS for URL's:</info>");
            foreach ($result['success'] as $url) {
                $output->writeln("<info>$url</info>");
            }
        }

        return 0;
    }
}
