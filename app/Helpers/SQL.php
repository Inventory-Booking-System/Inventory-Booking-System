<?php

namespace App\Helpers;

class SQL
{
    public static function escapeLikeString($string) {
        /**
         * Backslashes are an escape character in SQL, so 
         * we must escape them to match them literally
         * 
         * https://dev.mysql.com/doc/refman/8.0/en/string-comparison-functions.html#:~:text=\\\\
         */
        $string = str_replace('\\', '\\\\', $string);

        /**
         * `%` and `_` are wildcard characters in SQL, so escape
         * them to match them literally
         */
        $string = str_replace('%', '\%', $string);
        $string = str_replace('_', '\_', $string);

        return $string;
    }
}