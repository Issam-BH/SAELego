<?php
class Captcha
{
    private string $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function validate(string $token, ?string $remoteIp = null): array
    {
        if (empty($token)) {
            return ['success' => false, 'error-codes' => ['missing-input']];
        }

        $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
        $data = [
            'secret'   => $this->secretKey,
            'response' => $token,
        ];

        if ($remoteIp) {
            $data['remoteip'] = $remoteIp;
        }

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
                'timeout' => 5
            ]
        ];

        $context = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            return ['success' => false, 'error-codes' => ['internal-error']];
        }

        return json_decode($response, true);
    }

    public function isValid(string $token, ?string $remoteIp = null): bool
    {
        $result = $this->validate($token, $remoteIp);

        if (!$result['success']) {
            error_log('Turnstile validation failed: ' . implode(', ', $result['error-codes'] ?? []));
        }

        return $result['success'] ?? false;
    }
}