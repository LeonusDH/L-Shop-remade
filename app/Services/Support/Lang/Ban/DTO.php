<?php
declare(strict_types = 1);

namespace app\Services\Support\Lang\Ban;

/**
 * Class DTO
 * Data transfer object for {@see BanMessage}
 */
class DTO
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string[]
     */
    private $messages = [];

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    public function setMessages(array $messages): DTO
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
