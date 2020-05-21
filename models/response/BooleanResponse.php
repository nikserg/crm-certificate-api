<?php
/**
 * Created by PhpStorm.
 * User: n.zarubin
 * Date: 12.03.2020
 * Time: 11:05
 */

namespace nikserg\CRMCertificateAPI\models\response;

/**
 * Ответ успех/не успех
 *
 * @package nikserg\CRMCertificateAPI\models\response
 */
class BooleanResponse
{
    /**
     * Успех или нет
     *
     * @var bool
     */
    public $status;

    /**
     * @var string
     */
    public $message;
}