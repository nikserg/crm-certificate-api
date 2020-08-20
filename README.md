# crm-certificate-api

API для системы https://crm.uc-itcom.ru/

## Установка

`composer require nikserg/crm-certificate-api`

## Запуск

```php
$client = new Client(<api-ключ>, 'https://crm.uc-itcom.ru/index.php/'); // or 'https://dev.uc-itcom.ru/index.php/'
```

## Функции

### Запросы к API

* `$client->sendCustomerForm(SendCustomerFormRequest $customerForm)` - создание заявки на сертификат
* `$client->getCustomerForm($customerFormCrmId)` - получение данных о заявке на сертификат
* `$client->getOpportunity($opportunityCrmId)` - получение данных о сделке
* `$client->changeStatus(ChangeStatus $changeStatus)` - cмена статуса заявки
* `$client->deleteCustomerForm($customerFormCrmId)` - удаление заявки на сертификат
* `$client->getCustomerFormClaim($customerFormCrmId, $format = 'pdf')` - получение содержимого файла заявления на выпуск сертификата
* `$client->getCustomerFormCertificateBlank($customerFormCrmId, $format = 'pdf')` - получение содержимого файла бланка сертификата
* `$client->sendCustomerFormData($crmCustomerFormId, SendCustomerFormData $customerFormData)` - заполнение формы заявления на выпуск сертификата

### Без запроса к API

* `$client->editUrl($token)` - формирование ссылки на редактирование заявки на сертификат без авторизации
* `$client->generationUrl($token, $generatonToken, $iframe = false)` - формирование ссылки на генерацию запроса на выпуск сертификата
* `$client->realizationDownloadUrl($customerFormId, $token)` - формирование ссылки для скачивания реализации по заявке
* `$client->certificateDownloadUrl($customerFormId, $token)` - формирование ссылки для скачивания выпущенного сертификата
* `$client->certificateWriteUrl($customerFormId, $token)` - формирование ссылки для записи выпущенного сертификата на носитель
