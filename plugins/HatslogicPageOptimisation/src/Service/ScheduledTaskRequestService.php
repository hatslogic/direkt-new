<?php declare(strict_types=1);

namespace HatslogicPageOptimisation\Service;

use Symfony\Component\HttpFoundation\Response;
use HatslogicPageOptimisation\Service\ConfigService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

class ScheduledTaskRequestService
{
    private ConfigService $configService;

    /**
     * @var EntityRepository
     */
    private EntityRepository $scheduleTaskRepository;

    public function __construct(
        ConfigService $configService,
        EntityRepository $scheduleTaskRepository,
    )
    {
        $this->configService = $configService;
        $this->scheduleTaskRepository = $scheduleTaskRepository;
    }

    public function generateCriticalCss()
    {
        $isEnable = $this->configService->isCriticalCssScheduleTaskEnable();
        if ($isEnable)  {
            //check last execution time of this schedule task using schedultTask repository
            // $scheduleTask = $this->scheduleTaskRepository->search(
            //     new \Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria(),
            //     Context::getSystemContext()
            // );


        }

        return true;
    }
}
