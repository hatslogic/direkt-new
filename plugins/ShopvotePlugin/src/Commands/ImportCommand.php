<?php declare(strict_types=1);

namespace Shopvote\ShopvotePlugin\Commands;

use Shopvote\ShopvotePlugin\Model\ImportModel;
use Shopware\Core\Framework\Context;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'shopvote:import', description: 'Imports product reviews from SHOPVOTE.', hidden: false)]
class ImportCommand extends Command
{
    protected static $defaultName = 'shopvote:import';

    /** @var ImportModel */
    private $importModel;

    /**
     * @required
     * @param ImportModel $importModel
     */
    public function setImportModel(ImportModel $importModel): void
    {
        $this->importModel = $importModel;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->importModel->setWriter($output);
        $this->importModel->importAll(Context::createDefaultContext());

        return 0;
    }
}