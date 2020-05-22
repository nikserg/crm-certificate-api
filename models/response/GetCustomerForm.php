<?php
/**
 * Created by PhpStorm.
 * User: n.zarubin
 * Date: 12.03.2020
 * Time: 11:05
 */

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
     * Номер клиента
     *
     * @var int
     */
    public $accountId;

    public $tokenCertificate;
}