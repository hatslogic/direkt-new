<?php
declare(strict_types=1);

namespace Tmms\CmsElementPeriodRequestForm\Core\Content\PeriodRequestForm;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class PeriodRequestFormDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'periodrequestform';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return PeriodRequestFormEntity::class;
    }

    public function getCollectionClass(): string
    {
        return PeriodRequestFormCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new StringField('salutation', 'salutation')),
            (new StringField('firstname', 'firstname')),
            (new StringField('lastname', 'lastname')),
            (new StringField('street', 'street')),
            (new StringField('zipcode', 'zipcode')),
            (new StringField('city', 'city')),
            (new StringField('country', 'country')),
            (new StringField('email', 'email')),
            (new StringField('phone', 'phone')),
            (new LongTextField('comment', 'comment'))->addFlags(new AllowHtml()),
            (new StringField('date', 'date')),
            (new StringField('freeinputlabel', 'freeinputlabel')),
            (new LongTextField('freeinput', 'freeinput'))->addFlags(new AllowHtml()),
            (new StringField('freeinput2label', 'freeinput2label')),
            (new LongTextField('freeinput2', 'freeinput2'))->addFlags(new AllowHtml()),
            (new StringField('freeinput3label', 'freeinput3label')),
            (new LongTextField('freeinput3', 'freeinput3'))->addFlags(new AllowHtml()),
            (new StringField('freeinput4label', 'freeinput4label')),
            (new LongTextField('freeinput4', 'freeinput4'))->addFlags(new AllowHtml()),
            (new StringField('freeinput5label', 'freeinput5label')),
            (new LongTextField('freeinput5', 'freeinput5'))->addFlags(new AllowHtml()),
            (new StringField('freeinput6label', 'freeinput6label')),
            (new LongTextField('freeinput6', 'freeinput6'))->addFlags(new AllowHtml()),
            (new StringField('freeinput7label', 'freeinput7label')),
            (new LongTextField('freeinput7', 'freeinput7'))->addFlags(new AllowHtml()),
            (new StringField('freeinput8label', 'freeinput8label')),
            (new LongTextField('freeinput8', 'freeinput8'))->addFlags(new AllowHtml()),
            (new StringField('freeinput9label', 'freeinput9label')),
            (new LongTextField('freeinput9', 'freeinput9'))->addFlags(new AllowHtml()),
            (new StringField('freeinput10label', 'freeinput10label')),
            (new LongTextField('freeinput10', 'freeinput10'))->addFlags(new AllowHtml()),
            (new StringField('origin', 'origin')),
            (new StringField('originid', 'originid')),
            (new StringField('originname', 'originname')),
            (new BoolField('confirmed', 'confirmed')),
            (new BoolField('answered', 'answered')),
        ]);
    }
}
