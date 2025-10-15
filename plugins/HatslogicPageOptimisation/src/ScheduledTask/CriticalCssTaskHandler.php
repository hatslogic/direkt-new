<?php declare(strict_types=1);

namespace HatslogicPageOptimisation\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use HatslogicPageOptimisation\Service\ScheduledTaskRequestService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: CriticalCssTask::class)]
class CriticalCssTaskHandler extends ScheduledTaskHandler
{
    /**
     * @var EntityRepository
     */
    protected EntityRepository $scheduledTaskRepository;

    /**
     * @var ScheduledTaskRequestService
     */
    public $scheduledTaskRequestService;

    public function __construct(EntityRepository $scheduledTaskRepository, ScheduledTaskRequestService $scheduledTaskRequestService)
    {
        parent::__construct($scheduledTaskRepository);
        $this->scheduledTaskRequestService = $scheduledTaskRequestService;
    }

    public function run(): void
    {
        $this->scheduledTaskRequestService->generateCriticalCss();
    }
}