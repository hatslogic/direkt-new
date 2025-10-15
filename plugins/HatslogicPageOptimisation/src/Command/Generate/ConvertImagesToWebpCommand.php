<?php

namespace HatslogicPageOptimisation\Command\Generate;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use HatslogicPageOptimisation\Service\ConvertImagesToWebpService;

#[AsCommand(
    name: 'hatslogic:convert-images-to-webp',
    description: 'Convert all images to WebP format.'
)]
class ConvertImagesToWebpCommand extends Command
{
    public function __construct(
        private ConvertImagesToWebpService $convertImagesToWebpService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting image conversion to WebP...');

        $isCommand = true;
        $this->convertImagesToWebpService->run($isCommand);

        $output->writeln('Image conversion completed successfully.');

        return Command::SUCCESS;
    }
}