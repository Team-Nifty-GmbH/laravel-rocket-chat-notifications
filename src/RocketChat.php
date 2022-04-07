<?php

declare(strict_types=1);

namespace TeamNiftyGmbh\RocketChatNotifications;

use GuzzleHttp\Client as HttpClient;

class RocketChat
{
    /** @var HttpClient */
    private HttpClient $http;

    /** @var string */
    private string $url;

    /** @var string */
    private string $token;

    /** @var string */
    private string $userId;

    /**
     * @param HttpClient $http
     * @param string $url
     * @param string $token
     * @param string $userId
     */
    public function __construct(HttpClient $http, string $url, string $token, string $userId)
    {
        $this->http = $http;
        $this->url = rtrim($url, '/');
        $this->token = $token;
        $this->userId = $userId;
    }

    /**
     * Returns RocketChat token.
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Returns user id.
     *
     * @return string|null
     */
    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * Send a message.
     *
     * @param string $to
     * @param array $message
     * @return void
     */
    public function sendMessage(string $to, array $message): void
    {
        $url = $this->url . '/api/v1/chat.postMessage';

        $this->post($url, [
            'headers' => [
                'X-Auth-Token' => $this->token,
                'X-User-Id' => $this->userId,
                'Content-Type' => 'application/json'
            ],
            'json' => array_merge($message, [
                'channel' => $to,
            ]),
        ]);
    }

    /**
     * Perform a simple post request.
     *
     * @param string $url
     * @param array $options
     * @return void
     */
    private function post(string $url, array $options): void
    {
        $this->http->post($url, $options);
    }
}
