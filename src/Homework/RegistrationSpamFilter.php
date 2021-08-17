<?php

namespace App\Homework;

class RegistrationSpamFilter
{
    const WHITE_LIST = [
        '.ru',
        '.com',
        '.org'
    ];

    /**
     * Фильтрует email по доменной зоне
     * @param string $email
     * @return bool
     */
    public function filter(string $email): bool
    {
        foreach (self::WHITE_LIST as $item) {
            $item = '\\' . $item;
            if (preg_match("/$item$/", $email)) {
                return false;
            }
        }
        return true;
    }
}
