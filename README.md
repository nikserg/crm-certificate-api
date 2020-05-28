# crm-certificate-api

API для системы https://crm.uc-itcom.ru/

## Установка

`composer require nikserg/crm-certificate-api`

## Запуск

```php
$client = new Client(<api-ключ>, Client::PRODUCTION_URL); //Для теста использовать Client::TEST_URL
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
* `$client->getEgrul($customerFormCrmId)` - получение выписки из ЕГРЮЛ по заявке на сертификат
* `$client->sendCustomerFormData($crmCustomerFormId, SendCustomerFormData $customerFormData)` - заполнение формы заявления на выпуск сертификата
* `$client->getPassportCheck($series, $number)` - проверка паспорта через ЕСИА
* `$client->getSnilsCheck($customerFormCrmId)` - проверка СНИЛС через ЕСИА

### Без запроса к API

* `$client->editUrl($token)` - формирование ссылки на редактирование заявки на сертификат без авторизации
* `$client->generationUrl($token, $generatonToken, $iframe = false)` - формирование ссылки на генерацию запроса на выпуск сертификата