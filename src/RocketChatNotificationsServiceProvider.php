<?php

declare(strict_types=1);

namespace TeamNiftyGmbh\RocketChatNotifications;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use TeamNiftyGmbh\RocketChatNotifications\Channels\RocketChatNotificationChannel;

class RocketChatNotificationsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app
            ->when(RocketChatNotificationChannel::class)
            ->needs(RocketChat::class)
            ->give(function () {
                return new RocketChat(
                    new HttpClient(),
                    Config::get('services.rocketchat.url'),
                    Config::get('services.rocketchat.token'),
                    Config::get('services.rocketchat.user_id')
                );
            });
    }
}
