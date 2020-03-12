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
class GetCustomerForm
{
    #region статусы заявок на сертификаты
    const STATUS_INIT = 0; //Не заполнено
    const STATUS_CALLBACK = 5; // Требуется связаться с клиентом
    const STATUS_FORMFILLED = 10; //Форма заполнена, документы не прикреплены
    const STATUS_CERTIFICATE_REQUEST_DECLINED = 19; //Запрос сертификата отклонен
    const STATUS_REFILL = 20; //Необходимо повторно заполнить форму
    const STATUS_DOCUMENTSLOADED = 30; //Заполнено, не проверено
    const STATUS_NEED_MANUAL_START = 35; //Требуется одобрение выпуска

    const STATUS_NEED_UNION = 37; //Требуется загрузка заявления на выпуск сертификата
    const STATUS_NEED_UNION_RELOAD = 38; //Ошибка в заявлении
    const STATUS_UNION_LOADED = 39; //Заявление загружено

    const STATUS_VERIFIED = 40; //Заполнено и проверено

    const STATUS_AWAITING_CLIENT = 43; //Ожидается клиент
    const STATUS_CLIENT_VISITED = 44; //Клиент предоставил оригиналы

    const STATUS_LAUNCH = 45; //Выпуск сертификата одобрен

    const STATUS_USER_REQUEST = 50; //Отправлен запрос пользователя
    const STATUS_USER_CREATED = 60; //Пользователь создан
    const STATUS_CERTIFICATE_REQUEST_FORMED = 70; //Запрос сертификата сформирован
    const STATUS_CERTIFICATE_REQUEST_SENDED = 90; //Запрос сертификата отправлен

    const STATUS_CERTIFICATE_SIGNED_REQUEST_FORMED = 100; //Сформирован подписанный запрос сертификата
    const STATUS_CERTIFICATE_SIGNED_REQUEST_SENDED = 110; //Отправлен подписанный запрос сертификата

    const STATUS_AWAITING_BLANK = 115; //Ожидание загрузки бланка сертификата
    const STATUS_AWAITING_BLANK_RESEND = 116; //Ожидание исправления бланка сертификата
    const STATUS_AWAITING_BLANK_ITK_CONFIRM = 117; //Ожидание подтверждения бланка сертификата со стороны ИТК
    const STATUS_AWAITING_BLANK_TRANSFER_CONFIRM = 118; //Ожидание подтверждения бланка сертификата оо стороны партнера-трансфера

    const STATUS_CERTIFICATE_PAUSED = 124; //«Приостановленые» В этом состоянии находятся сертификаты, запрос на приостановление действия которых был одобрен.
    const STATUS_CERTIFICATE_REVOKED = 125; //«Отозванные» В этом состоянии находятся сертификаты, запрос на отзыв  которых был одобрен.
    const STATUS_CERTIFICATE_EXPIRED = 126; //«Просроченные» Сертификаты, срок действия которых истек.
    const STATUS_CERTIFICATE_KEYEXPIRED = 127; //«Просроченный ключ» Сертификаты, для которых истек срок действия соответствующих ключей (задается параметром "KeyValidityPeriod" в "дополнительных параметрах" ЦР).

    const STATUS_CERTIFICATE = 130; //Запрос одобрен, выпущен сертификат

    public static $statusNames = [
        self::STATUS_INIT              => 'Анкета не заполнена',
        self::STATUS_CALLBACK          => 'Требуется связаться с клиентом',
        self::STATUS_FORMFILLED        => 'Анкета заполнена',
        self::STATUS_REFILL            => 'Исправить документы',
        self::STATUS_DOCUMENTSLOADED   => 'Требуется проверка документов',
        self::STATUS_NEED_MANUAL_START => 'Требуется оплата счета',

        self::STATUS_NEED_UNION        => 'Загрузить заявление',
        self::STATUS_NEED_UNION_RELOAD => 'Исправить заявление',
        self::STATUS_UNION_LOADED      => 'Требуется проверка заявления',

        self::STATUS_VERIFIED        => 'Документы и анкета проверены',
        self::STATUS_AWAITING_CLIENT => 'Ожидается клиент',
        self::STATUS_CLIENT_VISITED  => 'Клиент предоставил оригиналы',
        self::STATUS_LAUNCH          => 'Выпуск сертификата одобрен',

        self::STATUS_USER_REQUEST => 'Запрос на создание пользователя отправлен',
        self::STATUS_USER_CREATED => 'Пользователь в ЦР создан',

        self::STATUS_CERTIFICATE_REQUEST_DECLINED => 'Запрос отклонен',
        self::STATUS_CERTIFICATE_REQUEST_FORMED   => 'Запрос на выдачу сертификата сформирован',
        self::STATUS_CERTIFICATE_REQUEST_SENDED   => 'Запрос на выдачу сертификата отправлен',

        self::STATUS_CERTIFICATE_PAUSED     => 'Приостановлен',
        self::STATUS_CERTIFICATE_EXPIRED    => 'Просрочен',
        self::STATUS_CERTIFICATE_REVOKED    => 'Отозван',
        self::STATUS_CERTIFICATE_KEYEXPIRED => 'Ключ просрочен',

        self::STATUS_AWAITING_BLANK                  => 'Ожидание загрузки бланка',
        self::STATUS_AWAITING_BLANK_RESEND           => 'Ожидание исправленного бланка',
        self::STATUS_AWAITING_BLANK_ITK_CONFIRM      => 'Ожидание подтверждения бланка со стороны УЦ',
        self::STATUS_AWAITING_BLANK_TRANSFER_CONFIRM => 'Ожидание подтверждения бланка со стороны партнера-трансфера',

        self::STATUS_CERTIFICATE => 'Выдан',
    ];
    #endregion статусы заявок на сертификаты


    /**
     * Числовой статус заявки
     *
     * @var int
     */
    public $status;

}