<?php

namespace nikserg\CRMCertificateAPI;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use nikserg\CRMCertificateAPI\exceptions\BooleanResponseException;
use nikserg\CRMCertificateAPI\exceptions\NotFoundException;
use nikserg\CRMCertificateAPI\models\request\ChangeStatus;
use nikserg\CRMCertificateAPI\models\request\PartnerPlatformsRequest;
use nikserg\CRMCertificateAPI\models\request\PartnerProductsRequest;
use nikserg\CRMCertificateAPI\models\request\SendCheckRef;
use nikserg\CRMCertificateAPI\models\request\SendCustomerForm as SendCustomerFormRequest;
use nikserg\CRMCertificateAPI\models\request\SendCustomerFormData;
use nikserg\CRMCertificateAPI\models\request\SendPrice;
use nikserg\CRMCertificateAPI\models\response\BooleanResponse;
use nikserg\CRMCertificateAPI\models\response\Esia\GetEgrul;
use nikserg\CRMCertificateAPI\models\response\GetCheckRef;
use nikserg\CRMCertificateAPI\models\response\GetCustomerForm;
use nikserg\CRMCertificateAPI\models\response\GetOpportunity;
use nikserg\CRMCertificateAPI\models\response\GetPassportCheck;
use nikserg\CRMCertificateAPI\models\response\GetPrice;
use nikserg\CRMCertificateAPI\models\response\GetSnilsCheck;
use nikserg\CRMCertificateAPI\models\response\models\PartnerPlatform;
use nikserg\CRMCertificateAPI\models\response\models\PartnerProduct;
use nikserg\CRMCertificateAPI\models\response\models\Platforms;
use nikserg\CRMCertificateAPI\models\response\models\ProductTemplates;
use nikserg\CRMCertificateAPI\models\response\PartnerPlatforms;
use nikserg\CRMCertificateAPI\models\response\PartnerProducts;
use nikserg\CRMCertificateAPI\models\response\SendCustomerForm as SendCustomerFormResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Client
 *
 * Клиент для связи с CRM
 *
 * @package nikserg\CRMCertificateAPI
 */
class Client
{
    public const PRODUCTION_URL = 'https://crm.uc-itcom.ru/index.php/';
    public const TEST_URL = 'https://dev.uc-itcom.ru/index.php/';
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
    private const ACTION_CHECK_REFERRAL = 'gateway/itkExchange/checkRef';
    private const ACTION_GET_PRICE = 'gateway/itkExchange/getPrice';
    private const ACTION_GET_PARTNER_PLATFORMS = 'gateway/itkExchange/getPlatformsInfo';
    private const ACTION_GET_PARTNER_PRODUCTS = 'gateway/itkExchange/infoProducts';
    protected $apiKey;
    protected $url;
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
            'verify' => false,
        ]);
    }

    /**
     * @param ResponseInterface $response
     * @return mixed
     * @throws \Exception
     */
    private function getJsonBody(ResponseInterface $response)
    {
        $body = $response->getBody();
        if (strlen($body)===0) {
            throw new \Exception('Получено пустое тело ответа на запрос');
        }
        $json = @json_decode($body);
        $jsonErrorCode = json_last_error();
        $jsonErrorMessage = json_last_error_msg();
        if ($jsonErrorCode !== JSON_ERROR_NONE) {
            throw new \Exception("Невозможно распарсить ответ ($jsonErrorMessage): " . print_r($body, true),$jsonErrorCode);
        }
        return $json;
    }

    /**
     * Отправить запрос на создание заявки на сертификат
     *
     *
     * @param SendCustomerFormRequest $customerForm
     * @return SendCustomerFormResponse
     */
    public function sendCustomerForm(SendCustomerFormRequest $customerForm)
    {
        $url = $this->url . self::ACTION_ADD_CUSTOMER_FORM . '?key=' . $this->apiKey;
        $result = $this->guzzle->post($url, [RequestOptions::JSON => $customerForm]);
        $result = $this->getJsonBody($result);
        $response = new SendCustomerFormResponse();
        $response->id = $result->id;
        $response->token = $result->token;
        $response->generationToken = $result->generationToken;
        return $response;
    }

    /**
     * Получить информацию о заявке на сертификат
     *
     *
     * @param int $customerFormCrmId
     * @return GetCustomerForm
     */
    public function getCustomerForm($customerFormCrmId)
    {
        $url = $this->url . self::ACTION_GET_CUSTOMER_FORM . '?key=' . $this->apiKey;
        $result = $this->guzzle->get($url . '&id=' . $customerFormCrmId);
        $result = $this->getJsonBody($result);
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
     * @throws \Exception
     */
    public function getOpportunity($opportunityCrmId)
    {
        $url = $this->url . self::ACTION_GET_OPPORTUNITY . '?key=' . $this->apiKey;
        $result = $this->guzzle->get($url . '&id=' . $opportunityCrmId);
        $result = $this->getJsonBody($result);
        $response = new GetOpportunity();
        $response->isPay = $result->isPay ?? '';
        $response->accountId = $result->accountId ?? '';
        $response->paymentToken = $result->paymentToken ?? '';
        return $response;
    }

    /**
     * Изменить статус заявки
     *
     *
     * @param ChangeStatus $changeStatus
     * @return BooleanResponse
     * @throws BooleanResponseException
     * @throws GuzzleException
     * @throws NotFoundException
     */
    public function changeStatus(ChangeStatus $changeStatus)
    {
        $url = $this->url . self::ACTION_CHANGE_STATUS . '?key=' . $this->apiKey;
        try {
            $result = $this->guzzle->post($url, [RequestOptions::JSON => $changeStatus]);
        } catch (GuzzleException $e) {
            if ($e->getCode() == 404) {
                throw new NotFoundException('В CRM не найдена заявка #' . $changeStatus->id);
            }
            throw $e;
        }
        $result = $this->getJsonBody($result);
        $response = new BooleanResponse();
        $response->status = $result->status;
        if (property_exists($result, 'message')) {
            $response->message = $result->message;
        }
        if (!$response->status) {
            throw new BooleanResponseException('Ошибка при обновлении статуса в CRM ' . print_r($response, true));
        }
        return $response;
    }

    /**
     * Удалить заявку на сертификат
     *
     *
     * @param int $customerFormCrmId
     * @return BooleanResponse
     * @throws BooleanResponseException
     * @throws NotFoundException
     * @throws GuzzleException
     */
    public function deleteCustomerForm($customerFormCrmId)
    {
        $url = $this->url . self::ACTION_DELETE_CUSTOMER_FORM . '?key=' . $this->apiKey;
        try {
            $result = $this->guzzle->get($url . '&id=' . $customerFormCrmId);
        } catch (GuzzleException $e) {
            if ($e->getCode() == 404) {
                throw new NotFoundException('В CRM не найдена заявка #' . $customerFormCrmId);
            }
            throw $e;
        }
        $result = $this->getJsonBody($result);
        $response = new BooleanResponse();
        $response->status = $result->status;
        if (property_exists($result, 'message')) {
            $response->message = $result->message;
        }
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
     * @throws NotFoundException
     * @throws GuzzleException
     */
    public function getCustomerFormClaim($customerFormCrmId, $format = 'pdf')
    {
        $url = $this->url . self::ACTION_UNION . '?key=' . $this->apiKey;
        try {
            $result = $this->guzzle->get($url . '&id=' . $customerFormCrmId . '&format=' . $format);
        } catch (GuzzleException $e) {
            if ($e->getCode() == 404) {
                throw new NotFoundException('В CRM не найдена заявка #' . $customerFormCrmId);
            }
            throw $e;
        }
        return $result->getBody()->getContents();
    }

    /**
     * Получить заявление на выпуск сертификата
     *
     * @param int    $customerFormCrmId
     * @param string $format
     * @return string
     * @throws NotFoundException
     * @throws GuzzleException
     */
    public function getCustomerFormCertificateBlank($customerFormCrmId, $format = 'pdf')
    {
        try {
            $result = $this->guzzle->get($this->url . self::ACTION_CERTIFICATE_BLANK, [
                'query' => [
                    'key'    => $this->apiKey,
                    'id'     => $customerFormCrmId,
                    'format' => $format,
                ],
            ]);
        } catch (GuzzleException $e) {
            if ($e->getCode() == 404) {
                throw new NotFoundException('В CRM не найдена заявка #' . $customerFormCrmId);
            }
            throw $e;
        }
        return $result->getBody()->getContents();
    }

    public function getEgrul($customerFormCrmId)
    {
        try {
            $result = $this->guzzle->get($this->url . self::ACTION_EGRUL, [
                'query' => [
                    'key'            => $this->apiKey,
                    'customerFormId' => $customerFormCrmId,
                ],
            ]);
        } catch (GuzzleException $e) {
            if ($e->getCode() == 404) {
                throw new NotFoundException('В CRM не найдена заявка #' . $customerFormCrmId);
            }
            throw $e;
        }
        $json = $result->getBody()->getContents();
        return new GetEgrul($json);
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
     * Отправить данные бланка заявки на сертификат
     *
     * @param int $crmCustomerFormId
     * @param SendCustomerFormData $customerFormData
     * @return SendCustomerFormResponse
     * @throws \Exception
     */
    public function sendCustomerFormData($crmCustomerFormId, SendCustomerFormData $customerFormData)
    {
        $result = $this->guzzle->post($this->url . self::ACTION_PUSH_CUSTOMER_FORM_DATA, [
            RequestOptions::QUERY => ['key' => $this->apiKey],
            RequestOptions::JSON  => [
                'id' => $crmCustomerFormId,
                'formData' => $customerFormData
            ],
        ]);
        $result = $this->getJsonBody($result);
        $response = new SendCustomerFormResponse();
        $response->id = $result->id;
        $response->token = $result->token;
        $response->generationToken = $result->generationToken;
        return $response;
    }

    /**
     * Проверка паспортных данных
     *
     *
     * @param $series
     * @param $number
     * @return GetPassportCheck
     * @throws \Exception
     */
    public function getPassportCheck($series, $number) {
        try {
            $result = $this->guzzle->get($this->url . self::ACTION_PASSPORT_CHECK, [
                'query' => [
                    'key'    => $this->apiKey,
                    'series'     => $series,
                    'number' => $number,
                ],
            ]);
        } catch (GuzzleException $e) {
            if ($e->getCode() == 400) {
                throw new \Exception('Неправильный формат серии и номера паспорта '.$series.' '.$number);
            }

        }

        $result = $this->getJsonBody($result);

        $response = new GetPassportCheck();
        $response->comment = $result->comment;
        $response->status = $result->status;
        return $response;

    }

    /**
     * Проверка СНИЛС данных
     *
     * @param $customerFormCrmId
     * @return GetSnilsCheck
     * @throws \Exception
     */
    public function getSnilsCheck($customerFormCrmId)
    {
        try {
            $result = $this->guzzle->get($this->url . self::ACTION_CHECK_SNILS, [
                'query' => [
                    'key'    => $this->apiKey,
                    'customerFormId' => $customerFormCrmId,
                ],
            ]);
        } catch (GuzzleException $e) {
            if ($e->getCode() == 400) {
                throw new \Exception('Неправильный формат заявки ' . $customerFormCrmId);
            }

        }

        $result = $this->getJsonBody($result);
        $response = new GetSnilsCheck();
        $response->status = $result->status ?? '';
        $response->message = $result->message ?? '';
        $response->createRequestDate = $result->createRequestDate ?? '';
        $response->id = $result->id;

        return $response;
    }

    /**
     * Ссылка для записи сертификата на носитель
     *
     *
     * @param $customerFormId
     * @param $token
     * @return string
     */
    public function certificateWriteUrl($customerFormId, $token)
    {
        return $this->url . 'customerForms/external/writeCertificate?token=' . $token.'&customerFormId='.$customerFormId;
    }

    /**
     * Получение информации из рееферальной ссылке
     *
     * @param SendCheckRef $sendCheckRef
     * @return GetCheckRef
     * @throws \Exception
     */
    public function getCheckRef(SendCheckRef $sendCheckRef)
    {
        $result = $this->guzzle->post($this->url . self::ACTION_CHECK_REFERRAL, [
            RequestOptions::QUERY => ['key' => $this->apiKey],

            RequestOptions::JSON => $sendCheckRef

        ]);

        $result = $this->getJsonBody($result);

        $response = new GetCheckRef();
        $response->id = $result->id ?? '';
        $response->paymentMode = $result->paymentMode ?? '';
        $response->userName = $result->userName ?? '';

        return $response;
    }

    /**
     * Отдает цены по продуктам и платформам
     *
     * @param SendPrice $sendPrice
     * @return GetPrice
     * @throws \Exception
     */
    public function getPrice(SendPrice $sendPrice)
    {
        $result = $this->guzzle->post($this->url . self::ACTION_GET_PRICE, [
            RequestOptions::QUERY => ['key' => $this->apiKey],
            RequestOptions::JSON => $sendPrice
        ]);
        $result = $this->getJsonBody($result);
        $response = new GetPrice();
        $response->productTemplates = [];
        $response->platforms  = [];


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
     * @return PartnerPlatforms
     * @throws \Exception
     */
    public function getPartnerPlatforms(PartnerPlatformsRequest $request)
    {
        $result = $this->guzzle->post($this->url . self::ACTION_GET_PARTNER_PLATFORMS, [
            RequestOptions::QUERY => ['key' => $this->apiKey],
            RequestOptions::JSON => [
                'referalId' => $request->partnerUserId,
                'legalForm' => $request->clientLegalForm,
            ]
        ]);
        $result = $this->getJsonBody($result);
        $response = new PartnerPlatforms();
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
     * @return PartnerProducts
     * @throws \Exception
     */
    public function getPartnerProducts(PartnerProductsRequest $request)
    {
        $result = $this->guzzle->post($this->url . self::ACTION_GET_PARTNER_PRODUCTS, [
            RequestOptions::QUERY => ['key' => $this->apiKey],
            RequestOptions::JSON => [
                'referalId' => $request->partnerUserId,
            ]
        ]);
        $result = $this->getJsonBody($result);
        $response = new PartnerProducts();
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
     * Ссылка для скачивания сертификата
     *
     *
     * @param $customerFormId
     * @param $token
     * @return string
     */
    public function certificateDownloadUrl($customerFormId, $token)
    {
        return $this->url . 'customerForms/external/downloadCertificate?token=' . $token.'&customerFormId='.$customerFormId;
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
        return $this->url . 'customerForms/external/downloadFirstRealization?token=' . $token.'&customerFormId='.$customerFormId;
    }
}
