<?php

namespace nikserg\CRMCertificateAPI\models\response;

use nikserg\CRMCertificateAPI\models\response\models\DetectPlatformVariantPlatform;

/**
 * Вариант определения типа платформы
 *
 */
class DetectPlatformVariant
{
    /**
     * Площадки
     *
     *
     * @var DetectPlatformVariantPlatform[]
     */
    public $platforms;


    /**
     * Стоимость варианта
     *
     *
     * @var float
     */
    public $price;


    /**
     * Список ОИДов, не вошедших в вариант
     *
     *
     * @var string[]
     */
    public $excluded;

    /**
     * Список человекопонятных названий всех платформ
     *
     *
     * @return string[]
     */
    public function getPlatformNames()
    {
        $return = [];
        foreach ($this->platforms as $platform) {
            $return[] = $platform->name;
        }
        return $return;
    }

    /**
     * Список машинных названий всех платформ
     *
     *
     * @return string[]
     */
    public function getPlatformValues()
    {
        $return = [];
        foreach ($this->platforms as $platform) {
            $return[] = $platform->value;
        }
        return $return;
    }

    /**
     * Список площадок в строку
     *
     *
     * @return string
     */
    public function getImplodedPlatforms()
    {
        return implode(',', $this->getPlatformValues());
    }
}