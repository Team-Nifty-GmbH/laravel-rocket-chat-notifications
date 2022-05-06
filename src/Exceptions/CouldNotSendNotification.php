<?php

declare(strict_types=1);

namespace TeamNiftyGmbh\RocketChatNotifications\Exceptions;

use Exception;
use GuzzleHttp\Exception\ClientException;
use RuntimeException;

class CouldNotSendNotification extends RuntimeException
{
    /**
     * Thrown when connection is missing.
     *
     * @return static
     */
    public static function missingConnection(string $connection = null): self
    {
        return new CouldNotSendNotification(
            "RocketChat notification was not sent. Connection `{$connection}` is missing."
        );
    }

    /**
     * Thrown when domain is missing.
     *
     * @return static
     */
    public static function missingDomain(): self
    {
        return new CouldNotSendNotification('RocketChat notification was not sent. Domain is missing.');
    }

    /**
     * Thrown when channel identifier is missing.
     *
     * @return static
     */
    public static function missingTo(): self
    {
        return new CouldNotSendNotification(
            'RocketChat notification was not sent. Channel identifier is missing.'
        );
    }

    /**
     * Thrown when user or app access token is missing.
     *
     * @return static
     */
    public static function missingFrom(): self
    {
        return new CouldNotSendNotification(
            'RocketChat notification was not sent. Access token or User identifier is missing.'
        );
    }

    /**
     * Thrown when there's a bad response from the RocketChat.
     *
     * @param  ClientException  $exception
     * @return static
     */
    public static function rocketChatRespondedWithAnError(ClientException $exception): self
    {
        $message = $exception->getResponse()->getBody();
        $code = $exception->getResponse()->getStatusCode();
        $headers = $exception->getRequest()->getHeaders();
        array_walk($headers, function (&$item) {
            $item = $item[0] ?? $item;
        });

        return new CouldNotSendNotification(
            "RocketChat responded with an error `{$code} - {$message}` " .
            ". Headers: " . urldecode(http_build_query($headers, '', ', '))
        );
    }

    /**
     * Thrown when we're unable to communicate with RocketChat.
     *
     * @param  \Exception  $exception
     * @return static
     */
    public static function couldNotCommunicateWithRocketChat(Exception $exception): self
    {
        return new CouldNotSendNotification(
            "The communication with RocketChat failed. Reason: {$exception->getMessage()}"
        );
    }
}
