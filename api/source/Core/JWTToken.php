<?php

namespace Source\Core;

use DateTimeImmutable;

class JWTToken
{
    private string $secretKey = JWT_SECRET_KEY;
    private string $url = CONF_URL_BASE;

    public function encode(array $payLoad): string
    {
        $issuedAt = new DateTimeImmutable();
        $header = [
            "typ" => "JWT",
            "alg" => "HS256"
        ];
        $payload = [
            "iat" => $issuedAt->getTimestamp(),
            "jti" => base64_encode(random_bytes(16)),
            "iss" => $this->url,
            "nbf" => $issuedAt->getTimestamp(),
            "exp" => $issuedAt->modify("+90 minutes")->getTimestamp(),
            "data" => $payLoad
        ];

        $base64Header = $this->base64UrlEncode(json_encode($header));
        $base64Payload = $this->base64UrlEncode(json_encode($payload));
        $signature = hash_hmac("sha256", "{$base64Header}.{$base64Payload}", $this->secretKey, true);

        return "{$base64Header}.{$base64Payload}." . $this->base64UrlEncode($signature);
    }

    public function decode($token): bool|object
    {
        $parts = explode(".", $token);

        if (count($parts) !== 3) {
            return false;
        }

        [$base64Header, $base64Payload, $base64Signature] = $parts;
        $signature = $this->base64UrlDecode($base64Signature);
        $validSignature = hash_hmac("sha256", "{$base64Header}.{$base64Payload}", $this->secretKey, true);

        if (!hash_equals($validSignature, $signature)) {
            return false;
        }

        $payload = json_decode($this->base64UrlDecode($base64Payload));
        $now = (new DateTimeImmutable())->getTimestamp();

        if (!$payload || $payload->iss !== $this->url || $payload->nbf > $now || $payload->exp < $now) {
            return false;
        }

        return $payload;
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), "+/", "-_"), "=");
    }

    private function base64UrlDecode(string $data): string
    {
        $data .= str_repeat("=", (4 - strlen($data) % 4) % 4);
        return base64_decode(strtr($data, "-_", "+/"));
    }
}
