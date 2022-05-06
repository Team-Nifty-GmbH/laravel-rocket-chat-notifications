<?php

declare(strict_types=1);

namespace TeamNiftyGmbh\RocketChatNotifications;

use GuzzleHttp\Client as HttpClient;
use TeamNiftyGmbh\RocketChatNotifications\Exceptions\CouldNotSendNotification;
use TeamNiftyGmbh\RocketChatNotifications\Messages\RocketChatMessage;

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
     * Send a RocketChat message
     *
     * @param string $domain
     * @param string $token
     * @param string $userId
     * @param string $to
     * @param RocketChatMessage $message
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function send(string $domain,
                                string $token,
                                string $userId,
                                string $to,
                                RocketChatMessage $message): void
    {
        $url = rtrim($domain, '/') . '/api/v1/chat.postMessage';
        $options = [
            'headers' => [
                'X-Auth-Token' => $token,
                'X-User-Id' => $userId,
                'Content-Type' => 'application/json'
            ],
            'json' => array_merge($message->toArray(), [
                'channel' => $to,
            ]),
        ];

        $client = new HttpClient();
        $client->post($url, $options);
    }

    /**
     * Send a RocketChat message via defined connection from rocket-chat config
     *
     * @param string $connection
     * @param string $to
     * @param RocketChatMessage $message
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function sendVia(string $connection, string $to, RocketChatMessage $message): void
    {
        $rocketChatConnection = config('rocket-chat.connections.' . $connection);

        if (!$rocketChatConnection) {
            throw CouldNotSendNotification::missingConnection($connection);
        }

        if ($domain = $rocketChatConnection['url']) {
            throw CouldNotSendNotification::missingDomain();
        }

        if (($token = $rocketChatConnection['token']) || ($userId = $rocketChatConnection['user_id'])) {
            throw CouldNotSendNotification::missingFrom();
        }

        self::send($domain, $token, $userId, $to, $message);
    }

    /**
     * Returns RocketChat domain.
     *
     * @return string
     */
    public function getDomain(): string
    {
        return $this->url;
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
     * @throws \GuzzleHttp\Exception\GuzzleException
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function post(string $url, array $options): void
    {
        $this->http->post($url, $options);
    }
}
