<?php

namespace Atelier;

use Atelier\Telegram\Update;

class Telegram
{
    private array $update;

    public function __construct(private string $token)
    {
        $this->input = json_decode(file_get_contents("php://input"), true, 512, JSON_THROW_ON_ERROR);
        Logger::info('Debug: ' . var_export($this->input, true));
        Logger::info('Data: ' . var_export($this->input['data'], true));
//        array (   'update_id' => 278445189,   'callback_query' =>
//            array (     'id' => '690781338248020581',
//                'from' => array (       'id' => 160835063,       'is_bot' => false,       'first_name' => 'Костя',       'username' => 'haspadar_III',       'language_code' => 'ru',),
//                'message' =>      array (       'message_id' => 137,       'from' =>        array (
//                    'id' => 5734498019,         'is_bot' => true,         'first_name' => 'atelier',         'username' => 'atelier_palto_bot',
//                    ),
//                    'chat' =>        array (         'id' => 160835063,         'first_name' => 'Костя',         'username' => 'haspadar_III',         'type' => 'private',       ),
//                    'date' => 1667007821,       'text' => 'Привет, Костя. Какие уведомления хочешь получать?',
//                    'reply_markup' =>        array (         'inline_keyboard' =>          array (
//                        0 =>            array (             0 =>              array (               'text' => 'Только важные',               'callback_data' => 'CRITICAL',             ),
//                        1 =>              array (               'text' => 'Предупреждения',               'callback_data' => 'WARNING',
//                            ),             2 =>              array (               'text' => 'Рекомендации',
//                                'callback_data' => 'INFO',             ),           ),         ),       ),
//                    ),     'chat_instance' => '5758169490136786947',     'data' => 'CRITICAL',   ), )
    }

    public function getUpdate(): array
    {
        return $this->update;
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
        return $this->input['data'] ?? '';
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