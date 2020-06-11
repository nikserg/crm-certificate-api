<?php

namespace nikserg\CRMCertificateAPI\models\response\models;

/**
 * Структура описания предмета партнерской платформы из CRM
 */
class PartnerPlatform
{
    /**
     * Наименование
     *
     * @var string
     */
    public $name;
    /**
     * Описание
     *
     * @var string
     */
    public $description;
    /**
     * Цена продажи для клиентов партнера
     *
     * @var float
     */
    public $price;
    /**
     * Тег платформы (текстовый айди)
     *
     * @var string
     */
    public $platform;
}