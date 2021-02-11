<?php

namespace nikserg\CRMCertificateAPI\models\request;

/**
 * Модель для передачи сделки в CRM
 *
 * @package nikserg\CRMCertificateAPI\models\request
 */
class SendOpportunity
{

    const ORGANIZATION_ITK = 'itk';
    const ORGANIZATION_ITCOM = 'itcom';
    const ORGANIZATION_ITCOM_MARKET = 'itcom_market';
    const ORGANIZATION_ITCOM_CONSULT = 'itcom_consult';
    const ORGANIZATION_ITCOM_SECURITY = 'itcom_security';
    /**
     * E-mail ответственного менеджера
     *
     *
     * @var  string
     */
    public $responsibleManagerEmail;
    /**
     * GUID - идентификатор склада в 1С
     *
     * @var string
     */
    public $transfer;
    /**
     * Комментарии в свободной форме
     *
     * @var string
     */
    public $description;
    /**
     * Наименование клиента для сделки
     *
     * @var string
     */
    public $fullName;
    /**
     * E-mail клиента для сделки
     *
     * @var string
     */
    public $email;
    /**
     * Телефон клиента для сделки
     *
     * @var string
     */
    public $phone;
    /**
     * ОПФ клиента для сделки
     *
     * @var string
     */
    public $legalForm;
    /**
     * ИНН клиента для сделки. Уникальный ключ - пара ИНН+КПП
     *
     * @var string
     */
    public $inn;
    /**
     * ОРГНИП клиента для сделки. Если передан, клиент считается ИП
     *
     * @var string
     */
    public $ogrnip;
    /**
     * КПП клиента для сделки
     *
     * @var string
     */
    public $kpp;
    /**
     * Организация
     *
     * @var string
     */
    public $organization;
}