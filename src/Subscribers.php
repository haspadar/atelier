<?php

namespace Atelier;

class Subscribers
{
    public static function add(array $subscriber)
    {
        $subscriber['create_time'] = (new \DateTime())->format('Y-m-d H:i:s');
        (new \Atelier\Model\Subscribers())->add($subscriber);
    }
}