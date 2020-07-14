<?php

namespace nikserg\CRMCertificateAPI;


use nikserg\CRMCertificateAPI\models\data\Status;
use nikserg\CRMCertificateAPI\models\PaymentModes;
use nikserg\CRMCertificateAPI\models\request\ChangeStatus;
use nikserg\CRMCertificateAPI\models\request\CustomerFormDocuments;
use nikserg\CRMCertificateAPI\models\request\PartnerPlatforms as PartnerPlatformsRequest;
use nikserg\CRMCertificateAPI\models\request\PartnerProducts as PartnerProductsRequest;
use nikserg\CRMCertificateAPI\models\request\SendCheckRef;
use nikserg\CRMCertificateAPI\models\request\SendCustomerForm as SendCustomerFormRequest;
use nikserg\CRMCertificateAPI\models\request\SendCustomerFormData;
use nikserg\CRMCertificateAPI\models\request\SendPrice;
use nikserg\CRMCertificateAPI\models\response\BooleanResponse;
use nikserg\CRMCertificateAPI\models\response\GetCustomerForm;
use nikserg\CRMCertificateAPI\models\response\GetOpportunity;
use nikserg\CRMCertificateAPI\models\response\GetPassportCheck;
use nikserg\CRMCertificateAPI\models\response\GetPrice;
use nikserg\CRMCertificateAPI\models\response\GetSnilsCheck;
use nikserg\CRMCertificateAPI\models\response\models\PartnerPlatform;
use nikserg\CRMCertificateAPI\models\response\models\PartnerProduct;
use nikserg\CRMCertificateAPI\models\response\models\Platforms;
use nikserg\CRMCertificateAPI\models\response\models\ProductTemplates;
use nikserg\CRMCertificateAPI\models\response\PartnerPlatforms as PartnerPlatformsResponse;
use nikserg\CRMCertificateAPI\models\response\PartnerProducts as PartnerProductsResponse;
use nikserg\CRMCertificateAPI\models\response\ReferralUser;
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

    //
    // Данные для запроса ЕГРЮЛ
    //
    public const EGRUL_IP_KULSH = PHP_INT_MAX-2; //Выписка для ИП Кулиш Янина Викторовна
    public const EGRUL_LEGAL_ITK = PHP_INT_MAX-1; //Пыписка для юридического лица ООО "ИТК"

    private static $data;

    private static function getData()
    {
        if (!empty(self::$data)) {
            return self::$data;
        }
        if (file_exists(__DIR__.'/mock.runtime')) {
            self::$data = unserialize(file_get_contents(__DIR__.'/mock.runtime'));
        } else {
            self::$data = [
                'currentId' => 1,
                'currentStatus' => [
                    1 => 0
                ]
            ];
        }
        return self::$data;
    }
    private static function flushData($data)
    {
        self::$data = $data;
        file_put_contents(__DIR__.'/mock.runtime', serialize(self::$data));
    }

    public function getEgrul($customerFormCrmId)
    {
        if ($customerFormCrmId == self::EGRUL_IP_KULSH) {
            return new GetEgrul('{ "id": 695854, "status": "4", "response": { "organizationShortName": "ИП КУЛИШ ЯНИНА ВИКТОРОВНА", "OGRNIP": "306232719200028", "INN": "232702943100", "headLastName": "Кулиш", "headFirstName": "Янина", "headMiddleName": "Викторовна", "ownerGender": 2 }, "customerFormId": "559196" }');
        } elseif ($customerFormCrmId == self::EGRUL_LEGAL_ITK) {
            return new GetEgrul('{ "id": 689373, "status": "4", "response": { "organizationShortName": "ООО \"ИТК\"", "organizationFullName": "ОБЩЕСТВО С ОГРАНИЧЕННОЙ ОТВЕТСТВЕННОСТЬЮ \"ИНТЕРНЕТ ТЕХНОЛОГИИ И КОММУНИКАЦИИ\"", "OGRN": "1112310000220", "INN": "2310152134", "KPP": "230801001", "fiasAddress": "КРАЙ КРАСНОДАРСКИЙ, ГОРОД КРАСНОДАР, УЛИЦА ДАЛЬНЯЯ, ДОМ 39\/3, ПОМЕЩЕНИЕ 140", "rawParticipators": [ " ", " ", " " ], "rawRegion": "КРАЙ КРАСНОДАРСКИЙ", "rawCity": "КРАСНОДАР", "rawOffice": "ПОМЕЩЕНИЕ 140", "rawHouse": "ДОМ 39\/3", "rawStreet": "УЛИЦА ДАЛЬНЯЯ", "postcode": "350051", "region": "23 Краснодарский край", "city": "Краснодар", "street": "УЛИЦА ДАЛЬНЯЯ, ДОМ 39\/3, ПОМЕЩЕНИЕ 140", "headLastName": "Сорокин", "headFirstName": "Дмитрий", "headMiddleName": "Викторович", "headPosition": "Генеральный директор" }, "customerFormId": "557436" }');
        }
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
            $data = self::getData();
            $response->id = $data['currentId'];
            $data['currentStatus'][$response->id] = Status::INIT;
            $data['currentId'] = $response->id + 1;
            self::flushData($data);
        }
        $response->token = 'crmToken';
        $response->generationToken = 'crmGenerateToken';
        return $response;
    }

    public function getCustomerForm($customerFormCrmId)
    {
        $response = new GetCustomerForm();
        $data = self::getData();
        $response->status = $data['currentStatus'][$customerFormCrmId];
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
        $data = self::getData();
        $data['currentStatus'][$changeStatus->id] = $changeStatus->status;
        self::flushData($data);
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
        $response->id = $crmCustomerFormId;
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

    public function getReferralUser(SendCheckRef $sendCheckRef)
    {
        $response = new ReferralUser();
        $response->id = 1;
        $response->paymentMode = PaymentModes::PAYMENT_UNLIMITED;
        $response->userName = 'username';
        $response->email = 'user@na.me';
        $response->phone = 'hi :)';
        $response->isOfd = true;
        $response->enablePlatformSelection = true;
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
            "name": "PROLONGATION_BISNES_RJD"
        },
        {
            "price": 6000,
            "name": "PROLONGATION_BIDDING_CDT"
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

    /**
     * Получает платформы, доступные партнеру переданному в запросе
     *
     * @param PartnerPlatformsRequest $request
     * @return PartnerPlatformsResponse
     * @throws \Exception
     */
    public function getPartnerPlatforms(PartnerPlatformsRequest $request)
    {
        $json = /** @lang JSON */ '{
            "platforms": [
                {
                    "price": 2500,
                    "description": "Описание EPGU",
                    "name": "Имя EPGU",
                    "platform": "EPGU"
                },
                {
                    "price": 17600,
                    "description": "Описание FABRIKANT",
                    "name": "Имя FABRIKANT",
                    "platform": "FABRIKANT"
                },
                {
                    "price": 3500,
                    "description": "Описание AETP_NEW_BASE",
                    "name": "Имя AETP_NEW_BASE",
                    "platform": "AETP_NEW_BASE"
                },
                {
                    "price": 6900,
                    "description": "Имя Описание B2B",
                    "name": "Имя B2B",
                    "platform": "B2B"
                }
            ]
        }';
        $result = json_decode($json);
        $response = new PartnerPlatformsResponse();
        $response->availablePlatforms = [];
        foreach ($result->platforms as $platform) {
            $partnerPlatform = new PartnerPlatform;
            $partnerPlatform->name = $platform->name;
            $partnerPlatform->description = $platform->description;
            $partnerPlatform->platform = $platform->platform;
            $partnerPlatform->price = $platform->price;
            $response->availablePlatforms[] = $partnerPlatform;
        }
        return $response;
    }


    /**
     * Получает продукты, настроенные для партнера переданного в запросе
     *
     * @param PartnerProductsRequest $request
     * @return PartnerProductsResponse
     * @throws \Exception
     */
    public function getPartnerProducts(PartnerProductsRequest $request)
    {
        $json = /** @lang JSON */ '{
            "productInfo": [
                {
                    "id": 981,
                    "price": 1500,
                    "name": "Сертифицированный защищенный носитель (Рутокен)",
                    "description": "Рутокен — специальное сертифицированное защищённое USB-устройство, внешне похожее на флешку. Предназначено для хранения и использования электронной подписи (КЭП)."
                },
                {
                    "id": 511,
                    "price": 700,
                    "name": "Лицензия на право использования СКЗИ КриптоПро CSP в составе сертификата ключа",
                    "description": "КриптоПро - специальная программа криптозащиты.\\nОна используется для генерации ключа электронной подписи и работы с сертификатами. Без действующей лицензии СКЗИ КриптоПро CSP электронная подпись на вашем компьютере не сможет работать."
                },
                {
                    "id": 127,
                    "price": 1000,
                    "name": "Установка СКЗИ КриптоПро CSP и КЭП",
                    "description": "Процесс установки СКЗИ «КриптоПро» и настройки рабочего места для корректной работы электронной подписи — весьма трудоёмкий и требует специальных знаний. Чтобы облегчить и ускорить этот процесс, закажите его у специалистов технической поддержки."
                }
            ]
        }';
        $result = json_decode($json);
        $response = new PartnerProductsResponse();
        $response->availableProducts = [];
        if (empty($result)) {
            $response->hasSettings = false;
        } else {
            $response->hasSettings = true;
            foreach ($result->productInfo as $product) {
                $partnerPlatform = new PartnerProduct();
                $partnerPlatform->name = $product->name;
                $partnerPlatform->description = $product->description;
                $partnerPlatform->id = $product->id;
                $partnerPlatform->price = $product->price;
                $response->availableProducts[] = $partnerPlatform;
            }
        }
        return $response;
    }

    public function pushCustomerFormDocuments(CustomerFormDocuments $documents)
    {
        return true;
    }
}