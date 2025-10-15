<?php

declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebLa_ContactFormFileUpload\Form\Component\Handler;

use Swag\CmsExtensions\Form\Component\Handler\TextComponentHandler;
use WebLa_ContactFormFileUpload\Form\Aggregate\FormGroupField\Upload;

class UploadComponentHandler extends TextComponentHandler
{
    public function getComponentType(): string
    {
        return Upload::NAME;
    }
}
