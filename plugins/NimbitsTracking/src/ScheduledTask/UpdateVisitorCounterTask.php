<?php declare(strict_types=1);

namespace Nimbits\NimbitsTracking\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class UpdateVisitorCounterTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'nimbits.tracking.visitor-counter.update';
    }

    public static function getDefaultInterval(): int
    {
        return 300;
    }
}