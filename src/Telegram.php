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

    private function request(string $message): string
    {
        if ($this->getChatId()) {
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

        Logger::error('Input doesn\'t contains chat info: ' . var_export($this->input, true));

        return '';
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
        Logger::info('ChatId1: ' . ($this->input['chat']['id'] ?? ''));
        Logger::info('ChatId1: ' . ($this->input['my_chat_member']['chat']['id'] ?? ''));
        return $this->input['chat']['id'] ?? ($this->input['my_chat_member']['chat']['id'] ?? '');
    }
}