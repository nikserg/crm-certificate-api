<?php

namespace nikserg\CRMCertificateAPI\models\response;


/**
 * Ответ проверки паспорта
 *
 * @package nikserg\CRMCertificateAPI\models\response
 */
class GetPassportCheck
{
    const STATUS_INIT = 0;
    const STATUS_INVALID = 1;
    const STATUS_VALID = 2;

    /**
     * @var int
     */
    public $status;

    /**
     * @var string
     */
    public $comment;
}