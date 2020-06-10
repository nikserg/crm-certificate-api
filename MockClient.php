<?php

namespace nikserg\CRMCertificateAPI;


use nikserg\CRMCertificateAPI\models\data\Status;
use nikserg\CRMCertificateAPI\models\request\ChangeStatus;
use nikserg\CRMCertificateAPI\models\request\SendCheckRef;
use nikserg\CRMCertificateAPI\models\request\SendCustomerForm as SendCustomerFormRequest;
use nikserg\CRMCertificateAPI\models\request\SendCustomerFormData;
use nikserg\CRMCertificateAPI\models\request\SendPrice;
use nikserg\CRMCertificateAPI\models\response\BooleanResponse;
use nikserg\CRMCertificateAPI\models\response\GetCheckRef;
use nikserg\CRMCertificateAPI\models\response\GetCustomerForm;
use nikserg\CRMCertificateAPI\models\response\GetOpportunity;
use nikserg\CRMCertificateAPI\models\response\GetPassportCheck;
use nikserg\CRMCertificateAPI\models\response\GetPrice;
use nikserg\CRMCertificateAPI\models\response\GetSnilsCheck;
use nikserg\CRMCertificateAPI\models\response\models\Platforms;
use nikserg\CRMCertificateAPI\models\response\models\ProductTemplates;
use nikserg\CRMCertificateAPI\models\response\SendCustomerForm as SendCustomerFormResponse;
use nikserg\CRMCertificateAPI\models\request\SendCustomerForm;
use nikserg\CRMCertificateAPI\models\response\Esia;
use nikserg\CRMCertificateAPI\models\response\Esia\GetEgrul;

/**
 * Class MockClient
 *
 * Тестовый клиент для связи с API CRM
 *
 * @package nikserg\CRMCertificateAPI
 */
class MockClient extends Client
{
    //
    // Данные для проверки паспорта
    //
    //Правильные
    public const PASSPORTCHECK_VALID_SERIES = '1111';
    public const PASSPORTCHECK_VALID_NUMBER = '111111';
    //Неправильные
    public const PASSPORTCHECK_INVALID_SERIES = '2222';
    public const PASSPORTCHECK_INVALID_NUMBER = '222222';

    public static $currentStatus = [];
    public static $currentId = 1;

    public function getEgrul($customerFormCrmId)
    {
        return new GetEgrul(json_encode([
            "id"       => 1,
            "status"   => Esia::STATUS_EXECUTED,
            "response" => [
                'organizationShortName' => 'ООО "ИТК"',
                'organizationFullName'  => 'ООО "ИТК"',
                'OGRN'                  => '1112310000220',
                'INN'                   => '2310152134',
                'KPP'                   => '12345678',
                'region'                => 'KRD',
                'city'                  => 'Krasnodar',
                'street'                => 'One way st.',
                'fiasAddress'           => 'Krasnodar, One way st. 1337',
                'headFirstName'         => 'Дмитрий',
                'headMiddleName'        => 'Викторович',
                'headLastName'          => 'Сорокин',
                'headPosition'          => 'Генеральный директор',
            ],
        ]));
    }

    public function getCustomerFormCertificateBlank($customerFormCrmId, $format = 'pdf')
    {
        return base64_encode(file_get_contents(__DIR__.'/data/blank.pdf'));
    }

    public function getCustomerFormClaim($customerFormCrmId, $format = 'pdf')
    {
        return base64_encode(file_get_contents(__DIR__.'/data/claim.pdf'));
    }

    public function sendCustomerForm(SendCustomerFormRequest $customerForm)
    {
        $response = new SendCustomerFormResponse();
        if ($customerForm->id) {
            $response->id = $customerForm->id;
        } else {
            $response->id = static::$currentId++;
        }
        self::$currentStatus[$response->id] = Status::INIT;
        $response->token = 'crmToken';
        $response->generationToken = 'crmGenerateToken';
        return $response;
    }

    public function getCustomerForm($customerFormCrmId)
    {
        $response = new GetCustomerForm();
        $response->status = self::$currentStatus[$customerFormCrmId];
        $response->tokenCertificate = 'crmToken';
        $response->opportunityId = 1;
        return $response;
    }

    public function getOpportunity($opportunityCrmId)
    {
        $response = new GetOpportunity();
        $response->isPay = true;
        $response->accountId = 1;
        $response->paymentToken = 'paymentToken';
        return $response;
    }

    public function changeStatus(ChangeStatus $changeStatus)
    {
        self::$currentStatus[$changeStatus->id] = $changeStatus->status;
        $response = new BooleanResponse();
        $response->status = true;
        return $response;
    }

    public function deleteCustomerForm($customerFormCrmId)
    {
        $response = new BooleanResponse();
        $response->status = true;
        return $response;
    }

    public function sendCustomerFormData($crmCustomerFormId, SendCustomerFormData $customerFormData)
    {
        $response = new SendCustomerFormResponse();
        $response->id = 1;
        $response->token = 'crmToken';
        $response->generationToken = 'crmGenerateToken';
        return $response;
    }

    public function getPassportCheck($series, $number)
    {
        $response = new GetPassportCheck();
        if ($number == self::PASSPORTCHECK_INVALID_NUMBER) {
            $response->comment = 'Паспорт не существует (тест)';
            $response->status = GetPassportCheck::STATUS_INVALID;
        } else {
            $response->comment = '';
            $response->status = GetPassportCheck::STATUS_VALID;
        }
        return $response;
    }

    public function getSnilsCheck($customerFormCrmId)
    {
        $response = new GetSnilsCheck();
        $response->status = GetSnilsCheck::STATUS_SUCCESS;
        $response->message = '';
        $response->id = 1;

        return $response;
    }

    public function getCheckRef(SendCheckRef $sendCheckRef)
    {
        $response = new GetCheckRef();
        $response->id = 1;
        $response->paymentMode = GetCheckRef::PAYMENT_UNLIMITED;
        $response->userName = 'username';

        return $response;
    }

    public function getPrice(SendPrice $sendPrice)
    {
        $result = json_decode('{
    "productTemplates": [
        {
            "id": 981,
            "price": "1500.00"
        },
        {
            "id": 511,
            "price": "1200.00"
        },
        {
            "id": 127,
            "price": "750"
        },
        {
            "id": 1044,
            "price": 1000
        }
    ],
    "platforms": [
        {
            "price": 2500,
            "name": "EPGU"
        },
        {
            "price": 17600,
            "name": "AETP_NEW_BASE,B2B,GPB,FABRIKANT"
        },
        {
            "price": 3500,
            "name": "AETP_NEW_BASE"
        },
        {
            "price": 6900,
            "name": "AETP_NEW_BASE,B2B"
        },
        {
            "price": 8200,
            "name": "AETP_NEW_BASE,FABRIKANT"
        },
        {
            "price": 9500,
            "name": "AETP_NEW_BASE,GPB"
        },
        {
            "price": 8300,
            "name": "AETP_NEW_BASE,RJD"
        },
        {
            "price": 6000,
            "name": "CDT_BUYER"
        },
        {
            "price": 33900,
            "name": "AETP_NEW_BASE,CENTREAL,CDT_BUYER,ATC,FREETRADE,URALBIDIN,ESP,TENDER_UG,ALFALOT"
        },
        {
            "price": 5000,
            "name": "EPGU,ROSREESTR_LEGAL"
        }
    ],
    "notFoundPlatforms": []
}');

        $response = new GetPrice();
        $response->productTemplates = [];
        $response->platforms = [];


        foreach ($result->productTemplates ?? [] as $productTemplateRequest) {
            $productTemplate = new ProductTemplates();
            $productTemplate->id = $productTemplateRequest->id ?? '';
            $productTemplate->price = $productTemplateRequest->price ?? '';
            $response->productTemplates[] = $productTemplate;
        }
        foreach ($result->platforms ?? [] as $platformRequest) {
            $platform = new Platforms();
            $platform->name = $platformRequest->name ?? '';
            $platform->price = $platformRequest->price ?? '';
            $response->platforms[] = $platform;
        }
        $response->notFoundPlatforms = $result->notFoundPlatforms ?? [];
        return $response;
    }

}