<?php


namespace nikserg\CRMCertificateAPI\models\response;

/**
 *  
 *
 * @package nikserg\CRMCertificateAPI\models\response
 */
class GetCheckRef
{

    /** @var int Тип оплаты по неограниченному выпуску. По партнерам на Общих условий  */
    public const PAYMENT_UNLIMITED = 0;
    /** @var int Тип оплаты Предоплата */
    public const PAYMENT_BEFORE = 1;
    /** @var int Тип оплаты Постоплата */
    public const PAYMENT_AFTER = 2;

    /**
     * Номер партнера в заявки
     *
     * @var int $id
     */
    public $id;

    /**
     * Тип оплаты сделки
     *
     * @var int $paymentMode
     */
    public $paymentMode;

    /**
     * Имя пользователя
     *
     * @var string $userName
     */
    public $userName;

}