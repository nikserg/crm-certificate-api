<?php
/**
 * Created by PhpStorm.
 * User: rheola
 * Date: 28.05.20
 * Time: 17:26
 */

namespace nikserg\CRMCertificateAPI\models\response;


use nikserg\CRMCertificateAPI\models\response\models\ProductTemplates;

class GetPrice
{
    /**
     * @var ProductTemplates[] $productTemplates
     */
    public $productTemplates;

    /**
     * @var
     */
    public $platforms;

    /**
     * @var string[] $notFoundPlatforms
     */
    public $notFoundPlatforms;



}