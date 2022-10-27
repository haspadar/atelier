<?php

namespace Atelier;

use Atelier\Message\Type;
use Atelier\Model\HttpInfo;
use Atelier\Model\NginxTraffic;
use Atelier\Model\Parser;
use Atelier\Model\PhpFpmTraffic;
use Atelier\Model\Rotator;
use Cassandra\Date;
use DateTime;
use Palto\Email;

class Notifications
{
    public static function generate(): void
    {
        $contacts = array_map(
            fn(string $email) => trim($email),
            explode(',', Settings::getByName('emails'))
        );
        foreach ($contacts as $contact) {
            $messages = (new Model\Notifications())->getActualMessages(
                $contact,
                (new \DateTime())->modify('-7 DAYS')->format('Y-m-d H:i:s')
            );
            if ($messages) {
                foreach (self::groupByType($messages) as $type => $typeMessages) {
                    $subject = self::generateSummarySubject($type, $typeMessages);
                    $body = self::generateSummaryBody($type, $typeMessages);
                    \Atelier\Email::send($contact, $subject, $body);
                    $now = new DateTime();
                    foreach ($typeMessages as $typeMessage) {
                        (new Model\Notifications())->add([
                            'message_id' => $typeMessage['id'],
                            'contact' => $contact,
                            'create_time' => $now->format('Y-m-d H:i:s')
                        ]);
                    }

                    Logger::info('Sent ' . $type . ' email to ' . $contact);
                }
            } else {
                Logger::debug('No actual messages for ' . $contact);
            }

        }

        if (!$contacts) {
            Logger::warning('No emails in config');
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
                . Plural::get(
                    count($messages),
                    'уведомление',
                    'уведомления',
                    'уведомлений'
                );
        }

        return count($messages)
            . Plural::get(
                count($messages),
                'рекомендация',
                'рекомендации',
                'рекомендаций'
            );
    }

    private static function generateSummaryBody(string $type, array $messages): string
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