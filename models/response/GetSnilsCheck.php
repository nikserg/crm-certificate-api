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
     * Запись Esia не найдена
     */
    const STATUS_NOT_FOUND = 'not_found';
    /**
     *  Комбинация СНИЛС-ФИО-пол-дата рождения -  верные
     */
    const STATUS_SUCCESS = 'success';
    /**
     * Комбинация СНИЛС-ФИО-пол-дата рождения - неверная
     */
    const STATUS_WRONG = 'wrong';
    /**
     * Запрос отправлен, но ответ не получен
     */
    const STATUS_WAIT = 'wait';
    /**
     * Ошибка разбора ответа
     */
    const STATUS_PARSE_ERROR = 'parse_error';
    /**
     * Запрос в ESIA выдал ошибку
     */
    const STATUS_INTERNAL_ERROR = 'internal_error';

    const ERROR_MESSAGE_USER = [
        self::STATUS_INTERNAL_ERROR => 'Внутренняя ошибка сервиса проверки СНИЛС',
        self::STATUS_NOT_FOUND      => 'Не найдено',
        self::STATUS_WRONG          => 'Комбинация данных неверная',
        self::STATUS_WAIT           => 'Запрос отправлен, но ответ не получен',
        self::STATUS_PARSE_ERROR    => 'Ошибка разбора ответа',
    ];

    /**
     * Дата создание запроса в СМЭВ
     *
     * @var string
     */
    public $createRequestDate;
    public $status;
    public $message;
    public $id;

    public function getErrorMessage()
    {
        return self::ERROR_MESSAGE_USER[$this->status] ?? "Неожиданный статус проверки: $this->status";
    }
}