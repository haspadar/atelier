<?php

namespace Atelier;

use Atelier\Check\Type;
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
                        $message = self::generateMessage($type, $typeMessages);
                        Logger::info('Message ' . $message);
                        $response = $telegram->sendMessage($message, $subscriber['chat_id']);
                        Logger::info('Response: ' . var_export($response, true));
                        if ($response['ok']) {
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
                            Logger::error($response['description']);
                        }
                    } else {
                        Logger::warning('Ignored ' . $type . ' type for ' . $subscriber['first_name']);
                    }

                }
            } else {
                Logger::debug('No actual messages for ' . $subscriber['first_name']);
            }

        }
    }

    private static function generateMessage(string $type, array $messages): string
    {
        $subject = self::generateSummarySubject($type, $messages);
        $url = sprintf('<a href="%s">Перейти на сайт</a>', self::generateUrl($type));
        $groupNames = self::getGroupNames($messages);
        $list = implode('', array_map(
            fn($name) => '<li>' . $name . '</li>',
            array_slice($groupNames, 0, 5)
        ));

        return $subject . ':<br><ul>' . $list . (count($groupNames) > 5 ? '<li>и др.</li>' : '') . '</ul>' . $url;
    }

    private static function getGroupNames(array $messages): array
    {
        return array_unique(array_column($messages, 'group_title'));
    }

    private static function generateUrl(string $type): string
    {
        if ($type == Type::CRITICAL->name) {
            return 'https://atelier.palto.name/checks#CRITICAL';
        }

        if ($type == Type::WARNING->name) {
            return 'https://atelier.palto.name/checks#WARNING';
        }

        if ($type == Type::INFO->name) {
            return 'https://atelier.palto.name/checks#INFO';
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