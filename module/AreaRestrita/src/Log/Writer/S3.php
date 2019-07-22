<?php

namespace AreaRestrita\Log\Writer;

use Zend\Log\Writer\AbstractWriter;

class S3 extends AbstractWriter
{

    public static $tmpDir = 'data/temp/logs';

    protected function doWrite(array $event): void
    {
        $this->persist($this->formatter->format($event), $event['priorityName']);
    }

    protected function persist($data, $priorityName = '')
    {
        $tmpDir = self::$tmpDir;
        if (!file_exists($tmpDir)) {
            mkdir($tmpDir);
        }
        $localFilename = date('Y_m_d_H.i') . "-$priorityName" . '.txt';
        file_put_contents($tmpDir . '/' . $localFilename, $data . PHP_EOL, FILE_APPEND);
        self::uploadData();
    }

    public static function uploadData()
    {
        $localFilename = '/' . date('Y_m_d_H.i-.*.\tx\t') . '/';
        $tmpDir = self::$tmpDir;
        $bucketName = 'log-area-restrita';
        $logs = glob($tmpDir . '/*.txt');

        $s3 = new \ZendService\Amazon\S3\S3(getenv('AWS_ACCESS_KEY_ID'), getenv('AWS_SECRET_ACCESS_KEY'));

        foreach ($logs as $log) {
            // Não faz nada com o arquivo de log do minuto atual
            if (preg_match($localFilename, $log) == 1) {
                continue;
            }

            $s3Filename = str_replace([$tmpDir, '_'], [$bucketName, '/'], $log);
            try {
                $s3->putFile($log, $s3Filename);
            } catch (\Exception $ex) {
                
            }
            if (file_exists($log)) {
                unlink($log);
            }
        }
    }
}
