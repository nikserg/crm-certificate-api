<?php

namespace nikserg\CRMCertificateAPI;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use nikserg\CRMCertificateAPI\exceptions\BooleanResponseException;
use nikserg\CRMCertificateAPI\exceptions\InvalidRequestException;
use nikserg\CRMCertificateAPI\exceptions\NotFoundException;
use nikserg\CRMCertificateAPI\exceptions\ServerException;
use nikserg\CRMCertificateAPI\exceptions\TransportException;
use nikserg\CRMCertificateAPI\models\request\ChangeStatus;
use nikserg\CRMCertificateAPI\models\request\CheckPassport;
use nikserg\CRMCertificateAPI\models\request\CustomerFormDocuments;
use nikserg\CRMCertificateAPI\models\request\DetectPlatforms as DetectPlatformsRequest;
use nikserg\CRMCertificateAPI\models\request\PartnerPlatforms as PartnerPlatformsRequest;
use nikserg\CRMCertificateAPI\models\request\PartnerProducts as PartnerProductsRequest;
use nikserg\CRMCertificateAPI\models\request\SendCheckRef;
use nikserg\CRMCertificateAPI\models\request\SendCustomerForm as SendCustomerFormRequest;
use nikserg\CRMCertificateAPI\models\request\SendCustomerFormData;
use nikserg\CRMCertificateAPI\models\request\SendPrice;
use nikserg\CRMCertificateAPI\models\response\BooleanResponse;
use nikserg\CRMCertificateAPI\models\response\Esia\GetEgrul;
use nikserg\CRMCertificateAPI\models\response\GetCustomerForm;
use nikserg\CRMCertificateAPI\models\response\GetOpportunity;
use nikserg\CRMCertificateAPI\models\response\GetPassportCheck;
use nikserg\CRMCertificateAPI\models\response\GetPrice;
use nikserg\CRMCertificateAPI\models\response\GetSnilsCheck;
use nikserg\CRMCertificateAPI\models\response\models\DetectPlatformVariantPlatform;
use nikserg\CRMCertificateAPI\models\response\models\PartnerPlatform;
use nikserg\CRMCertificateAPI\models\response\models\PartnerProduct;
use nikserg\CRMCertificateAPI\models\response\models\Platforms;
use nikserg\CRMCertificateAPI\models\response\models\ProductTemplates;
use nikserg\CRMCertificateAPI\models\response\PartnerPlatforms as PartnerPlatformsResponse;
use nikserg\CRMCertificateAPI\models\response\PartnerProducts as PartnerProductsResponse;
use nikserg\CRMCertificateAPI\models\response\DetectPlatformVariant;
use nikserg\CRMCertificateAPI\models\response\ReferralUser;
use nikserg\CRMCertificateAPI\models\response\SendCustomerForm as SendCustomerFormResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Клиент для связи с CRM
 *
 * @package nikserg\CRMCertificateAPI
 */
class Client
{
    public const PRODUCTION_URL = 'https://crm.uc-itcom.ru/index.php/'; //Боевая

    #region Действия API
    private const ACTION_ADD_CUSTOMER_FORM = 'gateway/itkExchange/pushCustomerForm';
    private const ACTION_GET_CUSTOMER_FORM = 'gateway/itkExchange/pullCustomerForm';
    private const ACTION_DELETE_CUSTOMER_FORM = 'gateway/itkExchange/deleteCustomerForm';
    private const ACTION_GET_OPPORTUNITY = 'gateway/itkExchange/pullOpportunity';
    private const ACTION_UNION = 'gateway/itkExchange/union';
    private const ACTION_CERTIFICATE_BLANK = 'gateway/itkExchange/certificateBlank';
    private const ACTION_CHANGE_STATUS = 'gateway/itkExchange/pushCustomerFormStatus';
    private const ACTION_EGRUL = 'gateway/itkExchange/egrul';
    private const ACTION_PUSH_CUSTOMER_FORM_DATA = 'gateway/itkExchange/pushCustomerFormData';
    private const ACTION_PASSPORT_CHECK = 'gateway/itkExchange/checkPassport';
    private const ACTION_CHECK_SNILS = 'gateway/itkExchange/checkSnils';
    private const ACTION_GET_REFERRAL_USER = 'gateway/itkExchange/getRefUserInfo';
    private const ACTION_GET_PRICE = 'gateway/itkExchange/getPrice';
    private const ACTION_GET_PARTNER_PLATFORMS = 'gateway/itkExchange/getPlatformsInfo';
    private const ACTION_GET_PARTNER_PRODUCTS = 'gateway/itkExchange/infoProducts';
    private const ACTION_DETECT_PLATFORMS = 'gateway/itkExchange/detectPlatforms';
    private const ACTION_PUSH_CUSTOMER_FORM_DOCUMENTS = 'gateway/itkExchange/pushCustomerFormDocuments';
    #endregion Действия API

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $guzzle;

    /**
     * Client constructor.
     *
     * @param string $apiKey
     * @param string $url
     */
    public function __construct($apiKey, $url = self::PRODUCTION_URL)
    {
        $this->apiKey = $apiKey;
        $this->url = $url;
        $this->guzzle = new \GuzzleHttp\Client([
            RequestOptions::VERIFY      => false,
            RequestOptions::HTTP_ERRORS => false,
        ]);
    }

    /**
     * @param       $method
     * @param       $endpoint
     * @param array $options
     * @return ResponseInterface
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     * @throws InvalidRequestException
     */
    protected function request($method, $endpoint, $options = [])
    {
        $options[RequestOptions::QUERY]['key'] = $this->apiKey;
        try {
            $response = $this->guzzle->request($method, $this->url . $endpoint, $options);
        } catch (GuzzleException $e) {
            throw new TransportException("Ошибка запроса; {$e->getMessage()}");
        }
        switch ($response->getStatusCode()) {
            case 200:
            case 204:
                return $response;
            case 400:
                throw new InvalidRequestException("Неверный формат запроса");
            case 404:
                throw new NotFoundException("Сущность или точка АПИ не найдены");
            case 500:
                throw new ServerException("Ошибка сервера: ".$response->getBody()->getContents());
            default:
                throw new TransportException("Неожиданный код ответа {$response->getStatusCode()}");
        }
    }

    /**
     * @param       $method
     * @param       $endpoint
     * @param       $data
     * @param array $options
     * @return mixed
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     * @throws InvalidRequestException
     */
    protected function requestJson($method, $endpoint, $data, $options = [])
    {
        $options[RequestOptions::QUERY]['key'] = $this->apiKey;
        $options[RequestOptions::JSON] = $data;
        try {
            $response = $this->guzzle->request($method, $this->url . $endpoint, $options);
        } catch (GuzzleException $e) {
            throw new TransportException("Ошибка запроса; {$e->getMessage()}");
        }
        try {
            return $this->parseJsonResponse($response);
        } catch (TransportException $e) {
            throw new TransportException('Ошибка во время отправки запроса '.print_r([
                $method,
                    $this->url.$endpoint,
                    $options,
                    $data
                ], true).': '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param ResponseInterface $response
     * @return mixed
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    protected function parseJsonResponse (ResponseInterface $response) {
        $data = $this->getJsonBody($response);
        switch ($response->getStatusCode()) {
            case 200:
                return $data;
            case 400:
                throw new InvalidRequestException($data->error->message ?? $data->message ?? "Неверный формат запроса");
            case 404:
                throw new NotFoundException($data->error->message ?? $data->message ?? "Сущность или точка АПИ не найдены");
            case 500:
                throw new ServerException($data->error->message ?? $data->message ?? "Неожиданная ошибка сервера");
            default:
                throw new TransportException($data->error->message ?? $data->message ?? "Неожиданный код ответа {$response->getStatusCode()}");
        }
    }

    /**
     * @param ResponseInterface $response
     * @return mixed
     * @throws TransportException
     */
    private function getJsonBody(ResponseInterface $response)
    {
        $body = $response->getBody();
        if (strlen($body) === 0) {
            throw new TransportException('Пустое тело ответа на JSON запрос. Код ответа '.$response->getStatusCode());
        }
        $json = @json_decode($body);
        $jsonErrorCode = json_last_error();
        $jsonErrorMessage = json_last_error_msg();
        if ($jsonErrorCode !== JSON_ERROR_NONE) {
            throw new TransportException("$jsonErrorMessage: " . print_r($body, true),
                $jsonErrorCode);
        }
        return $json;
    }

    /**
     * Отправить запрос на создание заявки на сертификат
     *
     * @param SendCustomerFormRequest $customerForm
     * @return SendCustomerFormResponse
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function sendCustomerForm(SendCustomerFormRequest $customerForm)
    {
        $result = $this->requestJson('POST', self::ACTION_ADD_CUSTOMER_FORM, $customerForm);
        $response = new SendCustomerFormResponse();
        $response->id = $result->id;
        $response->token = $result->token;
        $response->generationToken = $result->generationToken;
        return $response;
    }

    /**
     * Получить информацию о заявке на сертификат
     *
     * @param int $customerFormCrmId
     * @return GetCustomerForm
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getCustomerForm($customerFormCrmId)
    {
        $result = $this->getJsonBody($this->request('GET', self::ACTION_GET_CUSTOMER_FORM, [
            RequestOptions::QUERY => [
                'id' => $customerFormCrmId,
            ],
        ]));
        $response = new GetCustomerForm();
        $response->status = $result->status;
        $response->tokenCertificate = $result->token ?? '';
        $response->opportunityId = $result->opportunityId ?? '';
        $response->isPay = $result->isPay;
        return $response;
    }

    /**
     * Получить информацию о сделки
     *
     * @param $opportunityCrmId
     * @return GetOpportunity
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getOpportunity($opportunityCrmId)
    {
        $result = $this->getJsonBody($this->request('GET', self::ACTION_GET_OPPORTUNITY, [
            RequestOptions::QUERY => [
                'id' => $opportunityCrmId,
            ],
        ]));
        $response = new GetOpportunity();
        $response->isPay = $result->isPay ?? '';
        $response->accountId = $result->accountId ?? '';
        $response->paymentToken = $result->paymentToken ?? '';
        return $response;
    }

    /**
     * Изменить статус заявки
     *
     * @param ChangeStatus $changeStatus
     * @return BooleanResponse
     * @throws BooleanResponseException
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function changeStatus(ChangeStatus $changeStatus)
    {
        $result = $this->requestJson('POST', self::ACTION_CHANGE_STATUS, $changeStatus);
        $response = new BooleanResponse();
        $response->status = $result->status;
        $response->message = $result->message ?? null;
        if (!$response->status) {
            throw new BooleanResponseException('Ошибка при обновлении статуса в CRM ' . print_r($response, true));
        }
        return $response;
    }

    /**
     * Удалить заявку на сертификат
     *
     * @param int $customerFormCrmId
     * @return BooleanResponse
     * @throws BooleanResponseException
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function deleteCustomerForm($customerFormCrmId)
    {
        $result = $this->getJsonBody($this->request('GET', self::ACTION_DELETE_CUSTOMER_FORM, [
            RequestOptions::QUERY => [
                'id' => $customerFormCrmId,
            ],
        ]));
        $response = new BooleanResponse();
        $response->status = $result->status;
        $response->message = $result->message ?? null;
        if (!$response->status) {
            throw new BooleanResponseException('Ошибка при удалении заявки в CRM ' . print_r($response, true));
        }
        return $response;
    }

    /**
     * Получить заявление на выпуск сертификата
     *
     * @param int    $customerFormCrmId
     * @param string $format
     * @return string
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getCustomerFormClaim($customerFormCrmId, $format = 'pdf')
    {
        $result = $this->request('GET', self::ACTION_UNION, [
            RequestOptions::QUERY => [
                'id'     => $customerFormCrmId,
                'format' => $format,
            ],
        ]);
        return $result->getBody()->getContents();
    }

    /**
     * Получить заявление на выпуск сертификата
     *
     * @param int    $customerFormCrmId
     * @param string $format
     * @return string
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getCustomerFormCertificateBlank($customerFormCrmId, $format = 'pdf')
    {
        $result = $this->request('GET', self::ACTION_CERTIFICATE_BLANK, [
            RequestOptions::QUERY => [
                'id'     => $customerFormCrmId,
                'format' => $format,
            ],
        ]);
        return $result->getBody()->getContents();
    }

    /**
     * Проверка ЕГРЮЛ
     *
     * @param $customerFormCrmId
     * @return GetEgrul
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getEgrul($customerFormCrmId)
    {
        $result = $this->getJsonBody($this->request('GET', self::ACTION_EGRUL, [
            RequestOptions::QUERY => [
                'customerFormId' => $customerFormCrmId,
            ],
        ]));
        return new GetEgrul($result);
    }

    /**
     * Отправить данные бланка заявки на сертификат
     *
     * @param int                  $crmCustomerFormId
     * @param SendCustomerFormData $customerFormData
     * @return SendCustomerFormResponse
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function sendCustomerFormData($crmCustomerFormId, SendCustomerFormData $customerFormData)
    {
        $result = $this->requestJson('POST', self::ACTION_PUSH_CUSTOMER_FORM_DATA, [
            'id'       => $crmCustomerFormId,
            'formData' => $customerFormData,
        ]);
        $response = new SendCustomerFormResponse();
        $response->id = $result->id;
        $response->token = $result->token;
        $response->generationToken = $result->generationToken;
        return $response;
    }

    /**
     * Проверка паспортных данных
     *
     * @param $series
     * @param $number
     * @return GetPassportCheck
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getPassportCheck($series, $number)
    {
        $result = $this->getJsonBody($this->request('GET', self::ACTION_PASSPORT_CHECK, [
            RequestOptions::QUERY => [
                'series' => $series,
                'number' => $number,
            ],
        ]));
        $response = new GetPassportCheck();
        $response->comment = $result->comment;
        $response->status = $result->status;
        return $response;

    }

    /**
     * Расширенная проверка паспортных данных
     *
     * @param CheckPassport $request
     * @return GetPassportCheck
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function checkPassport(CheckPassport $request)
    {
        $result = $this->requestJson('GET', self::ACTION_PASSPORT_CHECK, $request);
        $response = new GetPassportCheck();
        $response->status = $result->status;
        $response->comment = $result->comment;
        return $response;
    }

    /**
     * Проверка СНИЛС данных
     *
     * @param $customerFormCrmId
     * @return GetSnilsCheck
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getSnilsCheck($customerFormCrmId)
    {
        $result = $this->getJsonBody($this->request('GET', self::ACTION_CHECK_SNILS, [
            RequestOptions::QUERY => [
                'customerFormId' => $customerFormCrmId,
            ],
        ]));
        $response = new GetSnilsCheck();
        $response->status = $result->status ?? '';
        $response->message = $result->message ?? '';
        $response->createRequestDate = $result->createRequestDate ?? '';
        $response->id = $result->id;
        return $response;
    }

    /**
     * Получение информации о реферальном пользователе
     *
     * @param SendCheckRef $sendCheckRef
     * @return ReferralUser|null - null если реферальный пользователь не найден
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getReferralUser(SendCheckRef $sendCheckRef)
    {
        $result = $this->requestJson('POST', self::ACTION_GET_REFERRAL_USER, $sendCheckRef);
        if ($result === null) {
            return null;
        }
        $response = new ReferralUser();
        $response->id = $result->id;
        $response->paymentMode = $result->paymentMode;
        $response->userName = $result->userName;
        $response->email = $result->email;
        $response->phone = $result->phone;
        $response->isOfd = $result->isOfd;
        $response->enablePlatformSelection = $result->enablePlatformSelection;
        return $response;
    }

    /**
     * Отдает цены по продуктам и платформам
     *
     * @param SendPrice $sendPrice
     * @return GetPrice
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getPrice(SendPrice $sendPrice)
    {
        $result = $this->requestJson('POST', self::ACTION_GET_PRICE, $sendPrice);
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
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getPartnerPlatforms(PartnerPlatformsRequest $request)
    {
        $result = $this->requestJson('POST', self::ACTION_GET_PARTNER_PLATFORMS, [
            'referalId'         => $request->partnerUserId,
            'legalForm'         => $request->clientLegalForm,
            'selectedPlatforms' => $request->selectedPlatforms,
        ]);
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
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getPartnerProducts(PartnerProductsRequest $request)
    {
        $result = $this->requestJson('POST', self::ACTION_GET_PARTNER_PRODUCTS, [
            'referalId' => $request->partnerUserId,
        ]);
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

    /**
     * Определить варианты площадок для переданного списка ОИДов
     *
     * @param DetectPlatformsRequest $request
     * @return DetectPlatformVariant[]
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function detectPlatforms(DetectPlatformsRequest $request)
    {
        $result = $this->requestJson('POST', self::ACTION_DETECT_PLATFORMS, [
            'userId'    => $request->partnerUserId,
            'legalForm' => $request->clientLegalForm,
            'period'    => $request->period,
            'oids'      => $request->oids,
        ]);
        if (empty($result->variants)) {
            return [];
        }
        $return = [];
        foreach ($result->variants as $variant) {
            $variantModel = new DetectPlatformVariant();
            $variantModel->platforms = [];
            foreach ($variant->platforms as $value => $name) {
                $platform = new DetectPlatformVariantPlatform();
                $platform->value = $value;
                $platform->name = $name;
                $variantModel->platforms[] = $platform;
            }
            $variantModel->price = $variant->price;
            $variantModel->excluded = $variant->excluded;
            $return[] = $variantModel;
        }
        return $return;
    }

    /**
     * @param CustomerFormDocuments $documents
     * @return bool
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function pushCustomerFormDocuments(CustomerFormDocuments $documents)
    {
        $multipart = [
            [
                'name'     => 'customerFormId',
                'contents' => $documents->customerFormId,
            ],
        ];
        if ($documents->signedClaim) {
            $multipart[] = [
                'name'     => 'signedClaim',
                'filename' => 'claim.pdf.sig',
                'contents' => $documents->signedClaim,
            ];
        }
        if ($documents->signedBlank) {
            $multipart[] = [
                'name'     => 'signedBlank',
                'filename' => 'blank.pdf.sig',
                'contents' => $documents->signedBlank,
            ];
        }
        $this->request('POST', self::ACTION_PUSH_CUSTOMER_FORM_DOCUMENTS, [
            RequestOptions::MULTIPART => $multipart,
        ]);
        return true;
    }

    #region urls
    /**
     * Ссылка для скачивания сертификата
     *
     *
     * @param $customerFormId
     * @param $token
     * @return string
     */
    public function certificateDownloadUrl($customerFormId, $token)
    {
        return $this->url . 'customerForms/external/downloadCertificate?token=' . $token . '&customerFormId=' . $customerFormId;
    }

    /**
     * Ссылка для скачивания реализации
     *
     *
     * @param $customerFormId
     * @param $token
     * @return string
     */
    public function realizationDownloadUrl($customerFormId, $token)
    {
        return $this->url . 'customerForms/external/downloadFirstRealization?token=' . $token . '&customerFormId=' . $customerFormId;
    }

    /**
     * Индивидуальная ссылка для редактирования
     *
     *
     * @param $token
     * @return string
     */
    public function editUrl($token)
    {
        return $this->url . 'customerForms/external?token=' . $token;
    }

    /**
     * Индивидуальная ссылка для генерации
     *
     *
     * @param      $token
     * @param      $generatonToken
     * @param bool $iframe Выводить отображение для фрейма
     * @return string
     */
    public function generationUrl($token, $generatonToken, $iframe = false)
    {
        $return = $this->url . 'customerForms/external/generate?token=' . $token . '&generationToken=' . $generatonToken;
        if ($iframe) {
            $return .= '&iframe=1';
        }
        return $return;
    }

    /**
     * Ссылка на фрейм
     *
     * @param $customerFormId
     * @param $token
     * @return string
     */
    public function customerFormFrameUrl($customerFormId, $token)
    {
        return $this->url . 'customerForms/external/step1?token=' . $token . '&customerFormId=' . $customerFormId;
    }

    /**
     * Ссылка для записи сертификата на носитель
     *
     * @param $customerFormId
     * @param $token
     * @return string
     */
    public function certificateWriteUrl($customerFormId, $token)
    {
        return $this->url . 'customerForms/external/writeCertificate?token=' . $token . '&customerFormId=' . $customerFormId;
    }
    #endregion urls
}
