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
     * @throws CouldNotSendNotification
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        /** @var RocketChatMessage $message */
        $message = $notification->toRocketChat($notifiable);

        $domain = $message->getDomain() ?: $this->rocketChat->getDomain();
        if (!$domain) {
            throw CouldNotSendNotification::missingDomain();
        }

        $to = $message->getChannel() ?: $notifiable->routeNotificationFor('RocketChat');
        if ($to === null) {
            throw CouldNotSendNotification::missingTo();
        }

        $from = $message->getFrom() ?: $this->rocketChat->getToken();
        if (!$from) {
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
     */
    private function sendMessage(string $to, RocketChatMessage $message): void
    {
        $this->rocketChat->sendMessage($to, $message->toArray());
    }
}
