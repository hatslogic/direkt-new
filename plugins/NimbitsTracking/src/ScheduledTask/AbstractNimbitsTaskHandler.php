<?php

namespace Nimbits\NimbitsTracking\ScheduledTask;

use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Throwable;

abstract class AbstractNimbitsTaskHandler extends ScheduledTaskHandler
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(
        EntityRepository $scheduledTaskRepository,
        LoggerInterface           $logger
    )
    {
        parent::__construct($scheduledTaskRepository);
        $this->logger = $logger;
    }

    public function handle($task): void
    {
        // Wrap with custom Error-Handler to prevent "failed"-Status in Database
        try {
            parent::handle($task);
        } catch (Throwable $e) {
            $this->logger->error(sprintf("%s\nin %s (line %d)\n%s", $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString()));
        }
    }
}