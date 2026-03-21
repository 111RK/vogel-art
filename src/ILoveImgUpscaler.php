<?php

class ILoveImgUpscaler
{
    private string $publicKey;
    private string $secretKey;

    public function __construct()
    {
        $this->publicKey = ILOVEIMG_PUBLIC_KEY;
        $this->secretKey = ILOVEIMG_SECRET_KEY;
    }

    public function upscale(string $filePath, int $multiplier = 2): ?string
    {
        if (empty($this->publicKey) || empty($this->secretKey)) return null;
        if (!file_exists($filePath)) return null;
        if (!in_array($multiplier, [2, 4])) $multiplier = 2;

        $token = $this->generateToken();
        if (!$token) return null;

        $start = $this->startTask($token);
        if (!$start) return null;

        $server = $start['server'];
        $taskId = $start['task'];

        $upload = $this->uploadFile($server, $taskId, $token, $filePath);
        if (!$upload) return null;

        $serverFilename = $upload['server_filename'];

        $process = $this->processTask($server, $taskId, $token, $serverFilename, basename($filePath), $multiplier);
        if (!$process) return null;

        $result = $this->downloadResult($server, $taskId, $token);
        if (!$result) return null;

        file_put_contents($filePath, $result);
        return $filePath;
    }

    private function generateToken(): ?string
    {
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode([
            'iss' => $this->publicKey,
            'iat' => time(),
            'exp' => time() + 7200,
            'nbf' => time(),
        ]));
        $signature = hash_hmac('sha256', "$header.$payload", $this->secretKey, true);
        $sig64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        return "$header.$payload.$sig64";
    }

    private function startTask(string $token): ?array
    {
        $ch = curl_init('https://api.iloveimg.com/v1/start/upscaleimage');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token],
        ]);
        $resp = json_decode(curl_exec($ch), true);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code === 200 && !empty($resp['server']) && !empty($resp['task'])) {
            return $resp;
        }
        return null;
    }

    private function uploadFile(string $server, string $taskId, string $token, string $filePath): ?array
    {
        $ch = curl_init("https://$server/v1/upload");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token],
            CURLOPT_POSTFIELDS => [
                'task' => $taskId,
                'file' => new CURLFile($filePath),
            ],
        ]);
        $resp = json_decode(curl_exec($ch), true);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code === 200 && !empty($resp['server_filename'])) {
            return $resp;
        }
        return null;
    }

    private function processTask(string $server, string $taskId, string $token, string $serverFilename, string $filename, int $multiplier): bool
    {
        $body = json_encode([
            'task' => $taskId,
            'tool' => 'upscaleimage',
            'files' => [
                [
                    'server_filename' => $serverFilename,
                    'filename' => $filename,
                ],
            ],
            'multiplier' => $multiplier,
        ]);

        $ch = curl_init("https://$server/v1/process");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => $body,
        ]);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $code === 200;
    }

    private function downloadResult(string $server, string $taskId, string $token): ?string
    {
        $ch = curl_init("https://$server/v1/download/$taskId");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token],
        ]);
        $data = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code === 200 && strlen($data) > 1000) {
            return $data;
        }
        return null;
    }
}
