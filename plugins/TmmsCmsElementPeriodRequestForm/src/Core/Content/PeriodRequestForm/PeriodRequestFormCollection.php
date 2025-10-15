<?php
declare(strict_types=1);

namespace Tmms\CmsElementPeriodRequestForm\Core\Content\PeriodRequestForm;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                         add(PeriodRequestFormEntity $entity)
 * @method void                         set(string $key, PeriodRequestFormEntity $entity)
 * @method PeriodRequestFormEntity[]    getIterator()
 * @method PeriodRequestFormEntity[]    getElements()
 * @method PeriodRequestFormEntity|null get(string $key)
 * @method PeriodRequestFormEntity|null first()
 * @method PeriodRequestFormEntity|null last()
 */
class PeriodRequestFormCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return PeriodRequestFormEntity::class;
    }
}
