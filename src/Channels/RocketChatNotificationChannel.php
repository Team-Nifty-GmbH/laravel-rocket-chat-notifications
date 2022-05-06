<?php

declare(strict_types=1);

namespace TeamNiftyGmbh\RocketChatNotifications\Channels;

use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Notifications\Notification;
use TeamNiftyGmbh\RocketChatNotifications\RocketChat;
use TeamNiftyGmbh\RocketChatNotifications\Messages\RocketChatMessage;
use TeamNiftyGmbh\RocketChatNotifications\Exceptions\CouldNotSendNotification;

class RocketChatNotificationChannel
{
    /** @var RocketChat The HTTP client instance. */
    private RocketChat $rocketChat;

    /**
     * Create a new RocketChat channel instance.
     *
     * @param  RocketChat $rocketChat
     * @return void
     */
    public function __construct(RocketChat $rocketChat)
    {
        $this->rocketChat = $rocketChat;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param Notification $notification
     * @return void
     *
     * @throws CouldNotSendNotification|\GuzzleHttp\Exception\GuzzleException
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        /** @var RocketChatMessage $message */
        $message = $notification->toRocketChat($notifiable);

        if ($message->getDomain()) {
            $this->rocketChat->setDomain($message->getDomain());
        }

        if (!$this->rocketChat->getDomain()) {
            throw CouldNotSendNotification::missingDomain();
        }

        $to = $message->getChannel() ?: $notifiable->routeNotificationFor('RocketChat');
        if ($to === null) {
            throw CouldNotSendNotification::missingTo();
        }

        if ($message->getFrom()) {
            $this->rocketChat->setToken($message->getFrom());
        }

        if ($message->getUserId()) {
            $this->rocketChat->setUserId($message->getUserId());
        }

        if (!$this->rocketChat->getToken() || !$this->rocketChat->getUserId()) {
            throw CouldNotSendNotification::missingFrom();
        }

        try {
            $this->sendMessage($to, $message);
        } catch (ClientException $exception) {
            throw CouldNotSendNotification::rocketChatRespondedWithAnError($exception);
        } catch (Exception $exception) {
            throw CouldNotSendNotification::couldNotCommunicateWithRocketChat($exception);
        }
    }

    /**
     * @param string $to
     * @param RocketChatMessage $message
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function sendMessage(string $to, RocketChatMessage $message): void
    {
        $this->rocketChat->sendMessage($to, $message->toArray());
    }
}
