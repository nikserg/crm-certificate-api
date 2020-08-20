<?php


namespace nikserg\CRMCertificateAPI\models;

/**
 * Семантический статус ответа
 *
 * @package nikserg\CRMCertificateAPI\models
 */
class Semantic
{
    const UNDEFINED = 0;
    const NEGATIVE = 1;
    const POSITIVE = 2;

    public static $semanticNames = [
        self::UNDEFINED => 'Не определен',
        self::NEGATIVE  => 'Негативный',
        self::POSITIVE  => 'Позитивный',
    ];
}