<?php

namespace App\Libraries;

/**
 * JWT Helper — HS256
 * Implementación pura en PHP sin dependencias externas.
 * Secreto: JWT_SECRET en .env
 */
class JwtHelper
{
    private static function secret(): string
    {
        $secret = getenv('JWT_SECRET') ?: '';
        if (empty($secret)) {
            // Fallback a una clave derivada de la app key de CI4
            $secret = getenv('app.key') ?: 'portfolio-pro-jwt-secret-key-2024';
        }
        return $secret;
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }

    /**
     * Genera un JWT con payload dado y TTL en segundos (default 8h).
     */
    public static function generate(array $payload, int $ttl = 28800): string
    {
        $header = self::base64UrlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));

        $payload['iat'] = time();
        $payload['exp'] = time() + $ttl;

        $payloadEncoded = self::base64UrlEncode(json_encode($payload));

        $signature = hash_hmac('sha256', "$header.$payloadEncoded", self::secret(), true);
        $signatureEncoded = self::base64UrlEncode($signature);

        return "$header.$payloadEncoded.$signatureEncoded";
    }

    /**
     * Valida el JWT. Retorna el payload decodificado o false si inválido/expirado.
     */
    public static function validate(string $token): array|false
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        [$header, $payloadEncoded, $signatureEncoded] = $parts;

        // Verificar firma
        $expectedSig = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payloadEncoded", self::secret(), true)
        );

        if (!hash_equals($expectedSig, $signatureEncoded)) {
            return false;
        }

        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);
        if (!is_array($payload)) {
            return false;
        }

        // Verificar expiración
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }

        return $payload;
    }
}
