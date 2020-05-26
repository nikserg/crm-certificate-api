<?php

namespace nikserg\CRMCertificateAPI\models\response;


/**
 * Ответ на запрос СНИЛС
 *
 * @package nikserg\CRMCertificateAPI\models\response
 */
class GetSnilsCheck
{

    /**
     * @var  string
     * Запись Esia не найдена
     */
    const STATUS_NOT_FOUND = 'not_found';

    /**
     * @var  string
     *  Комбинация СНИЛС-ФИО-пол-дата рождения -  верные
     */
    const STATUS_SUCCESS = 'success';
    /**
     * @var  string
     * Комбинация СНИЛС-ФИО-пол-дата рождения - неверная
     */
    const STATUS_WRONG = 'wrong';

    /**
     * @var  string
     * Запрос отправлен, но ответ не получен
     */
    const STATUS_WAIT = 'wait';

    /**
     * @var  string
     * Ошибка разбора ответа
     */
    const STATUS_PARSE_ERROR = 'parse_error';

    /**
     * @var  string
     * Запрос в ESIA выдал ошибку
     */
    const STATUS_INTERNAL_ERROR = 'internal_error';

    public $status;

    public $message;
}