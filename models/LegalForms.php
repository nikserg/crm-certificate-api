<?php

namespace nikserg\CRMCertificateAPI\models;


/**
 * Типы ОПФ используемые в апи
 *
 * @package nikserg\CRMCertificateAPI\models
 */
class LegalForms
{
    /**
     * Организационно-правовая форма Юр. лицо
     *
     * @var string
     */
    public const LEGAL = 'legal';

    /**
     * Организациона-правовая форма ИП
     *
     * @var string
     */
    public const INDIVIDUAL = 'individual';

    /**
     * Организационно-правовая форма Физического лица
     *
     * @var string
     */
    public const PERSON = 'person';
}