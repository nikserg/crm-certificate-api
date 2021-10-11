<?php

namespace nikserg\CRMCertificateAPI\models\request;

/**
 * Модель для передачи заявки на сертификат в CRM
 *
 * @package nikserg\CRMCertificateAPI\models\request
 */
class SendCustomerForm
{
    /**
     * ID сделки в CRM, в которую будет добавлена заявка
     *
     * @var int
     */
    public $opportunityId;

    /**
     * Массив площадок
     *
     * @var array
     */
    public $platforms;
    const PLATFORM_EPGU = 'EPGU';
    const PLATFORM_EPGU_NEP = 'EPGU_NEP';

    /**
     * Организационно-правовая форма
     *
     * @see CUSTOMER_LEGAL_FORM_LEGAL
     * @var string
     */
    public $customerLegalForm;
    const CUSTOMER_LEGAL_FORM_LEGAL = 'legal';           //Юр. лицо
    const CUSTOMER_LEGAL_FORM_INDIVIDUAL = 'individual'; //ИП
    const CUSTOMER_LEGAL_FORM_PERSON = 'person';         //Физ. лицо

    /**
     * Если передан, изменяется запись с указанным ID, иначе создается новая заявка
     *
     * @var int
     */
    public $id;

    /**
     * Криптопровайдер
     *
     * @see CRYPTOPROVIDER_CRYPTOPRO_2012
     * @var string
     */
    public $cryptoProvider;
    const CRYPTOPROVIDER_CRYPTOPRO = 'CRYPTO_PRO';
    const CRYPTOPROVIDER_CRYPTOPRO_2012 = 'CRYPTO_PRO_2012'; //По умолчанию
    const CRYPTOPROVIDER_JACARTA = 'JA_CARTA';
    const CRYPTOPROVIDER_RUTOKEN_GOST = 'RUTOKEN_GOST';
    const CRYPTOPROVIDER_VIPNET = 'VIPNET';
    const CRYPTOPROVIDER_VIPNET_2012 = 'VIPNET_2012';
    const CRYPTOPROVIDER_ESMART = 'ESMART';
    const CRYPTOPROVIDER_ESMART_2012 = 'ESMART_2012';
    const CRYPTOPROVIDER_JACARTA_2012 = 'JA_CARTA_2012';
    const CRYPTOPROVIDER_RUTOKEN_GOST_2012 = 'RUTOKEN_GOST_2012';
    const CRYPTOPROVIDER_CRYPTOPRO_CLOUD_CSP = 'CRYPTO_PRO_CLOUD_CSP';

    /**
     * ID пользователя в CRM, который будет назначен владельцем. По умолчанию - владелец сделки
     *
     * @var int
     */
    public $ownerId;

    /**
     * Псевдоним заявки
     *
     * @var string
     */
    public $name;

    /**
     * 1 - есть встроенная лицензия, 0 (по умолчанию) - нет
     *
     * @var bool
     */
    public $embededCP;

    /**
     * 1 - выпуск на иностранного гражданина, 0 (по умолчанию) - нет
     *
     * @var bool
     */
    public $isForeigner;

    /**
     * Выпуск на обособленное подразделение или филиал
     *
     *
     * @var bool
     */
    public $needKPP;

    /**
     * ИНН клиента
     *
     *
     * @var string
     */
    public $inn;

    /**
     * Срок действия в месяцах
     *
     *
     * @var int
     */
    public $period;
    const PERIOD_1_YEAR = 12;
    const PERIOD_1_YEAR_3_MONTHS = 15;

    /**
     * Телефон получателя сертификата
     *
     * @var string
     */
    public $phone;

    /**
     * @var SendCustomerFormData|null
     */
    public $formData;

    /**
     * ОГРН организации
     *
     * @var string
     */
    public $ogrn;

    /**
     * На кого выпускается подпись
     *
     * @see CUSTOMER_TARGET_OWNER
     * @var string
     */
    public $customerTarget;
    const CUSTOMER_TARGET_OWNER = 'owner';               //Руководитель организации (ИП) (по умолчанию)
    const CUSTOMER_TARGET_CONFIDANT = 'confidant';       //Доверенное лицо организации (ИП)

    /**
     * Кто получает подпись
     *
     * @see CUSTOMER_RECEIVER_RECEIVER
     * @var string
     */
    public $customerReceiver;
    const CUSTOMER_RECEIVER_RECEIVER = 'personally'; // лично
    const CUSTOMER_RECEIVER_CONFIDANT = 'confidant'; // доверенное лицо

    /**
     * Дополнительный продукт.
     *
     * @var SendProductTemplate[] $additionalProducts
     */
    public $additionalProducts;

    /**
     * Требуется ли сверка документов (трансфер)
     *
     * @var bool
     */
    public $transferNotRequired;
    /**
     * id склада / точки сверки
     *
     * @var int
     */
    public $transferStoreId;
    /**
     * Комментарии по заявке в свободной форме
     *
     * @var string
     */
    public $description;

    /**
     * Выпуск на бюджетную организацию
     *
     * @var bool
     */
    public $budgetOrganization;
}