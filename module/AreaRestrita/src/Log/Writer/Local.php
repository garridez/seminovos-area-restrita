<?php

namespace AreaRestrita\Log\Writer;

use Laminas\Log\Writer\AbstractWriter;

class Local extends AbstractWriter
{

    public static $tmpDir = 'data/logs';

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
        $localFilename = date('Y_m_d') . "-$priorityName" . '.log';
        file_put_contents($tmpDir . '/' . $localFilename, $data . PHP_EOL, FILE_APPEND);
    }
}
