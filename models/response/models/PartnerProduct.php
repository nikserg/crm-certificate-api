<?php

namespace nikserg\CRMCertificateAPI\models\response\models;

/**
 * Структура описания предмета партнерского продукта из CRM
 */
class PartnerProduct
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
     * ID продукта в црм
     *
     * @var int
     */
    public $id;
}