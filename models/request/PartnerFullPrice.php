<?php

namespace nikserg\CRMCertificateAPI\models\request;

class PartnerFullPrice
{
    /**
     * Номер пользователя
     *
     * @var int
     */
    public $userId;

    /**
     * Организационно-правовая форма. Необязательное поле. По умолчанию берется legal
     *
     * @var string
     */
    public $legalForm;

    /**
     * Период действие сертфиката необязательное поле по умолчанию берется 1 год
     *
     * @var string $period
     */
    public $period;

    /**
     * Платформы по которым нужно проверить цены
     *
     * @var string[]
     */
    public $platforms;

    /**
     * Номера продуктов предмета коталог
     *
     * @var int[]
     */
    public $products;
}