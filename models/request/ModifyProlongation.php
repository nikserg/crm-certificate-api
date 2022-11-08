<?php

namespace nikserg\CRMCertificateAPI\models\request;

/**
 * Модель для передачи информации об изменении заявки продления
 *
 * @package nikserg\CRMCertificateAPI\models\request
 */
class ModifyProlongation
{
    /**
     * ID заявки
     *
     * @var
     */

    public $id;
    /**
     * Тип носителя
     *
     * @var
     */
    public $token;

    /**
     * Тип сертификата
     *
     * @var
     */
    public $certificate;
}
