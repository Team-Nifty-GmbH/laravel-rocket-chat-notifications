# laravel-rocket-chat-notifications

## Introduction

This package makes it easy to send notifications using [RocketChat](https://rocket.chat/) with Laravel 9.0+.

## Contents

- [Installation](#installation)
    - [Setting up the RocketChat service](#setting-up-the-rocketchat-service)
- [Usage](#usage)
    - [Available Message methods](#available-message-methods)
    - [Adding Attachment](#adding-attachment)
    - [Available Attachment methods](#available-attachment-methods)
    - [Sending messages without notification](#sending-messages-without-a-notification)
    - [Sending messages via connection](#sending-messages-via-connection)
- [Credits](#credits)
- [License](#license)

## Installation

You can install the package via composer:

```shell script
$ composer require team-nifty-gmbh/laravel-rocket-chat-notifications
```

### Setting up the RocketChat service

In order to send message to RocketChat channels, you need to create a bot user with an access token in your RocketChat Application

You can publish the config file with:
```shell script
$ php artisan vendor:publish --provider="TeamNiftyGmbh\RocketChatNotifications\RocketChatNotificationsServiceProvider"
```

The config file looks as follows:
```php
// config/rocket-chat.php
...
    /*
    |--------------------------------------------------------------------------
    | Default RocketChat Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which RocketChat connections below you wish
    | to use as your default connection for all RocketChat notifications. Of course
    | you may use many connections at once using the RocketChat static methods or 
    | RocketChatMessage connection method.
    |
    */

    'default' => env('ROCKETCHAT_CONNECTION', 'rocket-chat'),

    /*
    |--------------------------------------------------------------------------
    | RocketChat Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the RocketChat connections setup for your application.
    |
    */

    'connections' => [

        'rocket-chat' => [
            // Base URL for RocketChat API server (https://your.rocketchat.server.com)
            'url' => env('ROCKETCHAT_URL'),
            'token' => env('ROCKETCHAT_TOKEN'),
            'user_id' => env('ROCKETCHAT_USER_ID')
        ]

    ]
...
```

If you have published the config file, you can add your RocketChat API server's base url, access token and user Id to `config/rocket-chat.php`.
You can also create additional connections if you wish to serve multiple RocketChats in your application.

## Usage

You can use the channel in your `via()` method inside the notification:

```php
use Illuminate\Notifications\Notification;
use TeamNiftyGmbh\RocketChatNotifications\Channels\RocketChatNotificationChannel;
use TeamNiftyGmbh\RocketChatNotifications\Messages\RocketChatMessage;

class TaskCompleted extends Notification
{
    public function via(mixed $notifiable): array
    {
        return [
            RocketChatNotificationChannel::class
        ];
    }

    public function toRocketChat($notifiable): RocketChatMessage
    {
        return RocketChatMessage::create('Test Message');
    }
}
```

In order to let your notification know which RocketChat channel you are targeting, add the `routeNotificationForRocketChat` method to your Notifiable model:

```php
public function routeNotificationForRocketChat(): string
{
    return 'channel_name';
}
```

### Available Message methods

`connection()`: Sets the connection.

`domain()`: Sets the domain of your rocket chat server.

`from()`: Sets the sender's access token and user id.

`to()`: Specifies the channel id to send the notification to (overridden by `routeNotificationForRocketChat` if empty).

`content()`: Sets a content of the notification message. Supports Github flavoured markdown.

`alias()`:  This will cause the message’s name to appear as the given alias, but your username will still display.

`avatar()`: This will make the avatar use the provided image url.

`attachment()`: This will add a single attachment.

`attachments()`: This will add multiple attachments.

`clearAttachments()`: This will remove all attachments.

### Adding Attachment

There are several ways to add one or more attachments to a message

```php
public function toRocketChat($notifiable)
{
    return RocketChatMessage::create('Test message')
        ->connection('rocket-chat') // optional to alter the default connection, overrides 'domain', 'to' and 'from'
        ->domain('https://your.rocketchat.server.com') //optional if set in config
        ->to('channel_name') // optional if set in config
        ->from('access_token', 'rocket_chat_user_id') // optional if set in config
        ->attachments([
            RocketChatAttachment::create()->imageUrl('test'),
            RocketChatAttachment::create(['image_url' => 'test']),
            new RocketChatAttachment(['image_url' => 'test']),
            [
                'image_url' => 'test'
            ]   
        ]);   
}
```

### Available Attachment methods

`color()`: The color you want the order on the left side to be, any value background-css supports.

`text()`: The text to display for this attachment, it is different than the message’s text.

`timestamp()`: Displays the time next to the text portion. ISO8601 Zulu Date or instance of any `\DateTime`

`thumbnailUrl()`: An image that displays to the left of the text, looks better when this is relatively small.

`messageLink()`: Only applicable if the ts is provided, as it makes the time clickable to this link.

`collapsed()`: Causes the image, audio, and video sections to be hiding when collapsed is true.

`author($name, $link, $icon)`: shortcut for author methods

`authorName()`: Name of the author.

`authorLink()`: Providing this makes the author name clickable and points to this link.

`authorIcon()`: Displays a tiny icon to the left of the Author’s name.

`title()`: Title to display for this attachment, displays under the author.

`titleLink()`: Providing this makes the title clickable, pointing to this link.

`titleLinkDownload()`: When this is true, a download icon appears and clicking this saves the link to file.

`imageUrl()`: The image to display, will be “big” and easy to see.

`audioUrl()`: Audio file to play, only supports what html audio does.

`videoUrl()`: Video file to play, only supports what html video does.

`fields()`: An array of Attachment Field Objects.

```php
[
    [
        'short' => false, // Whether this field should be a short field. Default: false
        'title' => 'Title 1', //The title of this field. Required
        'value' => 'Value 1' // The value of this field, displayed underneath the title value. Required
    ],
    [
        'short' => true,
        'title' => 'Title 2',
        'value' => 'Value 2'
    ],

];   
```

### Sending messages without a notification
```php
use TeamNiftyGmbh\RocketChatNotifications\Messages\RocketChatMessage;
use TeamNiftyGmbh\RocketChatNotifications\RocketChat

RocketChat::send('domain', 'token', 'userId', 'channel', new RocketChatMessage('message'));
```

### Sending messages via connection 
connection must be defined in `config/rocket-chat.php`

```php
use TeamNiftyGmbh\RocketChatNotifications\Messages\RocketChatMessage;
use TeamNiftyGmbh\RocketChatNotifications\RocketChat

RocketChat::sendVia('connection', 'channel', new RocketChatMessage('message'));
```

## Credits

- [Steffen Franz]
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
