<?php

declare(strict_types=1);

namespace TeamNiftyGmbh\RocketChatNotifications\Exceptions;

use Exception;
use GuzzleHttp\Exception\ClientException;
use RuntimeException;

final class CouldNotSendNotification extends RuntimeException
{
    /**
     * Thrown when channel identifier is missing.
     *
     * @return static
     */
    public static function missingTo(): self
    {
        return new CouldNotSendNotification('RocketChat notification was not sent. Channel identifier is missing.');
    }

    /**
     * Thrown when user or app access token is missing.
     *
     * @return static
     */
    public static function missingFrom(): self
    {
        return new CouldNotSendNotification('RocketChat notification was not sent. Access token is missing.');
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

        return new CouldNotSendNotification("RocketChat responded with an error `{$code} - {$message}`");
    }

    /**
     * Thrown when we're unable to communicate with RocketChat.
     *
     * @param  \Exception  $exception
     * @return static
     */
    public static function couldNotCommunicateWithRocketChat(Exception $exception): self
    {
        return new CouldNotSendNotification("The communication with RocketChat failed. Reason: {$exception->getMessage()}");
    }
}
