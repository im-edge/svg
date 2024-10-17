<?php

namespace IMEdge\Svg;

class SvgString
{
    public static function svgFromDataString(string $string): string
    {
        return self::decode(substr($string, strpos($string, ',') + 1));
    }

    protected static function decode(string $svg): string
    {
        return str_replace([
            '',
            '',
            '%27', // urlencode("'"),
            "'",
            '%3C', // urlencode('<'),
            '%3E', // urlencode('>'),
            '%23', // urlencode('#'),
        ], [
            "\r",
            "\n",
            "'",
            '"',
            '<',
            '>',
            '#',
        ], $svg);
    }
}
