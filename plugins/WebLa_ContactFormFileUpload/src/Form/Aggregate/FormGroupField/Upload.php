<?php

declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebLa_ContactFormFileUpload\Form\Aggregate\FormGroupField;

use Swag\CmsExtensions\Form\Aggregate\FormGroupField\Type\AbstractFieldType;

class Upload extends AbstractFieldType
{
    final public const NAME = 'upload';

    public function getName(): string
    {
        return self::NAME;
    }
}
