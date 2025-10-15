<?php declare(strict_types=1);

namespace HatslogicPageOptimisation\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class CriticalCssTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'hatslogic_page_optimisation.critical_css_task';
    }

    public static function getDefaultInterval(): int
    {
        return 86400; // daily
    }
}
