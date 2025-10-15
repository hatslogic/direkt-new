<?php

declare(strict_types=1);

namespace WebLa_ContactFormFileUpload\Form\Aggregate\FormGroupField;

use Swag\CmsExtensions\Form\Aggregate\FormGroupField\Type\AbstractFieldType;

class UploadMultiple extends AbstractFieldType
{
    final public const NAME = 'upload-multiple';

    public function getName(): string
    {
        return self::NAME;
    }
}
