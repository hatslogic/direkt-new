<?php

namespace Nimbits\NimbitsTracking\Command;

use Nimbits\NimbitsTracking\Service\VisitorCounterService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('tracking:update-visitors')]
class UpdateVisitorCounterCommand extends Command
{
    private VisitorCounterService $visitorCounterService;

    public function __construct(
        VisitorCounterService $visitorCounterService,
        string                $name = null
    )
    {
        $this->visitorCounterService = $visitorCounterService;
        parent::__construct($name);
    }

    // Provides a description, printed out in bin/console
    protected function configure(): void
    {
        $this->setDescription('Transfers data from the nb_tracking_visitor_ips to the nb_tracking_visitors table.');
    }

    // Actual code executed in the command
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->visitorCounterService->updateVisitorCount();
        $output->writeln('<info>Success</info>');
        return 0;
    }
}
