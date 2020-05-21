<?php
namespace nikserg\CRMCertificateAPI\models\data;
class Status {
    const INIT = 0; //Не заполнено
    const CALLBACK = 5; // Требуется связаться с клиентом
    const FORMFILLED = 10; //Форма заполнена, документы не прикреплены
    const CERTIFICATE_REQUEST_DECLINED = 19; //Запрос сертификата отклонен
    const REFILL = 20; //Необходимо повторно заполнить форму
    const DOCUMENTSLOADED = 30; //Заполнено, не проверено
    const NEED_MANUAL_START = 35; //Требуется одобрение выпуска

    const NEED_UNION = 37; //Требуется загрузка заявления на выпуск сертификата
    const NEED_UNION_RELOAD = 38; //Ошибка в заявлении
    const UNION_LOADED = 39; //Заявление загружено

    const VERIFIED = 40; //Заполнено и проверено

    const AWAITING_CLIENT = 43; //Ожидается клиент
    const CLIENT_VISITED = 44; //Клиент предоставил оригиналы

    const LAUNCH = 45; //Выпуск сертификата одобрен

    const USER_REQUEST = 50; //Отправлен запрос пользователя
    const USER_CREATED = 60; //Пользователь создан
    const CERTIFICATE_REQUEST_FORMED = 70; //Запрос сертификата сформирован
    const CERTIFICATE_REQUEST_SENDED = 90; //Запрос сертификата отправлен

    const CERTIFICATE_SIGNED_REQUEST_FORMED = 100; //Сформирован подписанный запрос сертификата
    const CERTIFICATE_SIGNED_REQUEST_SENDED = 110; //Отправлен подписанный запрос сертификата

    const AWAITING_BLANK = 115; //Ожидание загрузки бланка сертификата
    const AWAITING_BLANK_RESEND = 116; //Ожидание исправления бланка сертификата
    const AWAITING_BLANK_ITK_CONFIRM = 117; //Ожидание подтверждения бланка сертификата со стороны ИТК
    const AWAITING_BLANK_TRANSFER_CONFIRM = 118; //Ожидание подтверждения бланка сертификата оо стороны партнера-трансфера

    const CERTIFICATE_PAUSED = 124; //«Приостановленые» В этом состоянии находятся сертификаты, запрос на приостановление действия которых был одобрен.
    const CERTIFICATE_REVOKED = 125; //«Отозванные» В этом состоянии находятся сертификаты, запрос на отзыв  которых был одобрен.
    const CERTIFICATE_EXPIRED = 126; //«Просроченные» Сертификаты, срок действия которых истек.
    const CERTIFICATE_KEYEXPIRED = 127; //«Просроченный ключ» Сертификаты, для которых истек срок действия соответствующих ключей (задается параметром "KeyValidityPeriod" в "дополнительных параметрах" ЦР).

    const CERTIFICATE = 130; //Запрос одобрен, выпущен сертификат

    public static $names = [
        self::INIT              => 'Анкета не заполнена',
        self::CALLBACK          => 'Требуется связаться с клиентом',
        self::FORMFILLED        => 'Анкета заполнена',
        self::REFILL            => 'Исправить документы',
        self::DOCUMENTSLOADED   => 'Требуется проверка документов',
        self::NEED_MANUAL_START => 'Требуется оплата счета',

        self::NEED_UNION        => 'Загрузить заявление',
        self::NEED_UNION_RELOAD => 'Исправить заявление',
        self::UNION_LOADED      => 'Требуется проверка заявления',

        self::VERIFIED        => 'Документы и анкета проверены',
        self::AWAITING_CLIENT => 'Ожидается клиент',
        self::CLIENT_VISITED  => 'Клиент предоставил оригиналы',
        self::LAUNCH          => 'Выпуск сертификата одобрен',

        self::USER_REQUEST => 'Запрос на создание пользователя отправлен',
        self::USER_CREATED => 'Пользователь в ЦР создан',

        self::CERTIFICATE_REQUEST_DECLINED => 'Запрос отклонен',
        self::CERTIFICATE_REQUEST_FORMED   => 'Запрос на выдачу сертификата сформирован',
        self::CERTIFICATE_REQUEST_SENDED   => 'Запрос на выдачу сертификата отправлен',

        self::CERTIFICATE_PAUSED     => 'Приостановлен',
        self::CERTIFICATE_EXPIRED    => 'Просрочен',
        self::CERTIFICATE_REVOKED    => 'Отозван',
        self::CERTIFICATE_KEYEXPIRED => 'Ключ просрочен',

        self::AWAITING_BLANK                  => 'Ожидание загрузки бланка',
        self::AWAITING_BLANK_RESEND           => 'Ожидание исправленного бланка',
        self::AWAITING_BLANK_ITK_CONFIRM      => 'Ожидание подтверждения бланка со стороны УЦ',
        self::AWAITING_BLANK_TRANSFER_CONFIRM => 'Ожидание подтверждения бланка со стороны партнера-трансфера',

        self::CERTIFICATE => 'Выдан',
    ];
}