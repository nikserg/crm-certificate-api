<?php

namespace nikserg\CRMCertificateAPI\models\request;

/**
 * Модель для передачи файла выпущенного сертификата в CRM
 *
 * @package nikserg\CRMCertificateAPI\models\request
 */
class SendCrtFile
{
    /**
     * @var int ID заявки
     */
    public $id;

    /**
     * @var string Base64-строка с содержимым файла сертификата
     */
    public $content;

    /**
     * @param int $id
     * @param string $content
     * @param string $container
     */
    public function __construct($id, $content)
    {
        $this->id = $id;
        $this->content = $content;
    }
}
