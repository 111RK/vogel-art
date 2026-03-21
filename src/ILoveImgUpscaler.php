<?php

class ILoveImgUpscaler
{
    private string $publicKey;
    private ?string $token = null;

    public function __construct()
    {
        $this->publicKey = ILOVEIMG_PUBLIC_KEY;
    }

    public function upscale(string $filePath, int $multiplier = 2): ?string
    {
        if (empty($this->publicKey)) return null;
        if (!file_exists($filePath)) return null;
        if (!in_array($multiplier, [2, 4])) $multiplier = 2;

        $this->token = $this->authenticate();
        if (!$this->token) return null;

        $start = $this->startTask();
        if (!$start) return null;

        $server = $start['server'];
        $taskId = $start['task'];

        $upload = $this->uploadFile($server, $taskId, $filePath);
        if (!$upload) return null;

        $serverFilename = $upload['server_filename'];

        $process = $this->processTask($server, $taskId, $serverFilename, basename($filePath), $multiplier);
        if (!$process) return null;

        $result = $this->downloadResult($server, $taskId);
        if (!$result) return null;

        file_put_contents($filePath, $result);
        return $filePath;
    }

    private function authenticate(): ?string
    {
        $ch = curl_init('https://api.iloveimg.com/v1/auth');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode(['public_key' => $this->publicKey]),
        ]);
        $resp = json_decode(curl_exec($ch), true);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($code === 200 && !empty($resp['token'])) ? $resp['token'] : null;
    }

    private function startTask(): ?array
    {
        $ch = curl_init('https://api.iloveimg.com/v1/start/upscaleimage');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $this->token],
        ]);
        $resp = json_decode(curl_exec($ch), true);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($code === 200 && !empty($resp['server']) && !empty($resp['task'])) ? $resp : null;
    }

    private function uploadFile(string $server, string $taskId, string $filePath): ?array
    {
        $ch = curl_init("https://$server/v1/upload");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $this->token],
            CURLOPT_POSTFIELDS => [
                'task' => $taskId,
                'file' => new CURLFile($filePath),
            ],
        ]);
        $resp = json_decode(curl_exec($ch), true);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($code === 200 && !empty($resp['server_filename'])) ? $resp : null;
    }

    private function processTask(string $server, string $taskId, string $serverFilename, string $filename, int $multiplier): bool
    {
        $body = json_encode([
            'task' => $taskId,
            'tool' => 'upscaleimage',
            'files' => [['server_filename' => $serverFilename, 'filename' => $filename]],
            'multiplier' => $multiplier,
        ]);

        $ch = curl_init("https://$server/v1/process");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->token,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_TIMEOUT => 120,
        ]);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $code === 200;
    }

    private function downloadResult(string $server, string $taskId): ?string
    {
        $ch = curl_init("https://$server/v1/download/$taskId");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $this->token],
            CURLOPT_TIMEOUT => 120,
        ]);
        $data = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($code === 200 && strlen($data) > 1000) ? $data : null;
    }
}
