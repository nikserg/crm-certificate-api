<?php
/**
 * Created by PhpStorm.
 * User: n.zarubin
 * Date: 12.03.2020
 * Time: 11:05
 */

namespace nikserg\CRMCertificateAPI\models\request;

/**
 * Заявка на сертификат в CRM
 * Модель для передачи в CRM
 *
 * @package nikserg\CRMCertificateAPI\models\request
 */
class SendCustomerForm
{
    //
    // Возможные значения customerLegalForm
    //
    const CUSTOMER_LEGAL_FORM_LEGAL = 'legal';           //Юр. лицо
    const CUSTOMER_LEGAL_FORM_INDIVIDUAL = 'individual'; //ИП
    const CUSTOMER_LEGAL_FORM_PERSON = 'person';         //Физ. лицо


    //
    // Криптопровайдеры
    //
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

    /**
     * Организационно-правовая форма
     *
     * @see self::CUSTOMER_LEGAL_FORM_LEGAL
     * @var string
     */
    public $customerLegalForm;

    /**
     * Если передан, изменяется запись с указанным ID, иначе создается новая заявка
     *
     * @var int
     */
    public $id;

    /**
     * Криптопровайдер
     *
     * @see self::CRYPTOPROVIDER_CRYPTOPRO_2012
     * @var string
     */
    public $cryptoProvider;

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
    public $embededCP = false;

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

    /**
     * Телефон получателя сертификата
     *
     * @var int
     */
    public $phone;
}