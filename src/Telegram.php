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

    public function sendMessageWithInlineButtons(string $message, array $buttons): array
    {
        return $this->request($message, ['reply_markup' => ['inline_keyboard' => $buttons]]);
    }

    public function sendMessage(string $message): array
    {
        return $this->request($message);
    }

    private function request(string $message, array $params = []): array
    {
        if ($this->getChatId()) {
            $token = $this->token;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot$token/sendMessage");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array_merge([
                'text' => $message,
                'chat_id' => $this->getChatId()
            ], $params)));


            return json_decode(curl_exec($ch), true, 512, JSON_THROW_ON_ERROR);
        }

        Logger::error('Input doesn\'t contains chat info: ' . var_export($this->input, true));

        return [];
    }

    public function getClickedInlineButton(): string
    {
        return isset($this->input['callback_query']['data']) ?? '';
    }

    public function isMessage(): bool
    {
        return isset($this->input['message']);
    }

    public function getFromFirstName(): string
    {
        return $this->input['message']['from']['first_name'];
    }

    private function getChatId(): string
    {
        return $this->input['message']['chat']['id'] ?? ($this->input['my_chat_member']['chat']['id'] ?? '');
    }
}