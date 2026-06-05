<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmNotificationService
{
    public function sendToToken(string $token, string $title, string $body, string $type, array $data = []): bool
    {
        if (!config('services.firebase.enabled')) {
            Log::info('FCM skipped because FIREBASE_MESSAGING_ENABLED=false');
            return false;
        }

        $token = trim($token);
        if ($token === '') {
            return false;
        }

        $projectId = (string) config('services.firebase.project_id');
        if ($projectId === '') {
            Log::warning('FCM skipped because FIREBASE_PROJECT_ID is empty');
            return false;
        }

        $accessToken = $this->getAccessToken();
        if ($accessToken === '') {
            return false;
        }

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        'channel_id' => $this->channelId($type),
                        'tag' => $type,
                        'color' => '#FF6B1A',
                    ],
                ],
                'data' => array_merge([
                    'type' => $type,
                    'title' => $title,
                    'body' => $body,
                ], $this->stringifyData($data)),
            ],
        ];

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->asJson()
            ->timeout(15)
            ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", $payload);

        if ($response->failed()) {
            Log::warning('FCM send failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'type' => $type,
            ]);
            return false;
        }

        return true;
    }

    private function channelId(string $type): string
    {
        return str_contains($type, 'announcement') ? 'announcement_channel' : 'payment_channel';
    }

    private function stringifyData(array $data): array
    {
        return collect($data)
            ->mapWithKeys(fn ($value, $key) => [(string) $key => (string) $value])
            ->all();
    }

    private function getAccessToken(): string
    {
        return Cache::remember('firebase_access_token', now()->addMinutes(50), function () {
            $serviceAccount = $this->readServiceAccount();
            if (!$serviceAccount) {
                return '';
            }

            $now = time();
            $tokenUri = $serviceAccount['token_uri'] ?? 'https://oauth2.googleapis.com/token';
            $claims = [
                'iss' => $serviceAccount['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => $tokenUri,
                'iat' => $now,
                'exp' => $now + 3600,
            ];

            $header = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $payload = $this->base64UrlEncode(json_encode($claims));
            $unsignedJwt = "{$header}.{$payload}";

            $signature = '';
            $signed = openssl_sign($unsignedJwt, $signature, $serviceAccount['private_key'], OPENSSL_ALGO_SHA256);
            if (!$signed) {
                Log::warning('Failed to sign Firebase JWT');
                return '';
            }

            $assertion = $unsignedJwt . '.' . $this->base64UrlEncode($signature);

            $response = Http::asForm()
                ->timeout(15)
                ->post($tokenUri, [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $assertion,
                ]);

            if ($response->failed()) {
                Log::warning('Failed to get Firebase access token', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return '';
            }

            return (string) ($response->json('access_token') ?? '');
        });
    }

    private function readServiceAccount(): ?array
    {
        $path = (string) config('services.firebase.service_account_path');
        if ($path === '') {
            Log::warning('Firebase service account path is empty');
            return null;
        }

        if (!str_starts_with($path, DIRECTORY_SEPARATOR) && !preg_match('/^[A-Za-z]:\\\\/', $path)) {
            $path = base_path($path);
        }

        if (!is_file($path)) {
            Log::warning('Firebase service account file not found', ['path' => $path]);
            return null;
        }

        $decoded = json_decode((string) file_get_contents($path), true);
        if (!is_array($decoded) || empty($decoded['client_email']) || empty($decoded['private_key'])) {
            Log::warning('Firebase service account file is invalid', ['path' => $path]);
            return null;
        }

        return $decoded;
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
