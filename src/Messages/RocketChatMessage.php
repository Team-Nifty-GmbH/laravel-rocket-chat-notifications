<?php

declare(strict_types=1);

namespace TeamNiftyGmbh\RocketChatNotifications\Messages;

class RocketChatMessage
{
    /** @var string|null RocketChat channel id. */
    protected ?string $channel = null;

    /** @var string|null A user or app access token. */
    protected ?string $from = null;

    /** @var string|null A RocketChat user id. */
    protected ?string $userId = null;

    /** @var string The text content of the message. */
    protected string $content;

    /** @var string|null The alias name of the message. */
    protected ?string $alias;

    /** @var string|null The avatar image of the message. */
    protected ?string $avatar;

    /** @var RocketChatAttachment[] Attachments of the message. */
    protected array $attachments = [];

    /**
     * Create a new instance of RocketChatMessage.
     *
     * @param  string $content
     * @return static
     */
    public static function create(string $content = ''): self
    {
        return new static($content);
    }

    /**
     * Create a new instance of RocketChatMessage.
     *
     * @param string $content
     */
    public function __construct(string $content = '')
    {
        $this->content($content);
    }

    public function getChannel(): ?string
    {
        return $this->channel;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * Set the sender's access token and user id.
     *
     * @param string $accessToken
     * @param string $userId
     * @return $this
     */
    public function from(string $accessToken, string $userId): self
    {
        $this->from = $accessToken;
        $this->userId = $userId;

        return $this;
    }

    /**
     * Set the RocketChat channel the message should be sent to.
     *
     * @param  string $channel
     * @return $this
     */
    public function to(string $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Set the sender's alias.
     *
     * @param  string $alias
     * @return $this
     */
    public function alias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Set the sender's avatar.
     *
     * @param  string $avatar
     * @return $this
     */
    public function avatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Set the content of the RocketChat message.
     * Supports GitHub flavoured markdown.
     *
     * @param  string  $content
     * @return $this
     */
    public function content(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Add an attachment to the message.
     *
     * @param array|RocketChatAttachment $attachment
     * @return $this
     */
    public function attachment(RocketChatAttachment|array $attachment): self
    {
        if (! ($attachment instanceof RocketChatAttachment)) {
            $attachment = new RocketChatAttachment($attachment);
        }

        $this->attachments[] = $attachment;

        return $this;
    }

    /**
     * Add multiple attachments to the message.
     *
     * @param array|RocketChatAttachment[] $attachments
     * @return $this
     */
    public function attachments(array $attachments): self
    {
        foreach ($attachments as $attachment) {
            $this->attachment($attachment);
        }

        return $this;
    }

    /**
     * Get an array representation of the RocketChatMessage.
     *
     * @return array
     */
    public function toArray(): array
    {
        $attachments = [];

        foreach ($this->attachments as $attachment) {
            $attachments[] = $attachment->toArray();
        }

        return array_filter([
            'text' => $this->content,
            'channel' => $this->channel,
            'alias' => $this->alias ?? null,
            'avatar' => $this->avatar ?? null,
            'attachments' => $attachments,
        ]);
    }
}
