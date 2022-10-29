<?php

namespace Atelier;

use Atelier\Telegram\Input;

class Telegram
{
    private Input $input;

    public function __construct(private string $token)
    {
        $this->input = new Input();
//        $this->addChat();
    }

    public function getInput(): Input
    {
        return $this->input;
    }

    public function sendMessage(string $message): mixed
    {
        return json_decode($this->request($message), true, 512, JSON_THROW_ON_ERROR);
    }

    private function request(string $message)
    {
        $token = $this->token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot$token/sendMessage");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'text' => $message,
            'chat_id' => $this->input->getChatTd()
        ]));


        return curl_exec($ch);
    }

    private function addChat()
    {
        $user = $this->input->getUser();
    }
}