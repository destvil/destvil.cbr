<?php

namespace destvil\cbr\Control;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\EventResult;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\FloatField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\Type\DateTime;

class CurrencyTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'des_currency';
    }

    public static function getMap(): array
    {
        return [
            (new StringField('code'))
                ->configurePrimary(),

            (new DatetimeField('date'))
                ->configureRequired()
                ->configureDefaultValue(new DateTime()),

            (new FloatField('course'))
                ->configureRequired()
        ];
    }

    public static function onBeforeUpdate(Event $event)
    {
        $result = new EventResult();

        $fields = $event->getParameter('fields');
        if ($fields['date']) {
            return $result;
        }

        $result->modifyFields([
            'date' => new DateTime()
        ]);

        return $result;
    }
}