<?php

namespace nikserg\CRMCertificateAPI\models\response;


/**
 * Ответ проверки паспорта
 *
 * @package nikserg\CRMCertificateAPI\models\response
 */
class PassportCheck
{
    /**
     * @var int
     */
    public $status;

    /**
     * @var string
     */
    public $comment;
}