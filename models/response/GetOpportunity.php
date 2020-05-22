<?php
/**
 * Created by PhpStorm.
 * User: rheola
 * Date: 22.05.20
 * Time: 13:59
 */

namespace nikserg\CRMCertificateAPI\models\response;


/**
 * Ответ на запрос Сделки
 *
 * @package nikserg\CRMCertificateAPI\models\response
 */
class GetOpportunity
{
    /**
     * Если оплата
     *
     * @see Status::$names
     * @var int
     */
    public $isPay;


    /**
     * Номер клиента из CRM.
     *
     * @var int
     */
    public $accountId;

    /**
     * Токен платежа
     *
     * @var int
     */
    public $paymentToken;
}