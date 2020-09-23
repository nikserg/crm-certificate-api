<?php

namespace nikserg\CRMCertificateAPI\models\response;

use nikserg\CRMCertificateAPI\models\data\Status;

/**
 * Ответ на запрос заявки
 *
 * @package nikserg\CRMCertificateAPI\models\response
 */
class GetCustomerForm
{
    /**
     * Числовой статус заявки
     *
     * @see Status::$names
     * @var int
     */
    public $status;

    /**
     * Номер сделки
     *
     * @var int
     */
    public $opportunityId;

    /**
     * Владелец
     *
     * @var string
     */
    public $owner;

    /**
     * Номер клиента
     *
     * @var int
     */
    public $accountId;

    /**
     * Токен из сертификата.
     *
     * @var string
     */
    public $token;

    /**
     * Есть ли подтверждение заявки для ПО и ПР партнеров или есть оплата для сделки для ОУ партнера.
     *
     * @var bool
     */
    public $isPay;

    /**
     * Полная стоимость заявки
     *
     * @var float|null
     */
    public $totalPrice;
}