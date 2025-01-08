<?php

namespace TidioLiveChat\Clock;

if (!defined('WPINC')) {
    die('File loaded directly. Exiting.');
}

use DateTimeImmutable;

class Clock
{
    /**
     * @return DateTimeImmutable
     */
    public function getCurrentTimestamp()
    {
        return new DateTimeImmutable('now');
    }
}
