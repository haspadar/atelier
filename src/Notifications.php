<?php

namespace Atelier;

use Atelier\Message\Type;
use DateTime;
use Longman\TelegramBot\DB;

class Notifications
{
    public static function generate(): void
    {
        $subscribers = Subscribers::getAll();
        $telegram = new Telegram();
        foreach ($subscribers as $subscriber) {
            $messages = (new Model\Notifications())->getActualMessages(
                $subscriber['chat_id'],
                (new \DateTime())->modify('-1 MONTH')->format('Y-m-d H:i:s')
            );
            if ($messages) {
                $types = explode(',', $subscriber['message_types']);
                foreach (self::groupByType($messages) as $type => $typeMessages) {
                    if (in_array($type, $types)) {
                        $subject = self::generateSummarySubject($type, $typeMessages);
                        $body = self::generateSummaryBody($typeMessages);
                        Logger::info('Subject ' . $subject);
                        Logger::info('Body ' . $body);
                        $telegram->sendMessage($subject . PHP_EOL . PHP_EOL . $body, $subscriber['chat_id']);
                        $now = new DateTime();
                        foreach ($typeMessages as $typeMessage) {
                            (new Model\Notifications())->add([
                                'message_id' => $typeMessage['id'],
                                'contact' => $subscriber['chat_id'],
                                'create_time' => $now->format('Y-m-d H:i:s')
                            ]);
                        }

                        Logger::info('Sent ' . $type . ' telegram to ' . $subscriber['first_name']);
                    } else {
                        Logger::warning('Ignored ' . $type . ' type for ' . $subscriber['first_name']);
                    }

                }
            } else {
                Logger::debug('No actual messages for ' . $subscriber['first_name']);
            }

        }
    }

    private static function generateSummarySubject(string $type, array $messages): string
    {
        if ($type == Type::CRITICAL->name) {
            return count($messages)
                . ' '
                . Plural::get(
                    count($messages),
                    'важное сообщение',
                    'важных сообщения',
                    'важных сообщений'
                );
        }

        if ($type == Type::WARNING) {
            return count($messages)
                . ' '
                . Plural::get(
                    count($messages),
                    'уведомление',
                    'уведомления',
                    'уведомлений'
                );
        }

        return count($messages)
            . ' '
            . Plural::get(
                count($messages),
                'рекомендация',
                'рекомендации',
                'рекомендаций'
            );
    }

    private static function generateSummaryBody(array $messages): string
    {
        $summary = '<ol>';
        foreach ($messages as $message) {
            $summary .= '<li>' . $message['title'] . '<br>' . $message['text'] . '</li>';
        }

        return $summary . '</ol>';
    }

    private static function groupByType(array $messages): array
    {
        $grouped = [];
        foreach ($messages as $message) {
            $grouped[$message['type']][] = $message;
        }

        return $grouped;
    }
}