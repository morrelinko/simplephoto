<?php

/*
 * This file is part of the SimplePhoto package.
 *
 * (c) Laju Morrison <morrelinko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimplePhoto\Toolbox;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class TextUtils
{
    /**
     * Checks if a string ends with a sequence of characters
     *
     * @param string $text
     * @param string $char
     *
     * @return bool
     */
    public static function endsWith($text, $char)
    {
        return substr($text, -(strlen($char))) == $char;
    }
}
