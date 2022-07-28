<?php

namespace nikserg\CRMCertificateAPI;


use nikserg\CRMCertificateAPI\models\data\Status;
use nikserg\CRMCertificateAPI\models\PaymentModes;
use nikserg\CRMCertificateAPI\models\request\ChangeStatus;
use nikserg\CRMCertificateAPI\models\request\CheckPassport;
use nikserg\CRMCertificateAPI\models\request\CheckSnils;
use nikserg\CRMCertificateAPI\models\request\CustomerFormDocuments;
use nikserg\CRMCertificateAPI\models\request\DetectPlatforms as DetectPlatformsRequest;
use nikserg\CRMCertificateAPI\models\request\Egrul as EgrulRequest;
use nikserg\CRMCertificateAPI\models\request\PartnerFullPrice as PartnerFullPriceRequest;
use nikserg\CRMCertificateAPI\models\request\PartnerPlatforms as PartnerPlatformsRequest;
use nikserg\CRMCertificateAPI\models\request\PartnerProducts as PartnerProductsRequest;
use nikserg\CRMCertificateAPI\models\request\PartnerStores as PartnerStoresRequest;
use nikserg\CRMCertificateAPI\models\request\SendCheckRef;
use nikserg\CRMCertificateAPI\models\request\SendCrtFile;
use nikserg\CRMCertificateAPI\models\request\SendCustomerForm;
use nikserg\CRMCertificateAPI\models\request\SendCustomerForm as SendCustomerFormRequest;
use nikserg\CRMCertificateAPI\models\request\SendCustomerFormData;
use nikserg\CRMCertificateAPI\models\request\SendReqFile;
use nikserg\CRMCertificateAPI\models\response\BooleanResponse;
use nikserg\CRMCertificateAPI\models\response\DetectPlatformVariant;
use nikserg\CRMCertificateAPI\models\response\Esia\Egrul as EgrulResponse;
use nikserg\CRMCertificateAPI\models\response\GetCustomerForm;
use nikserg\CRMCertificateAPI\models\response\GetOpportunity;
use nikserg\CRMCertificateAPI\models\response\models\DetectPlatformVariantPlatform;
use nikserg\CRMCertificateAPI\models\response\models\PartnerPlatform;
use nikserg\CRMCertificateAPI\models\response\models\PartnerProduct;
use nikserg\CRMCertificateAPI\models\response\models\Store;
use nikserg\CRMCertificateAPI\models\response\PassportCheck;
use nikserg\CRMCertificateAPI\models\response\PushCustomerFormDocuments;
use nikserg\CRMCertificateAPI\models\response\ReferralUser;
use nikserg\CRMCertificateAPI\models\response\SendCustomerForm as SendCustomerFormResponse;
use nikserg\CRMCertificateAPI\models\response\SnilsCheck;
use nikserg\CRMCertificateAPI\models\Semantic;

/**
 * Тестовый клиент для связи с API CRM
 *
 * @package nikserg\CRMCertificateAPI
 */
class MockClient extends Client
{
    // "Правильные" паспорта
    public const PASSPORTCHECK_VALID_SERIES = '1111';
    public const PASSPORTCHECK_VALID_NUMBER = '111111';
    // "Неправильные" паспорта
    public const PASSPORTCHECK_INVALID_SERIES = '2222';
    public const PASSPORTCHECK_INVALID_NUMBER = '222222';

    // Данные для запроса ЕГРЮЛ
    public const EGRUL_IP_KULSH = 9000000 - 2; // Выписка для ИП Кулиш Янина Викторовна
    public const EGRUL_LEGAL_ITK = 9000000 - 1; // Пыписка для юридического лица ООО "ИТК"

    private static $data;

    private static function mockFilename(): string
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mock.runtime';
    }

    private static function getData()
    {
        if (!empty(self::$data)) {
            return self::$data;
        }
        if (file_exists(self::mockFilename())) {
            self::$data = unserialize(file_get_contents(self::mockFilename()));
        } else {
            self::$data = [
                'currentId'     => 1,
                'currentStatus' => [
                    1 => 0,
                ],
            ];
        }

        return self::$data;
    }

    private static function flushData($data)
    {
        self::$data = $data;
        file_put_contents(self::mockFilename(), serialize(self::$data));
    }

    public function egrul(EgrulRequest $request): EgrulResponse
    {
        if ($request->customerForm == self::EGRUL_IP_KULSH) {
            return new EgrulResponse(json_decode('{ "id": 695854, "status": 2, "comment": "ok","data": { "organizationShortName": "ИП КУЛИШ ЯНИНА ВИКТОРОВНА", "OGRNIP": "306232719200028", "INN": "232702943100", "headLastName": "Кулиш", "headFirstName": "Янина", "headMiddleName": "Викторовна", "ownerGender": 2 }, "customerFormId": "559196" }'));
        } elseif ($request->customerForm == self::EGRUL_LEGAL_ITK) {
            return new EgrulResponse(json_decode('{ "id": 689373, "status": 2, "comment": "ok", "data": { "organizationShortName": "ООО \"ИТК\"", "organizationFullName": "ОБЩЕСТВО С ОГРАНИЧЕННОЙ ОТВЕТСТВЕННОСТЬЮ \"ИНТЕРНЕТ ТЕХНОЛОГИИ И КОММУНИКАЦИИ\"", "OGRN": "1112310000220", "INN": "2310152134", "KPP": "230801001", "fiasAddress": "КРАЙ КРАСНОДАРСКИЙ, ГОРОД КРАСНОДАР, УЛИЦА ДАЛЬНЯЯ, ДОМ 39\/3, ПОМЕЩЕНИЕ 140", "rawParticipators": [ " ", " ", " " ], "rawRegion": "КРАЙ КРАСНОДАРСКИЙ", "rawCity": "КРАСНОДАР", "rawOffice": "ПОМЕЩЕНИЕ 140", "rawHouse": "ДОМ 39\/3", "rawStreet": "УЛИЦА ДАЛЬНЯЯ", "postcode": "350051", "region": "23 Краснодарский край", "city": "Краснодар", "street": "УЛИЦА ДАЛЬНЯЯ, ДОМ 39\/3, ПОМЕЩЕНИЕ 140", "headLastName": "Сорокин", "headFirstName": "Дмитрий", "headMiddleName": "Викторович", "headPosition": "Генеральный директор" }, "customerFormId": "557436" }'));
        }

        return new EgrulResponse(json_decode('{"id":1,"status":2,"comment":"ok","data":{"organizationShortName":"ООО \"ИТК\"","organizationFullName":"ООО \"ИТК\"","OGRN":"1112310000220","INN":"2310152134","KPP":"12345678","region":"KRD","city":"Krasnodar","street":"One way st.","fiasAddress":"Krasnodar, One way st. 1337","headFirstName":"Дмитрий","headMiddleName":"Викторович","headLastName":"Сорокин","headPosition":"Генеральный директор"}}'));
    }

    public function getCustomerFormCertificateBlank(int $customerFormCrmId, string $format = 'pdf'): string
    {
        return base64_encode(file_get_contents(__DIR__ . '/data/blank.pdf'));
    }

    public function getCustomerFormClaim(int $customerFormCrmId, string $format = 'pdf'): string
    {
        return base64_encode(file_get_contents(__DIR__ . '/data/claim.pdf'));
    }

    public function sendCustomerForm(SendCustomerFormRequest $customerForm): SendCustomerFormResponse
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

    public function getCustomerForm(int $customerFormCrmId): GetCustomerForm
    {
        $response = new GetCustomerForm();
        $data = self::getData();
        $response->status = $data['currentStatus'][$customerFormCrmId];
        $response->tokenCertificate = 'crmToken';
        $response->token = 'token';
        $response->opportunityId = 1;

        return $response;
    }

    public function getOpportunity(int $opportunityCrmId): GetOpportunity
    {
        $response = new GetOpportunity();
        $response->isPay = true;
        $response->accountId = 1;
        $response->paymentToken = 'paymentToken';

        return $response;
    }

    public function changeStatus(ChangeStatus $changeStatus): BooleanResponse
    {
        $data = self::getData();
        $data['currentStatus'][$changeStatus->id] = $changeStatus->status;
        self::flushData($data);
        $response = new BooleanResponse();
        $response->status = true;

        return $response;
    }

    public function deleteCustomerForm(int $customerFormCrmId): BooleanResponse
    {
        $response = new BooleanResponse();
        $response->status = true;

        return $response;
    }


    public function sendReqFile(SendReqFile $sendReqFile): BooleanResponse {

        $response = new BooleanResponse();
        $response->status = true;

        return $response;
    }
    public function sendCrtFile(SendCrtFile $sendCrtFile): BooleanResponse {

        $response = new BooleanResponse();
        $response->status = true;

        return $response;
    }

    public function sendCustomerFormData(
        int $crmCustomerFormId,
        SendCustomerFormData $customerFormData
    ): SendCustomerFormResponse {
        $response = new SendCustomerFormResponse();
        $response->id = $crmCustomerFormId;
        $response->token = 'crmToken';
        $response->generationToken = 'crmGenerateToken';

        return $response;
    }

    public function checkPassport(CheckPassport $request): PassportCheck
    {
        $response = new PassportCheck();
        $response->created = date("Y-m-d H:i:s");
        if ($request->number == self::PASSPORTCHECK_INVALID_NUMBER) {
            $response->comment = 'Паспорт не существует (тест)';
            $response->status = Semantic::NEGATIVE;
        } else {
            $response->comment = '';
            $response->status = Semantic::POSITIVE;
        }

        return $response;
    }

    public function checkPassportExtended(CheckPassport $request)
    {
        return $this->checkPassport($request);
    }

    public function checkSnils(CheckSnils $request): SnilsCheck
    {
        $response = new SnilsCheck();
        $response->id = 1;
        $response->status = Semantic::POSITIVE;
        $response->comment = '';
        $response->created = date("Y-m-d H:i:s");

        return $response;
    }

    public function getReferralUser(SendCheckRef $sendCheckRef): ReferralUser
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

    public function pushCustomerFormDocuments(CustomerFormDocuments $documents): PushCustomerFormDocuments
    {
        $return = new PushCustomerFormDocuments();
        $return->union = 100;
        $return->passportphoto = 100;
        $return->customerFormId = $documents->customerFormId;
        $return->signedBlank = 100;
        $return->signedClaim = 100;
        $return->foreignerpassport = 100;

        return $return;
    }

    public function detectPlatforms(DetectPlatformsRequest $request): array
    {
        $known = [
            "1.3.6.1.5.5.7.3.2",
            "1.3.6.1.5.5.7.3.4",
            "1.2.643.6.3.1.4.1",
            "1.2.643.6.3",
            "1.2.643.6.7",
            "1.2.643.6.3.1.1",
            "1.2.643.3.8.100.1.42",
            "1.2.643.100.113.1",
            "1.2.643.100.113.2",
            "1.2.643.2.2.34.6",
            "1.2.643.6.19.3",
            "1.2.643.6.3.1.4.3",
            "1.2.643.6.3.1.4.2",
            "1.2.643.6.3.1.3.1",
            "1.2.643.6.3.1.2.1",
        ];
        $unknown = array_diff($request->oids, $known);
        $variant = new DetectPlatformVariant();
        $platform1 = new DetectPlatformVariantPlatform();
        $platform1->value = 'AETP';
        $platform1->name = 'АЭТП';
        $platform2 = new DetectPlatformVariantPlatform();
        $platform2->value = 'PROLONGATION_BIDDING_COMPLECT';
        $platform2->name = 'Тариф «ЭП Торги-комплект»';
        $variant->platforms = [$platform1, $platform2];
        $variant->price = 5000;
        $variant->excluded = $unknown;

        return [$variant];
    }

    public function getPartnerPlatformsAll(PartnerPlatformsRequest $request): array
    {
        $json = /** @lang JSON */
            '{
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
                },
                {
                    "price": 1777,
                    "description": "АЭТП",
                    "name": "АЭТП",
                    "platform": "AETP"
                },
                {
                    "price": 1333,
                    "description": "Тариф «ЭП Торги-комплект»",
                    "name": "Тариф «ЭП Торги-комплект»",
                    "platform": "PROLONGATION_BIDDING_COMPLECT"
                }
            ]
        }';
        $result = json_decode($json);
        $response = [];
        foreach ($result->platforms as $platform) {
            $partnerPlatform = new PartnerPlatform;
            $partnerPlatform->name = $platform->name;
            $partnerPlatform->group = "Группа";
            $partnerPlatform->description = $platform->description;
            $partnerPlatform->platform = $platform->platform;
            $partnerPlatform->price = $platform->price;
            $response[] = $partnerPlatform;
        }

        return $response;
    }

    public function getPartnerProductsAll(PartnerProductsRequest $request): array
    {
        $json = /** @lang JSON */
            '{
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
        $response = [];
        foreach ($result->productInfo as $product) {
            $partnerPlatform = new PartnerProduct();
            $partnerPlatform->name = $product->name;
            $partnerPlatform->description = $product->description;
            $partnerPlatform->id = $product->id;
            $partnerPlatform->price = $product->price;
            $response[] = $partnerPlatform;
        }

        return $response;
    }

    public function getPartnerFullPrice(PartnerFullPriceRequest $fullPriceRequest): float
    {
        return 666;
    }

    public function getPartnerStores(PartnerStoresRequest $partnerStores): array
    {
        $json = /** @lang JSON */
            '{
              "stores": [
                {
                    "id": 3195,
                    "title": "ИП Глумова Татьяна Борисовна",
                    "address": "656002, Алтайский край, Барнаул г, Сизова ул, дом № 14Б",
                    "phone": "",
                    "lat": "53.280205",
                    "lng": "83.761495"
                },
                {
                    "id": 3225,
                    "title": "ООО БРАВО",
                    "address": "659319, Алтайский край, Бийск г, Петра Мерлина ул, дом № 58, кв 311",
                    "phone": "",
                    "lat": "52.530544",
                    "lng": "85.16064800000004"
                },
                {
                    "id": 3049,
                    "title": "ИП Лапина Ирина Анатольевна",
                    "address": "453431, Башкортостан респ, Благовещенский р-н, Благовещенск г, Седова ул, дом № 110",
                    "phone": "",
                    "lat": "55.049091",
                    "lng": "55.95632899999998"
                }
              ]
            }';
        $result = json_decode($json);

        return $this->fillList(Store::class, $result->stores);
    }

}
