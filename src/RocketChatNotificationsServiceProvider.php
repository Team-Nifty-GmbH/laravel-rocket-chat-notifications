<?php

declare(strict_types=1);

namespace TeamNiftyGmbh\RocketChatNotifications;

use GuzzleHttp\Client as HttpClient;
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
        $this->offerPublishing();

        $this->app
            ->when(RocketChatNotificationChannel::class)
            ->needs(RocketChat::class)
            ->give(function () {
                $connection = $this->app->config['rocket-chat.default'];

                return new RocketChat(
                    new HttpClient(),
                    $this->app->config['rocket-chat.connections.' . $connection . '.url'],
                    $this->app->config['rocket-chat.connections.' . $connection . '.token'],
                    $this->app->config['rocket-chat.connections.' . $connection . '.user_id'],
                );
            });
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/rocket-chat.php',
            'rocket-chat'
        );
    }

    protected function offerPublishing()
    {
        if (! function_exists('config_path')) {
            // function not available and 'publish' not relevant in Lumen
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/rocket-chat.php' => config_path('rocket-chat.php'),
        ], 'config');
    }
}
