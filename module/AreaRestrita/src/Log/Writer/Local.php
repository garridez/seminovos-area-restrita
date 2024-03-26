<?php

namespace AreaRestrita\Log\Writer;

use Laminas\Log\Writer\AbstractWriter;

class Local extends AbstractWriter
{
    public static string $tmpDir = 'data/logs';

    protected function doWrite(array $event): void
    {
        $this->persist($this->formatter->format($event), $event['priorityName']);
    }

    protected function persist(mixed $data, string|int $priorityName = ''): void
    {
        $tmpDir = self::$tmpDir;
        if (!file_exists($tmpDir)) {
            mkdir($tmpDir);
        }
        $localFilename = date('Y_m_d') . "-$priorityName" . '.log';
        file_put_contents($tmpDir . '/' . $localFilename, $data . PHP_EOL, FILE_APPEND);
    }
}
