<?php

namespace Atelier\Telegram;

use Atelier\Telegram\Update\MyChatMember;

class Update
{
    private mixed $input;

    public function __construct()
    {
        $this->input = json_decode(file_get_contents("php://input"), true, 512, JSON_THROW_ON_ERROR);
        if (isset($this->input['my_chat_member'])) {
            $this->myChatMember = new MyChatMember($this->input['my_chat_member']);
        } elseif (isset($this->input['message'])) {

        }
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