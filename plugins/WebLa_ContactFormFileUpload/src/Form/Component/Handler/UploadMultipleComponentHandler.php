<?php

declare(strict_types=1);

namespace WebLa_ContactFormFileUpload\Form\Component\Handler;

use Swag\CmsExtensions\Form\Component\Handler\TextComponentHandler;
use WebLa_ContactFormFileUpload\Form\Aggregate\FormGroupField\UploadMultiple;

class UploadMultipleComponentHandler extends TextComponentHandler
{
    public function getComponentType(): string
    {
        return UploadMultiple::NAME;
    }
}
