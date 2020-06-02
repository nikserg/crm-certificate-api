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


    const ERROR_MESSAGE_USER = [
        self::STATUS_NOT_FOUND => 'Не найдено',
        self::STATUS_WRONG => 'Комбинация СНИЛС-ФИО-пол-дата рождения - неверная',
        self::STATUS_WAIT  => 'Запрос отправлен, но ответ не получен',
        self::STATUS_PARSE_ERROR => 'Ошибка разбора ответа',
    ];

    /**
     * Дата создание запроса в СМЭВ
     *
     * @var string
     */
    public $createRequestDate;

    /**
     * Был ли ответ от запроса СМЭВ по СНИЛС
     *
     * @var $isResponse
     */
    public $isResponse;

    public $status;

    public $message;

    public $id;

    public function getErrorMessage() {
        return self::ERROR_MESSAGE_USER[$this->status] ?? 'Тип ошибки для статуса не надена';
    }
}