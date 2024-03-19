<?php

namespace SnBH\Common\Helper;

use Exception;
use Laminas\Filter\File\RenameUpload;

class MoveUpload
{
    /** @var RenameUpload $renameUpload */
    protected $renameUpload;

    /**
     * Essa classe move os arquivos de upload para um diretório específico
     * Para maiores detalhes veja em
     *
     * @see https://docs.zendframework.com/zend-filter/file/#renameupload
     *
     * @param array|RenameUpload     $optionsOrObject Opcoes da classe RenameUpload ou ela mesmo instanciada
     */
    public function __construct($optionsOrObject)
    {
        if (is_array($optionsOrObject)) {
            $this->renameUpload = new RenameUpload($optionsOrObject);
        } elseif ($optionsOrObject instanceof RenameUpload) {
            $this->renameUpload = $optionsOrObject;
        }

        if (!$this->renameUpload) {
            throw new Exception('O parametro $optionsOrObject deve ser uma string '
                . 'de configuração da class ' . RenameUpload::class . ' ou uma instância dela');
        }
    }

    public function getRenameUpload(): RenameUpload
    {
        return $this->renameUpload;
    }

    /**
     * O parametro recebido deve estar no formato conforme a seguinte classe retorna
     *
     * @see \Laminas\Http\PhpEnvironment\Request::getFiles
     *
     * @param array $files Lista de arquivo vindo por upload
     * @param bool $returnFullPathResultOnly Se true, retorna apenas um array com o caminho
     */
    public function move(array $files, $returnFullPathResultOnly = false): array
    {
        foreach ($files as &$file) {
            $file = $this->renameUpload->filter($file);

            if ($returnFullPathResultOnly) {
                $file = $file['tmp_name'];
            }
        }
        return $files;
    }
}
