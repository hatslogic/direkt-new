<?php declare(strict_types=1);

namespace Shopvote\ShopvotePlugin\ScheduledTask;

use Shopvote\ShopvotePlugin\Model\ImportModel;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;

class ImportTaskHandler extends ScheduledTaskHandler
{
    /** @var ImportModel */
    private $importModel;

    public static function getHandledMessages(): iterable
    {
        return [ ImportTask::class ];
    }

    /**
     * @required
     * @param ImportModel $importModel
     */
    public function setImportModel(ImportModel $importModel): void
    {
        $this->importModel = $importModel;
    }

    public function run(): void
    {
        $this->importModel->importAll(Context::createDefaultContext());
    }
}
