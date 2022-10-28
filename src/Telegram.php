<?php

namespace Atelier;

class Telegram
{
    private array $input;

    public function __construct(private string $token)
    {
        $this->input = json_decode(file_get_contents("php://input"), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @return array
     */
    public function getInput(): array
    {
        return $this->input;
    }

    public function sendMessage(string $message): mixed
    {
        return json_decode($this->request($message), true, 512, JSON_THROW_ON_ERROR);
    }

    private function getChatTd(): string
    {
        return $this->input["message"]["chat"]["id"];
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
            'chat_id' => $this->getChatTd()
        ]));


        return curl_exec($ch);
    }
}