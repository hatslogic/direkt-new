<?php
declare(strict_types=1);

namespace Tmms\CmsElementPeriodRequestForm;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Shopware\Core\Content\MailTemplate\Aggregate\MailTemplateType\MailTemplateTypeEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Uuid\Uuid;

class TmmsCmsElementPeriodRequestForm extends Plugin
{
    public const TEMPLATE_TYPE_NAME_DE = 'Anfrageformular';
    public const TEMPLATE_TYPE_NAME_EN = 'request form';
    public const TEMPLATE_TYPE_TECHNICAL_NAME = 'tmms_request_form';

    public function install(InstallContext $installContext): void
    {
        $mailTemplateTypeId = $this->addMailTemplateType($installContext->getContext());

        $this->addMailTemplate($installContext->getContext(), $mailTemplateTypeId);

        parent::install($installContext);
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            parent::uninstall($uninstallContext);

            return;
        }

        $mailTemplateTypeId = $this->getMailTemplateTypeId($uninstallContext->getContext());

        $this->removeMailTemplate($uninstallContext->getContext(), $mailTemplateTypeId);

        $this->removeMailTemplateType($uninstallContext->getContext(), $mailTemplateTypeId);

        $this->removePeriodRequestForm();

        parent::uninstall($uninstallContext);
    }

    private function addMailTemplateType($installContext): string
    {
        /** @var EntityRepository $mailTemplateTypeRepository */
        $mailTemplateTypeRepository = $this->container->get('mail_template_type.repository');

        $mailTemplateTypeId = Uuid::randomHex();

        $mailTemplateTypeData = [
            [
                'id' => $mailTemplateTypeId,
                'technicalName' => self::TEMPLATE_TYPE_TECHNICAL_NAME,
                'name' => [
                    'en-GB' => self::TEMPLATE_TYPE_NAME_EN,
                    'de-DE' => self::TEMPLATE_TYPE_NAME_DE,
                    Defaults::LANGUAGE_SYSTEM => self::TEMPLATE_TYPE_NAME_EN,
                ],
                'availableEntities' => [
                    'salesChannel' => 'sales_channel',
                ],
            ],
        ];

        try {
            $mailTemplateTypeRepository->create($mailTemplateTypeData, $installContext);

            return $mailTemplateTypeId;
        } catch (UniqueConstraintViolationException $exception) {
        }

        return "";
    }

    private function addMailTemplate($installContext, string $mailTemplateTypeId): void
    {
        /** @var EntityRepository $mailTemplateRepository */
        $mailTemplateRepository = $this->container->get('mail_template.repository');

        $mailTemplate = [
            [
                'id' => Uuid::randomHex(),
                'mailTemplateTypeId' => $mailTemplateTypeId,
                'subject' => [
                    'en-GB' => 'request form received - {{ salesChannel.name }}',
                    'de-DE' => 'Anfrage erhalten - {{ salesChannel.name }}',
                    Defaults::LANGUAGE_SYSTEM => 'request form received - {{ salesChannel.name }}',
                ],
                'description' => [
                    'en-GB' => self::TEMPLATE_TYPE_NAME_EN,
                    'de-DE' => self::TEMPLATE_TYPE_NAME_DE,
                    Defaults::LANGUAGE_SYSTEM => self::TEMPLATE_TYPE_NAME_EN,
                ],
                'senderName' => [
                    'en-GB' => '{{ salesChannel.name }}',
                    'de-DE' => '{{ salesChannel.name }}',
                    Defaults::LANGUAGE_SYSTEM => '{{ salesChannel.name }}',
                ],
                'contentPlain' => [
                    'en-GB' => "Hello, following data were transmitted.\n\nOrigin: {{ periodRequestFormData.origin }} \nOrigin value: {{ periodRequestFormData.originName }} ({{ periodRequestFormData.originId }}) \n{% if periodRequestFormData.salutation != \"\" %}Salutation: {{ periodRequestFormData.salutation }} \n{% endif %}{% if periodRequestFormData.firstname != \"\" %}Firstname: {{ periodRequestFormData.firstname }} \n{% endif %}{% if periodRequestFormData.lastname != \"\" %}Lastname: {{ periodRequestFormData.lastname }} \n{% endif %}{% if periodRequestFormData.street != \"\" %}Street address: {{ periodRequestFormData.street }} \n{% endif %}{% if periodRequestFormData.zipcode != \"\" %}Postal code: {{ periodRequestFormData.zipcode }} \n{% endif %}{% if periodRequestFormData.city != \"\" %}City: {{ periodRequestFormData.city }} \n{% endif %}{% if periodRequestFormData.country != \"\" %}Country: {{ periodRequestFormData.country }} \n{% endif %}{% if periodRequestFormData.email != \"\" %}Mail: {{ periodRequestFormData.email }} \n{% endif %}{% if periodRequestFormData.phone != \"\" %}Phone: {{ periodRequestFormData.phone }} \n{% endif %}{% if periodRequestFormData.comment != \"\" %}Comment: {{ periodRequestFormData.comment }} \n{% endif %}{% if periodRequestFormData.date != \"\" %}{{ periodRequestFormData.labelDate }}: {{ periodRequestFormData.date }}\n{% endif %}{% if periodRequestFormData.freeInput != \"\" %}{{ periodRequestFormData.freeInputLabel }}: {{ periodRequestFormData.freeInput }}\n{% endif %}{% if periodRequestFormData.freeInput2 != \"\" %}{{ periodRequestFormData.freeInput2Label }}: {{ periodRequestFormData.freeInput2 }}\n{% endif %}{% if periodRequestFormData.freeInput3 != \"\" %}{{ periodRequestFormData.freeInput3Label }}: {{ periodRequestFormData.freeInput3 }}\n{% endif %}{% if periodRequestFormData.freeInput4 != \"\" %}{{ periodRequestFormData.freeInput4Label }}: {{ periodRequestFormData.freeInput4 }}\n{% endif %}{% if periodRequestFormData.freeInput5 != \"\" %}{{ periodRequestFormData.freeInput5Label }}: {{ periodRequestFormData.freeInput5 }}\n{% endif %}{% if periodRequestFormData.freeInput6 != \"\" %}{{ periodRequestFormData.freeInput6Label }}: {{ periodRequestFormData.freeInput6 }}\n{% endif %}{% if periodRequestFormData.freeInput7 != \"\" %}{{ periodRequestFormData.freeInput7Label }}: {{ periodRequestFormData.freeInput7 }}\n{% endif %}{% if periodRequestFormData.freeInput8 != \"\" %}{{ periodRequestFormData.freeInput8Label }}: {{ periodRequestFormData.freeInput8 }}\n{% endif %}{% if periodRequestFormData.freeInput9 != \"\" %}{{ periodRequestFormData.freeInput9Label }}: {{ periodRequestFormData.freeInput9 }}\n{% endif %}{% if periodRequestFormData.freeInput10 != \"\" %}{{ periodRequestFormData.freeInput10Label }}: {{ periodRequestFormData.freeInput10 }}\n{% endif %}\nKind Regards,\nYours",
                    'de-DE' => "Hallo, die nachfolgenden Daten wurden übermittelt.\n\nHerkunft: {{ periodRequestFormData.origin }} \nHerkunftswert: {{ periodRequestFormData.originName }} ({{ periodRequestFormData.originId }}) \n{% if periodRequestFormData.salutation != \"\" %}Anrede: {{ periodRequestFormData.salutation }} \n{% endif %}{% if periodRequestFormData.firstname != \"\" %}Vorname: {{ periodRequestFormData.firstname }} \n{% endif %}{% if periodRequestFormData.lastname != \"\" %}Nachname: {{ periodRequestFormData.lastname }} \n{% endif %}{% if periodRequestFormData.street != \"\" %}Straße: {{ periodRequestFormData.street }} \n{% endif %}{% if periodRequestFormData.zipcode != \"\" %}Postleitzahl: {{ periodRequestFormData.zipcode }} \n{% endif %}{% if periodRequestFormData.city != \"\" %}Ort: {{ periodRequestFormData.city }} \n{% endif %}{% if periodRequestFormData.country != \"\" %}Land: {{ periodRequestFormData.country }} \n{% endif %}{% if periodRequestFormData.email != \"\" %}E-Mail: {{ periodRequestFormData.email }} \n{% endif %}{% if periodRequestFormData.phone != \"\" %}Telefon: {{ periodRequestFormData.phone }} \n{% endif %}{% if periodRequestFormData.comment != \"\" %}Kommentar: {{ periodRequestFormData.comment }} \n{% endif %}{% if periodRequestFormData.date != \"\" %}{{ periodRequestFormData.labelDate }}: {{ periodRequestFormData.date }}\n{% endif %}{% if periodRequestFormData.freeInput != \"\" %}{{ periodRequestFormData.freeInputLabel }}: {{ periodRequestFormData.freeInput }}\n{% endif %}{% if periodRequestFormData.freeInput2 != \"\" %}{{ periodRequestFormData.freeInput2Label }}: {{ periodRequestFormData.freeInput2 }}\n{% endif %}{% if periodRequestFormData.freeInput3 != \"\" %}{{ periodRequestFormData.freeInput3Label }}: {{ periodRequestFormData.freeInput3 }}\n{% endif %}{% if periodRequestFormData.freeInput4 != \"\" %}{{ periodRequestFormData.freeInput4Label }}: {{ periodRequestFormData.freeInput4 }}\n{% endif %}{% if periodRequestFormData.freeInput5 != \"\" %}{{ periodRequestFormData.freeInput5Label }}: {{ periodRequestFormData.freeInput5 }}\n{% endif %}{% if periodRequestFormData.freeInput6 != \"\" %}{{ periodRequestFormData.freeInput6Label }}: {{ periodRequestFormData.freeInput6 }}\n{% endif %}{% if periodRequestFormData.freeInput7 != \"\" %}{{ periodRequestFormData.freeInput7Label }}: {{ periodRequestFormData.freeInput7 }}\n{% endif %}{% if periodRequestFormData.freeInput8 != \"\" %}{{ periodRequestFormData.freeInput8Label }}: {{ periodRequestFormData.freeInput8 }}\n{% endif %}{% if periodRequestFormData.freeInput9 != \"\" %}{{ periodRequestFormData.freeInput9Label }}: {{ periodRequestFormData.freeInput9 }}\n{% endif %}{% if periodRequestFormData.freeInput10 != \"\" %}{{ periodRequestFormData.freeInput10Label }}: {{ periodRequestFormData.freeInput10 }}\n{% endif %}\nViele Grüße,\nIhr",
                    Defaults::LANGUAGE_SYSTEM => "Hello, following data were transmitted.\n\nOrigin: {{ periodRequestFormData.origin }} \nOrigin value: {{ periodRequestFormData.originName }} ({{ periodRequestFormData.originId }}) \n{% if periodRequestFormData.salutation != \"\" %}Salutation: {{ periodRequestFormData.salutation }} \n{% endif %}{% if periodRequestFormData.firstname != \"\" %}Firstname: {{ periodRequestFormData.firstname }} \n{% endif %}{% if periodRequestFormData.lastname != \"\" %}Lastname: {{ periodRequestFormData.lastname }} \n{% endif %}{% if periodRequestFormData.street != \"\" %}Street address: {{ periodRequestFormData.street }} \n{% endif %}{% if periodRequestFormData.zipcode != \"\" %}Postal code: {{ periodRequestFormData.zipcode }} \n{% endif %}{% if periodRequestFormData.city != \"\" %}City: {{ periodRequestFormData.city }} \n{% endif %}{% if periodRequestFormData.country != \"\" %}Country: {{ periodRequestFormData.country }} \n{% endif %}{% if periodRequestFormData.email != \"\" %}Mail: {{ periodRequestFormData.email }} \n{% endif %}{% if periodRequestFormData.phone != \"\" %}Phone: {{ periodRequestFormData.phone }} \n{% endif %}{% if periodRequestFormData.comment != \"\" %}Comment: {{ periodRequestFormData.comment }} \n{% endif %}{% if periodRequestFormData.date != \"\" %}{{ periodRequestFormData.labelDate }}: {{ periodRequestFormData.date }}\n{% endif %}{% if periodRequestFormData.freeInput != \"\" %}{{ periodRequestFormData.freeInputLabel }}: {{ periodRequestFormData.freeInput }}\n{% endif %}{% if periodRequestFormData.freeInput2 != \"\" %}{{ periodRequestFormData.freeInput2Label }}: {{ periodRequestFormData.freeInput2 }}\n{% endif %}{% if periodRequestFormData.freeInput3 != \"\" %}{{ periodRequestFormData.freeInput3Label }}: {{ periodRequestFormData.freeInput3 }}\n{% endif %}{% if periodRequestFormData.freeInput4 != \"\" %}{{ periodRequestFormData.freeInput4Label }}: {{ periodRequestFormData.freeInput4 }}\n{% endif %}{% if periodRequestFormData.freeInput5 != \"\" %}{{ periodRequestFormData.freeInput5Label }}: {{ periodRequestFormData.freeInput5 }}\n{% endif %}{% if periodRequestFormData.freeInput6 != \"\" %}{{ periodRequestFormData.freeInput6Label }}: {{ periodRequestFormData.freeInput6 }}\n{% endif %}{% if periodRequestFormData.freeInput7 != \"\" %}{{ periodRequestFormData.freeInput7Label }}: {{ periodRequestFormData.freeInput7 }}\n{% endif %}{% if periodRequestFormData.freeInput8 != \"\" %}{{ periodRequestFormData.freeInput8Label }}: {{ periodRequestFormData.freeInput8 }}\n{% endif %}{% if periodRequestFormData.freeInput9 != \"\" %}{{ periodRequestFormData.freeInput9Label }}: {{ periodRequestFormData.freeInput9 }}\n{% endif %}{% if periodRequestFormData.freeInput10 != \"\" %}{{ periodRequestFormData.freeInput10Label }}: {{ periodRequestFormData.freeInput10 }}\n{% endif %}\nKind Regards,\nYours",
                ],
                'contentHtml' => [
                    'en-GB' => '<div style="font-size:12px;">Hello,<br>the following data were transmitted.<br/><br/>Origin: {{ periodRequestFormData.origin }}<br/>Origin value: {{ periodRequestFormData.originName }} ({{ periodRequestFormData.originId }})<br/>{% if periodRequestFormData.salutation != "" %}Salutation: {{ periodRequestFormData.salutation }}<br/>{% endif %}{% if periodRequestFormData.firstname != "" %}Firstname: {{ periodRequestFormData.firstname }}<br/>{% endif %}{% if periodRequestFormData.lastname != "" %}Lastname: {{ periodRequestFormData.lastname }}<br/>{% endif %}{% if periodRequestFormData.street != "" %}Street address: {{ periodRequestFormData.street }}<br/>{% endif %}{% if periodRequestFormData.zipcode != "" %}Postal code: {{ periodRequestFormData.zipcode }}<br/>{% endif %}{% if periodRequestFormData.city != "" %}City: {{ periodRequestFormData.city }}<br/>{% endif %}{% if periodRequestFormData.country != "" %}Country: {{ periodRequestFormData.country }}<br/>{% endif %}{% if periodRequestFormData.email != "" %}Mail: {{ periodRequestFormData.email }}<br/>{% endif %}{% if periodRequestFormData.phone != "" %}Phone: {{ periodRequestFormData.phone }}<br/>{% endif %}{% if periodRequestFormData.comment != "" %}Comment: {{ periodRequestFormData.comment }}<br/>{% endif %}{% if periodRequestFormData.date != "" %}{{ periodRequestFormData.labelDate }}: {{ periodRequestFormData.date }}<br/>{% endif %}{% if periodRequestFormData.freeInput != "" %}{{ periodRequestFormData.freeInputLabel }}: {{ periodRequestFormData.freeInput }}<br/>{% endif %}{% if periodRequestFormData.freeInput2 != "" %}{{ periodRequestFormData.freeInput2Label }}: {{ periodRequestFormData.freeInput2 }}<br/>{% endif %}{% if periodRequestFormData.freeInput3 != "" %}{{ periodRequestFormData.freeInput3Label }}: {{ periodRequestFormData.freeInput3 }}<br/>{% endif %}{% if periodRequestFormData.freeInput4 != "" %}{{ periodRequestFormData.freeInput4Label }}: {{ periodRequestFormData.freeInput4 }}<br/>{% endif %}{% if periodRequestFormData.freeInput5 != "" %}{{ periodRequestFormData.freeInput5Label }}: {{ periodRequestFormData.freeInput5 }}<br/>{% endif %}{% if periodRequestFormData.freeInput6 != "" %}{{ periodRequestFormData.freeInput6Label }}: {{ periodRequestFormData.freeInput6 }}<br/>{% endif %}{% if periodRequestFormData.freeInput7 != "" %}{{ periodRequestFormData.freeInput7Label }}: {{ periodRequestFormData.freeInput7 }}<br/>{% endif %}{% if periodRequestFormData.freeInput8 != "" %}{{ periodRequestFormData.freeInput8Label }}: {{ periodRequestFormData.freeInput8 }}<br/>{% endif %}{% if periodRequestFormData.freeInput9 != "" %}{{ periodRequestFormData.freeInput9Label }}: {{ periodRequestFormData.freeInput9 }}<br/>{% endif %}{% if periodRequestFormData.freeInput10 != "" %}{{ periodRequestFormData.freeInput10Label }}: {{ periodRequestFormData.freeInput10 }}<br/>{% endif %}<br/>Kind Regards,<br/>Yours</div>',
                    'de-DE' => '<div style="font-size:12px;">Hallo,<br>die nachfolgenden Daten wurden übermittelt.<br/><br/>Herkunft: {{ periodRequestFormData.origin }}<br/>Herkunftswert: {{ periodRequestFormData.originName }} ({{ periodRequestFormData.originId }})<br/>{% if periodRequestFormData.salutation != "" %}Anrede: {{ periodRequestFormData.salutation }}<br/>{% endif %}{% if periodRequestFormData.firstname != "" %}Vorname: {{ periodRequestFormData.firstname }}<br/>{% endif %}{% if periodRequestFormData.lastname != "" %}Nachname: {{ periodRequestFormData.lastname }}<br/>{% endif %}{% if periodRequestFormData.street != "" %}Straße: {{ periodRequestFormData.street }}<br/>{% endif %}{% if periodRequestFormData.zipcode != "" %}Postleitzahl: {{ periodRequestFormData.zipcode }}<br/>{% endif %}{% if periodRequestFormData.city != "" %}Ort: {{ periodRequestFormData.city }}<br/>{% endif %}{% if periodRequestFormData.country != "" %}Land: {{ periodRequestFormData.country }}<br/>{% endif %}{% if periodRequestFormData.email != "" %}E-Mail: {{ periodRequestFormData.email }}<br/>{% endif %}{% if periodRequestFormData.phone != "" %}Telefon: {{ periodRequestFormData.phone }}<br/>{% endif %}{% if periodRequestFormData.comment != "" %}Kommentar: {{ periodRequestFormData.comment }}<br/>{% endif %}{% if periodRequestFormData.date != "" %}{{ periodRequestFormData.labelDate }}: {{ periodRequestFormData.date }}<br/>{% endif %}{% if periodRequestFormData.freeInput != "" %}{{ periodRequestFormData.freeInputLabel }}: {{ periodRequestFormData.freeInput }}<br/>{% endif %}{% if periodRequestFormData.freeInput2 != "" %}{{ periodRequestFormData.freeInput2Label }}: {{ periodRequestFormData.freeInput2 }}<br/>{% endif %}{% if periodRequestFormData.freeInput3 != "" %}{{ periodRequestFormData.freeInput3Label }}: {{ periodRequestFormData.freeInput3 }}<br/>{% endif %}{% if periodRequestFormData.freeInput4 != "" %}{{ periodRequestFormData.freeInput4Label }}: {{ periodRequestFormData.freeInput4 }}<br/>{% endif %}{% if periodRequestFormData.freeInput5 != "" %}{{ periodRequestFormData.freeInput5Label }}: {{ periodRequestFormData.freeInput5 }}<br/>{% endif %}{% if periodRequestFormData.freeInput6 != "" %}{{ periodRequestFormData.freeInput6Label }}: {{ periodRequestFormData.freeInput6 }}<br/>{% endif %}{% if periodRequestFormData.freeInput7 != "" %}{{ periodRequestFormData.freeInput7Label }}: {{ periodRequestFormData.freeInput7 }}<br/>{% endif %}{% if periodRequestFormData.freeInput8 != "" %}{{ periodRequestFormData.freeInput8Label }}: {{ periodRequestFormData.freeInput8 }}<br/>{% endif %}{% if periodRequestFormData.freeInput9 != "" %}{{ periodRequestFormData.freeInput9Label }}: {{ periodRequestFormData.freeInput9 }}<br/>{% endif %}{% if periodRequestFormData.freeInput10 != "" %}{{ periodRequestFormData.freeInput10Label }}: {{ periodRequestFormData.freeInput10 }}<br/>{% endif %}<br/>Viele Grüße,<br/>Ihr</div>',
                    Defaults::LANGUAGE_SYSTEM => '<div style="font-size:12px;">Hello,<br>the following data were transmitted.<br/><br/>Origin: {{ periodRequestFormData.origin }}<br/>Origin value: {{ periodRequestFormData.originName }} ({{ periodRequestFormData.originId }})<br/>{% if periodRequestFormData.salutation != "" %}Salutation: {{ periodRequestFormData.salutation }}<br/>{% endif %}{% if periodRequestFormData.firstname != "" %}Firstname: {{ periodRequestFormData.firstname }}<br/>{% endif %}{% if periodRequestFormData.lastname != "" %}Lastname: {{ periodRequestFormData.lastname }}<br/>{% endif %}{% if periodRequestFormData.street != "" %}Street address: {{ periodRequestFormData.street }}<br/>{% endif %}{% if periodRequestFormData.zipcode != "" %}Postal code: {{ periodRequestFormData.zipcode }}<br/>{% endif %}{% if periodRequestFormData.city != "" %}City: {{ periodRequestFormData.city }}<br/>{% endif %}{% if periodRequestFormData.country != "" %}Country: {{ periodRequestFormData.country }}<br/>{% endif %}{% if periodRequestFormData.email != "" %}Mail: {{ periodRequestFormData.email }}<br/>{% endif %}{% if periodRequestFormData.phone != "" %}Phone: {{ periodRequestFormData.phone }}<br/>{% endif %}{% if periodRequestFormData.comment != "" %}Comment: {{ periodRequestFormData.comment }}<br/>{% endif %}{% if periodRequestFormData.date != "" %}{{ periodRequestFormData.labelDate }}: {{ periodRequestFormData.date }}<br/>{% endif %}{% if periodRequestFormData.freeInput != "" %}{{ periodRequestFormData.freeInputLabel }}: {{ periodRequestFormData.freeInput }}<br/>{% endif %}{% if periodRequestFormData.freeInput2 != "" %}{{ periodRequestFormData.freeInput2Label }}: {{ periodRequestFormData.freeInput2 }}<br/>{% endif %}{% if periodRequestFormData.freeInput3 != "" %}{{ periodRequestFormData.freeInput3Label }}: {{ periodRequestFormData.freeInput3 }}<br/>{% endif %}{% if periodRequestFormData.freeInput4 != "" %}{{ periodRequestFormData.freeInput4Label }}: {{ periodRequestFormData.freeInput4 }}<br/>{% endif %}{% if periodRequestFormData.freeInput5 != "" %}{{ periodRequestFormData.freeInput5Label }}: {{ periodRequestFormData.freeInput5 }}<br/>{% endif %}{% if periodRequestFormData.freeInput6 != "" %}{{ periodRequestFormData.freeInput6Label }}: {{ periodRequestFormData.freeInput6 }}<br/>{% endif %}{% if periodRequestFormData.freeInput7 != "" %}{{ periodRequestFormData.freeInput7Label }}: {{ periodRequestFormData.freeInput7 }}<br/>{% endif %}{% if periodRequestFormData.freeInput8 != "" %}{{ periodRequestFormData.freeInput8Label }}: {{ periodRequestFormData.freeInput8 }}<br/>{% endif %}{% if periodRequestFormData.freeInput9 != "" %}{{ periodRequestFormData.freeInput9Label }}: {{ periodRequestFormData.freeInput9 }}<br/>{% endif %}{% if periodRequestFormData.freeInput10 != "" %}{{ periodRequestFormData.freeInput10Label }}: {{ periodRequestFormData.freeInput10 }}<br/>{% endif %}<br/>Kind Regards,<br/>Yours</div>',
                ],
            ],
        ];

        try {
            $mailTemplateRepository->create($mailTemplate, $installContext);
        } catch (UniqueConstraintViolationException $exception) {
        }
    }

    private function getMailTemplateTypeId($uninstallContext): string
    {
        /** @var EntityRepository $mailTemplateTypeRepository */
        $mailTemplateTypeRepository = $this->container->get('mail_template_type.repository');

        /** @var MailTemplateTypeEntity $mailTemplateType */
        $mailTemplateType = $mailTemplateTypeRepository->search(
            (new Criteria())->addFilter(new EqualsFilter('technicalName', self::TEMPLATE_TYPE_TECHNICAL_NAME)),
            $uninstallContext
        )->first();

        return $mailTemplateType->getId();
    }

    private function removeMailTemplate($uninstallContext, string $mailTemplateTypeId): void
    {
        /** @var EntityRepository $mailTemplateRepository */
        $mailTemplateRepository = $this->container->get('mail_template.repository');

        $mailTemplateIds = $mailTemplateRepository->searchIds(
            (new Criteria())->addFilter(new EqualsFilter('mailTemplateTypeId', $mailTemplateTypeId)),
            $uninstallContext
        )->getIds();

        $ids = array_map(static function ($id) { return ['id' => $id]; }, $mailTemplateIds);

        $mailTemplateRepository->delete($ids, $uninstallContext);
    }

    private function removeMailTemplateType($uninstallContext, string $mailTemplateTypeId): void
    {
        /** @var EntityRepository $mailTemplateTypeRepository */
        $mailTemplateTypeRepository = $this->container->get('mail_template_type.repository');
        $mailTemplateTypeRepository->delete([['id' => $mailTemplateTypeId]], $uninstallContext);
    }

    private function removePeriodRequestForm(): void
    {
        $connection = $this->container->get(Connection::class);
        $connection->executeStatement('DROP TABLE IF EXISTS `periodrequestform`');
    }
}
