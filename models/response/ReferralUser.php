<?php


namespace nikserg\CRMCertificateAPI\models\response;

use nikserg\CRMCertificateAPI\models\PaymentModes;

/**
 * Структура пользователя реферала
 *
 * @package nikserg\CRMCertificateAPI\models\response
 */
class ReferralUser
{
    /**
     * Номер партнера в заявки
     *
     * @var int
     */
    public $id;

    /**
     * Тип оплаты сделки
     *
     * @var int
     * @see PaymentModes
     */
    public $paymentMode;

    /**
     * Имя пользователя
     *
     * @var string
     */
    public $userName;

    /**
     * Адрес почты пользователя (для уведомлений и прочего)
     *
     * @var ?string
     */
    public $email;

    /**
     * Телефон для связи с пользователем
     *
     * @var ?string
     */
    public $phone;

    /**
     * Является-ли пользователь ОФД (?)
     *
     * @var bool
     */
    public $isOfd;

    /**
     * Есть-ли у пользователя настройки продуктов на фрейма
     *
     * @var bool
     */
    public $enablePlatformSelection;

}