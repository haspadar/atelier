<?php

namespace Atelier;

class FlashMessage
{
    public function __construct(private readonly string $message, private readonly FlashMessageType $type)
    {
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return FlashMessageType
     */
    public function getType(): FlashMessageType
    {
        return $this->type;
    }

    public function __serialize(): array
    {
        return ['message' => $this->message, 'type' => $this->type->name];
    }

    public function __unserialize(array $data): void
    {
        $this->message = $data['message'];
        $this->type = FlashMessageType::fromName($data['type']);
    }
}