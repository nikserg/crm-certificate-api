<?php

namespace nikserg\CRMCertificateAPI\models;


/**
 * Типы оплаты сделок используемые в апи
 *
 * @package nikserg\CRMCertificateAPI\models
 */
class PaymentModes
{
    /**
     * Тип оплаты по неограниченному выпуску. По партнерам на Общих условий
     *
     * @var int
     */
    public const PAYMENT_UNLIMITED = 0;
    /**
     * Тип оплаты Предоплата
     *
     * @var int
     */
    public const PAYMENT_BEFORE = 1;
    /**
     * Тип оплаты Постоплата
     *
     * @var int
     */
    public const PAYMENT_AFTER = 2;
}