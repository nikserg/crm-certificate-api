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
use nikserg\CRMCertificateAPI\models\request\CheckSnils;
use nikserg\CRMCertificateAPI\models\request\CustomerFormDocuments;
use nikserg\CRMCertificateAPI\models\request\DetectPlatforms as DetectPlatformsRequest;
use nikserg\CRMCertificateAPI\models\request\Egrul as EgrulRequest;
use nikserg\CRMCertificateAPI\models\request\PartnerFullPrice as PartnerFullPriceRequest;
use nikserg\CRMCertificateAPI\models\request\PartnerPlatforms as PartnerPlatformsRequest;
use nikserg\CRMCertificateAPI\models\request\PartnerProducts as PartnerProductsRequest;
use nikserg\CRMCertificateAPI\models\request\PartnerStores as PartnerStoresRequest;
use nikserg\CRMCertificateAPI\models\request\SendCheckRef;
use nikserg\CRMCertificateAPI\models\request\SendCustomerForm as SendCustomerFormRequest;
use nikserg\CRMCertificateAPI\models\request\SendCustomerFormData;
use nikserg\CRMCertificateAPI\models\request\SendOpportunity as SendOpportunityRequest;
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
use nikserg\CRMCertificateAPI\models\response\ReferralUser;
use nikserg\CRMCertificateAPI\models\response\SendCustomerForm as SendCustomerFormResponse;
use nikserg\CRMCertificateAPI\models\response\SendOpportunity as SendOpportunityResponse;
use nikserg\CRMCertificateAPI\models\response\SnilsCheck;
use Psr\Http\Message\ResponseInterface;

/**
 * Клиент для связи с CRM
 *
 * @package nikserg\CRMCertificateAPI
 */
class Client
{
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
     * @param string $apiKey
     * @param string $url
     */
    public function __construct($apiKey, $url = 'https://crm.uc-itcom.ru/index.php')
    {
        $this->apiKey = $apiKey;
        $this->url = trim($url, " /");
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
            $response = $this->guzzle->request($method, "$this->url/gateway/itkExchange/$endpoint", $options);
        } catch (GuzzleException $e) {
            throw new TransportException("Ошибка запроса; {$e->getMessage()}");
        }
        switch ($response->getStatusCode()) {
            case 200:
            case 204:
                return $response;
            case 400:
                throw new InvalidRequestException("$endpoint: Неверный формат запроса");
            case 404:
                throw new NotFoundException("$endpoint: Сущность или точка АПИ не найдены");
            case 500:
                throw new ServerException("$endpoint: Ошибка сервера \n" . $response->getBody()->getContents());
            default:
                throw new TransportException("$endpoint: Неожиданный код ответа {$response->getStatusCode()}");
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
            $response = $this->guzzle->request($method, "$this->url/gateway/itkExchange/$endpoint", $options);
        } catch (GuzzleException $e) {
            throw new TransportException("Ошибка запроса; {$e->getMessage()}");
        }
        try {
            $data = $this->getJsonBody($response);
            switch ($response->getStatusCode()) {
                case 200:
                    return $data;
                case 400:
                    throw new InvalidRequestException("$endpoint: " . $data->error->message ?? $data->message ?? "Неверный формат запроса");
                case 404:
                    throw new NotFoundException("$endpoint: " . $data->error->message ?? $data->message ?? "Сущность или точка АПИ не найдены");
                case 500:
                    throw new ServerException("$endpoint: " . $data->error->message ?? $data->message ?? "Неожиданная ошибка сервера");
                default:
                    throw new TransportException("$endpoint: " . $data->error->message ?? $data->message ?? "Неожиданный код ответа {$response->getStatusCode()}");
            }
        } catch (TransportException $e) {
            throw new TransportException('Ошибка во время отправки запроса ' . print_r([
                    $method,
                    $this->url . $endpoint,
                    $options,
                    $data,
                ], true) . ': ' . $e->getMessage(), $e->getCode(), $e);
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
            throw new TransportException('Пустое тело ответа на JSON запрос. Код ответа ' . $response->getStatusCode());
        }
        $json = json_decode($body);
        $jsonErrorCode = json_last_error();
        $jsonErrorMessage = json_last_error_msg();
        if ($jsonErrorCode !== JSON_ERROR_NONE) {
            throw new TransportException("$jsonErrorMessage: $body", $jsonErrorCode);
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
        $result = $this->requestJson('POST', 'pushCustomerForm', $customerForm);
        return $this->fill(SendCustomerFormResponse::class, $result);
    }

    /**
     * Отправить запрос на создание/изменение сделки и создание/изменение клиента
     *
     *
     * @param SendOpportunityRequest $sendOpportunity
     * @return SendOpportunityResponse
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function sendOpportunity(SendOpportunityRequest $sendOpportunity)
    {

        $result = $this->requestJson('POST', 'pushOpportunity', $sendOpportunity);
        return $this->fill(SendOpportunityResponse::class, $result);
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
        $result = $this->getJsonBody($this->request('GET', 'pullCustomerForm', [
            RequestOptions::QUERY => [
                'id' => $customerFormCrmId,
            ],
        ]));
        return $this->fill(GetCustomerForm::class, $result);
    }

    /**
     * Получить информацию о сделке
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
        $result = $this->getJsonBody($this->request('GET', 'pullOpportunity', [
            RequestOptions::QUERY => [
                'id' => $opportunityCrmId,
            ],
        ]));
        return $this->fill(GetOpportunity::class, $result);
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
        $result = $this->requestJson('POST', 'pushCustomerFormStatus', $changeStatus);
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
        $result = $this->getJsonBody($this->request('GET', 'deleteCustomerForm', [
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
        $result = $this->request('GET', 'union', [
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
        $result = $this->request('GET', 'certificateBlank', [
            RequestOptions::QUERY => [
                'id'     => $customerFormCrmId,
                'format' => $format,
            ],
        ]);
        return $result->getBody()->getContents();
    }

    /**
     * Выписка ЕГРЮЛ
     *
     * @param EgrulRequest $request
     * @return EgrulResponse
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function egrul(EgrulRequest $request)
    {
        return new EgrulResponse($this->requestJson('GET', 'egrul', $request));
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
        $result = $this->requestJson('POST', 'pushCustomerFormData', [
            'id'       => $crmCustomerFormId,
            'formData' => $customerFormData,
        ]);
        return $this->fill(SendCustomerFormResponse::class, $result);
    }

    /**
     * Расширенная проверка паспортных данных
     *
     * @param CheckPassport $request
     * @return PassportCheck
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function checkPassport(CheckPassport $request)
    {
        $result = $this->requestJson('GET', 'checkPassport', $request);
        return $this->fill(PassportCheck::class, $result);
    }

    /**
     * Проверка СНИЛС данных
     *
     * @param CheckSnils $request
     * @return SnilsCheck
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function checkSnils(CheckSnils $request)
    {
        $result = $this->requestJson('GET', 'checkSnils', $request);
        return $this->fill(SnilsCheck::class, $result);
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
        $result = $this->requestJson('POST', 'getRefUserInfo', $sendCheckRef);
        if ($result === null) {
            return null;
        }
        return $this->fill(ReferralUser::class, $result);
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
        $result = $this->requestJson('POST', 'detectPlatforms', $request);
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
        $this->request('POST', 'pushCustomerFormDocuments', [
            RequestOptions::MULTIPART => $multipart,
        ]);
        return true;
    }

    /**
     * Получает платформы, доступные партнеру переданному в запросе
     *
     * @param PartnerPlatformsRequest $request
     * @return PartnerPlatform[]
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getPartnerPlatformsAll(PartnerPlatformsRequest $request)
    {
        $result = $this->requestJson('POST', 'getPartnerPlatforms', $request);
        return $this->fillList(PartnerPlatform::class, $result->platforms);
    }

    /**
     * Получает продукты, настроенные для партнера переданного в запросе
     *
     * @param PartnerProductsRequest $request
     * @return PartnerProduct[]
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getPartnerProductsAll(PartnerProductsRequest $request)
    {
        $result = $this->requestJson('POST', 'getPartnerProducts', $request);
        return $this->fillList(PartnerProduct::class, $result->products);
    }

    /**
     * Отдает сумму по выбранным продуктам и плаформам для партнера
     *
     * @param PartnerFullPriceRequest $fullPriceRequest
     * @return float
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getPartnerFullPrice(PartnerFullPriceRequest $fullPriceRequest)
    {
        $result = $this->requestJson('POST', 'getPartnerFullPrice', $fullPriceRequest);
        return $result->price;
    }

    /**
     * Отдает список точек/складов/трансферов партнера
     *
     * @param PartnerStoresRequest $partnerStores
     * @return Store[]
     * @throws InvalidRequestException
     * @throws NotFoundException
     * @throws ServerException
     * @throws TransportException
     */
    public function getPartnerStores(PartnerStoresRequest $partnerStores)
    {
        $result = $this->requestJson('POST', 'getPartnerStores', $partnerStores);
        return $this->fillList(Store::class, $result->stores);
    }

    public function fill($class, $attributes)
    {
        $model = new $class;
        foreach ($attributes as $attribute => $value) {
            //zurmo-id => zurmoid
            $attribute = str_replace('-', '', $attribute);
            $model->$attribute = $value;
        }
        return $model;
    }

    public function fillList($class, $list)
    {
        $models = [];
        foreach ($list as $item) {
            $models[] = $this->fill($class, $item);
        }
        return $models;
    }

    #region urls

    /**
     * Ссылка для скачивания сертификата
     *
     * @param $customerFormId
     * @param $token
     * @return string
     */
    public function certificateDownloadUrl($customerFormId, $token)
    {
        return $this->url . '/customerForms/external/downloadCertificate?token=' . $token . '&customerFormId=' . $customerFormId;
    }

    /**
     * Ссылка для скачивания реализации
     *
     * @param $customerFormId
     * @param $token
     * @return string
     */
    public function realizationDownloadUrl($customerFormId, $token)
    {
        return $this->url . '/customerForms/external/downloadFirstRealization?token=' . $token . '&customerFormId=' . $customerFormId;
    }

    /**
     * Индивидуальная ссылка для редактирования
     *
     * @param $token
     * @return string
     */
    public function editUrl($token)
    {
        return $this->url . '/customerForms/external?token=' . $token;
    }

    /**
     * Индивидуальная ссылка для генерации
     *
     * @param      $token
     * @param      $generatonToken
     * @param bool $iframe Выводить отображение для фрейма
     * @return string
     */
    public function generationUrl($token, $generatonToken, $iframe = false)
    {
        $return = $this->url . '/customerForms/external/generate?token=' . $token . '&generationToken=' . $generatonToken;
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
        return $this->url . '/customerForms/external/step1?token=' . $token . '&customerFormId=' . $customerFormId;
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
        return $this->url . '/customerForms/external/writeCertificate?token=' . $token . '&customerFormId=' . $customerFormId;
    }

    /**
     * Ссылка для оплаты счета онлайн
     *
     *
     * @param      $paymentToken
     * @param bool $iframe
     * @return string
     */
    public function paymentUrl($paymentToken, $iframe = false)
    {
        return $this->url . '/clients/payment?paymentToken=' . $paymentToken . '&iframe=' . intval($iframe);
    }
    #endregion urls
}
