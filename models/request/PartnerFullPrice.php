<?php

namespace nikserg\CRMCertificateAPI\models\request;

/**
 * Запрос на получение полной цены на выбранные продукты и платформы партнера
 */
class PartnerFullPrice
{
    /**
     * Номер пользователя партнера
     *
     * @var int
     */
    public $partnerUserId;

    /**
     * Организационно-правовая форма клиента. По умолчанию 'legal'
     *
     * @var string
     */
    public $clientLegalForm;

    /**
     * Период действие сертфиката. По умолчанию берется 1 год
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