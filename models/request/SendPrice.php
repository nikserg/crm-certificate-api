<?php

namespace nikserg\CRMCertificateAPI\models\request;

/**
 * Class SendPrice
 *
 * @deprecated
 * @package nikserg\CRMCertificateAPI\models\request
 */
class SendPrice
{
    /**
     * Организационно-правовая форма Юр. лицо
     *
     * @var string
     */
    public const CUSTOMER_LEGAL_FORM_LEGAL = 'legal';

    /**
     * Организациона-правовая форма ИП
     *
     * @var string
     */
    public const CUSTOMER_LEGAL_FORM_INDIVIDUAL = 'individual';

    /**
     * Организационно-правовая форма Физического лица
     *
     * @var string
     */
    public const CUSTOMER_LEGAL_FORM_PERSON = 'person';

    /**
     * Номер пользователя
     *
     * @var int
     */
    public $userId;

    /**
     * Организационно-правовая форма. Необязательное поле. По умолчанию берется legal
     *
     * @var string
     */
    public $legalForm;

    /**
     * Период действие сертфиката необязательное поле по умолчанию берется 1 год
     *
     * @var string $period
     */
    public $period;

    /**
     * Платформы по которым нужно проверить цены
     *
     * @var string[]
     */
    public $platforms;

    /**
     * Номера продуктов предмета коталог
     *
     * @var int[]
     */
    public $products;
}