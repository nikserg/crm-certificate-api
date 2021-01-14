<?php

namespace nikserg\CRMCertificateAPI\models\response;


/**
 * Ответ на запрос СНИЛС
 *
 * @package nikserg\CRMCertificateAPI\models\response
 */
class SnilsCheck
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $status;
    /**
     * @var string
     */
    public $comment;
    /**
     * @var string
     */
    public $created;
}