<?php


namespace nikserg\CRMCertificateAPI\models\response;


class GetCheckRef
{

    public const PAYMENT_UNLIMITED = 0;   //Неограниченный выпуск
    public const PAYMENT_BEFORE = 1;      //Предоплата
    public const PAYMENT_AFTER = 2;       //Постоплата

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
}