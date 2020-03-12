<?php
/**
 * Created by PhpStorm.
 * User: n.zarubin
 * Date: 12.03.2020
 * Time: 11:05
 */

namespace nikserg\CRMCertificateAPI\models\response;

/**
 * Ответ на создание заявки
 *
 * @package nikserg\CRMCertificateAPI\models\response
 */
class SendCustomerForm
{
    /**
     * ID заявки
     *
     * @var int
     */
    public $id;

    /**
     * Токен для редактирования
     *
     * @var string
     */
    public $token;

    /**
     * Токен для генерации
     *
     * @var string
     */
    public $generationToken;

}