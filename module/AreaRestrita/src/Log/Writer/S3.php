<?php

namespace AreaRestrita\Log\Writer;

use Aws\Kinesis\KinesisClient;
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
        try {
            // As credenciais estão no env
            $kinesisClient = new KinesisClient([
                'version' => '2013-12-02',
                'region' => 'us-west-2',
            ]);
            $kinesisClient->PutRecord([
                'Data' => $data . PHP_EOL,
                'StreamName' => 'applications-logs',
                'PartitionKey' => '1'
            ]);
        } catch (\Exception $e) {
            $tmpDir = self::$tmpDir;
            if (!file_exists($tmpDir)) {
                mkdir($tmpDir);
            }
            $localFilename = date('Y_m_d_H.i') . "-$priorityName" . '.txt';
            file_put_contents($tmpDir . '/' . $localFilename, $e->getMessage() . PHP_EOL, FILE_APPEND);
        }
    }
}
