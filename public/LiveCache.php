<?php

class LiveCache
{

    protected $cacheName;

    public const REVALIDATE_HEADER = 'LIVE_CACHE_REVALIDATE';

    protected function getBaseCachePath()
    {
        return dirname(__DIR__) . '/data/cache/live-cache';
    }

    protected function getFilenameCache()
    {
        if ($this->cacheName) {
            return $this->cacheName;
        }
        $folderCache = $this->getBaseCachePath() . '/cache';
        $cacheName = preg_replace('/[^0-9a-zA-Z]/', '-', $_SERVER['REQUEST_URI']);
        $cacheName = preg_replace('/-+/', '-', $cacheName);
        $cacheName = trim($cacheName, '-');
        $cacheName = substr($cacheName, 0, 50);
        $cacheName = str_pad($cacheName, 50, '-', STR_PAD_RIGHT);
        $md5 = md5($_SERVER['REQUEST_URI']);

        return $this->cacheName = $folderCache . '/' . substr($md5, 0, 2) . '/' . $cacheName . '-' . $md5 . '.response';
    }

    public function init()
    {
        $cacheName = $this->getFilenameCache();

        if (!$this->isRevalidate() && file_exists($cacheName)) {
            $this->loadFromCache();
            $this->revalidateCache();
            die;
        } else {
            $this->registerToCreateCache();
        }

        if (!$this->isRevalidate()) {
            $this->revalidateCache();
        }
    }

    protected function isRevalidate()
    {
        return isset($_SERVER['HTTP_' . self::REVALIDATE_HEADER]) && $_SERVER['HTTP_' . self::REVALIDATE_HEADER];
    }

    protected function revalidateCache()
    {
        $headers = $this->getCurrentHeaders();

        $headers[] = self::REVALIDATE_HEADER . ':1';
        $headers[] = str_replace('_', '-', self::REVALIDATE_HEADER) . ':1';

        $scheme = 'http';
        if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {
            $scheme = 'https';
        }
        $url = $scheme . '://'
            . $_SERVER['SERVER_ADDR']
            . ':' . $_SERVER['SERVER_PORT']
            . $_SERVER['REQUEST_URI'];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_exec($ch);
    }

    protected function registerToCreateCache()
    {
        ob_start();
        $cacheName = $this->getFilenameCache();
        register_shutdown_function(function () use ($cacheName) {

            if (!file_exists(dirname($cacheName))) {
                mkdir(dirname($cacheName), 0744, true);
            }

            $header = headers_list();
            $body = ob_get_contents();
            $metadata = [
                'headers' => $header,
                'body' => $body
            ];
            $metadata = json_encode($metadata);
            if ($metadata) {
                file_put_contents($cacheName, $metadata);
            }
        });
    }

    protected function loadFromCache()
    {
        $cacheName = $this->getFilenameCache();
        $data = file_get_contents($cacheName);
        $data = json_decode($data, true);
        if ($data && $data['headers']) {
            foreach ($data['headers'] as $header) {
                header($header);
            }
        }
        header('X-SnBH-Live-Cache: 1');
        echo $data['body'];
    }

    protected function getCurrentHeaders()
    {
        $headers = [];
        foreach ($_SERVER as $k => $v) {
            if (strpos($k, 'HTTP_') !== false) {
                $k = str_replace('HTTP_', '', $k);
                $headers[] = $k . ':' . $v;
                $k = str_replace('_', '-', $k);
                $headers[] = $k . ':' . $v;
            }
        }
        return $headers;
    }
}
