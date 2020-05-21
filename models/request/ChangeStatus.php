<?php
/**
 * Created by PhpStorm.
 * User: n.zarubin
 * Date: 12.03.2020
 * Time: 11:05
 */

namespace nikserg\CRMCertificateAPI\models\request;

/**
 * Обновление статуса сертификата
 * Модель для передачи в CRM
 *
 * @package nikserg\CRMCertificateAPI\models\request
 */
class ChangeStatus
{
    public $id;
    public $status;

    public function __construct($id, $status)
    {
        $this->id = $id;
        $this->status = $status;
    }
}