<?php

namespace Atelier;

class Telegram
{
    private array $input;

    public function __construct(private string $token)
    {
        $this->input = json_decode(file_get_contents("php://input"), true, 512, JSON_THROW_ON_ERROR);
    }

    public function sendMessageWithBaseButtons(string $message, array $keyboard): array
    {
        return $this->request($message, [
            'reply_markup' => json_encode([
                "keyboard" => $keyboard,
                "resize_keyboard" => true
            ])
        ]);
    }

    public function sendMessageWithInlineButtons(string $message, array $buttons): array
    {
        return $this->request($message, [
            'reply_markup' => json_encode(['inline_keyboard' => [$buttons]])
        ]);
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
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array_merge_recursive([
                'text' => $message,
                'chat_id' => $this->getChatId()
            ], $params)));
            $response = curl_exec($ch);

            return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        }

        Logger::error('Input doesn\'t contains chat info: ' . var_export($this->input, true));

        return [];
    }

    public function getClickedInlineButton(): string
    {
        return $this->input['callback_query']['data'] ?? '';
    }

    public function isMessage(): bool
    {
        return isset($this->input['message']);
    }

    public function getFromFirstName(): string
    {
        return $this->input['message']['from']['first_name'];
    }

    public function getUsername(): string
    {
        $user = $this->getUser();

        return $user['username'];
    }

    public function getFirstName(): string
    {
        $user = $this->getUser();

        return $user['first_name'];
    }

    public function getChatId(): string
    {
        return $this->input['message']['chat']['id']
            ?? ($this->input['my_chat_member']['chat']['id']
                ?? ($this->input['callback_query']['message']['chat']['id']
                    ?? ''
                )
            );
    }

    private function getUser(): array
    {
        return $this->input['callback_query']['from'] ?? [];
    }
}