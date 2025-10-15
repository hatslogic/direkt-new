<?php declare(strict_types=1);

namespace Shopvote\ShopvotePlugin\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class ImportTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'shopvote.import_task';
    }

    public static function getDefaultInterval(): int
    {
        return 43200;
    }
}
