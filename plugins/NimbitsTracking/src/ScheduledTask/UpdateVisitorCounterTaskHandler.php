<?php declare(strict_types=1);

namespace Nimbits\NimbitsTracking\ScheduledTask;

use Nimbits\NimbitsTracking\Service\VisitorCounterService;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

class UpdateVisitorCounterTaskHandler extends AbstractNimbitsTaskHandler
{
    private VisitorCounterService $visitorCounterService;

    public function __construct(
        EntityRepository      $scheduledTaskRepository,
        LoggerInterface       $logger,
        VisitorCounterService $visitorCounterService
    )
    {
        parent::__construct($scheduledTaskRepository, $logger);
        $this->visitorCounterService = $visitorCounterService;
    }

    public static function getHandledMessages(): iterable
    {
        return [UpdateVisitorCounterTask::class];
    }

    public function run(): void
    {
        $this->visitorCounterService->updateVisitorCount();
    }
}
