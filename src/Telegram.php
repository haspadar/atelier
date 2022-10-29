<?php

namespace Atelier;

use Atelier\Telegram\Update;

class Telegram
{
    private array $update;

    public function __construct(private string $token)
    {
        $this->input = json_decode(file_get_contents("php://input"), true, 512, JSON_THROW_ON_ERROR);
    }

    public function getUpdate(): array
    {
        return $this->update;
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
            'chat_id' => $this->getChatId()
        ]));


        return curl_exec($ch);
    }

    public function isMessage(): bool
    {
        return isset($this->input['message']);
    }

    private function addChat()
    {
        $user = $this->update->getUser();
    }

    private function getChatId(): string
    {
        return $this->input['chat']['id'];
    }
}