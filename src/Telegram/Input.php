<?php

namespace Atelier\Telegram;

class Input
{
    private mixed $input;

    public function __construct()
    {
        $this->input = json_decode(file_get_contents("php://input"), true, 512, JSON_THROW_ON_ERROR);
    }

    public function getChatTd(): string
    {
        return $this->input['message']['chat']['id'];
    }

    public function getMessage(): string
    {
        return $this->input['message']['text'];
    }

    public function getUser(): array
    {
        $this->input['message'];
    }

    public function getInput(): array
    {
        return $this->input;
    }
}