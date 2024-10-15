<?php

namespace IMEdge\Svg;

use gipfl\IcingaWeb2\Url;
use ipl\Html\Html;
use ipl\Html\ValidHtml;

class SvgUtils
{
    public static function sendSvg(ValidHtml $svg)
    {
        header('Content-Type: image/svg+xml');
        $header = '<?xml version="1.0"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN"
    "http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd">
';
        echo $header;
        echo $svg->render();
        exit; // TODO: clean shutdown
    }

    public static function makePoints($points)
    {
        $result = [];
        foreach ($points as $point) {
            $result[] = static::float($point[0]) . ',' . static::float($point[1]);
        }

        return implode(' ', $result);
    }

    public static function adjustPoints($points, $offsetX = 0, $offsetY = 0)
    {
        foreach ($points as & $pair) {
            if ($offsetX !== 0) {
                $pair[0] += $offsetX;
            }
            if ($offsetY !== 0) {
                $pair[1] += $offsetY;
            }
        }

        return $points;
    }

    public static function rectangle($width, $height, $attributes = [])
    {
        $attributes['width'] = static::float($width);
        $attributes['height'] = static::float($height);

        return Html::tag('rect', $attributes)->setVoid();
    }

    public static function polygon($points, $attributes = [])
    {
        $attributes['points'] = SvgUtils::makePoints($points);
        return Html::tag('polygon', $attributes)->setVoid();
    }

    public static function float($number)
    {
        if ($number === null) {
            return null;
        } else {
            return preg_replace(
                '/(?:0+|\.0+)$/',
                '',
                number_format(round($number, 6, PHP_ROUND_HALF_UP), 6, '.', '')
            );
        }
    }

    public static function circle($x, $y, $radius, $attributes = [])
    {
        $attributes['cx'] = static::float($x);
        $attributes['cy'] = static::float($y);
        $attributes['r'] = static::float($radius);

        return Html::tag('circle', $attributes)->setVoid();
    }

    public static function circlePathDefinition($x, $y, $radius): string
    {
        return sprintf(
            'M (%1$s - %3$s), %2$s'
            . 'a %3$s,%3$s 0 1,0 %4$s,0'
            . 'a %3$s,%3$s 0 1,0 -%5$s,0',
            SvgUtils::float($x),
            SvgUtils::float($y),
            SvgUtils::float($radius),
            SvgUtils::float($radius * 2),
            SvgUtils::float(-($radius * 2))
        );
    }

    protected static function wantLocalUrl($url)
    {
        if ($url instanceof Url) {
            return $url;
        } else {
            return Url::fromPath($url);
        }
    }

    public static function linkLocalImage($url, $x, $y, $width = null, $height = null)
    {
        $url = static::wantLocalUrl($url);

        return Html::tag('image', [
            'x'          => SvgUtils::float($x),
            'y'          => SvgUtils::float($y),
            'width'      => $width,
            'height'     => $height,
            'xlink:href' => $url,
        ]);
    }

    public static function linkZoomedLocalImage($url, $x, $y, $zoom)
    {
        $url = static::wantLocalUrl($url);

        return Html::tag('image', [
            'x'         => SvgUtils::float($x / $zoom),
            'y'         => SvgUtils::float($y / $zoom),
            'transform' => sprintf(
                'scale(%s, %s)',
                SvgUtils::float($zoom),
                SvgUtils::float($zoom)
            ),
            'xlink:href' => $url,
        ]);
    }

    public static function linkZoomedLocalImage2($url, $x, $y, $width, $height)
    {
        $url = static::wantLocalUrl($url);

        return Html::tag('image', [
            'x'     => SvgUtils::float($x),
            'y'     => SvgUtils::float($y),
            'width' => SvgUtils::float($width),
            'height' => SvgUtils::float($height),
            'xlink:href' => $url,
        ]);
    }

    public static function createSvg2($attributes)
    {
        return Html::tag('svg', [
            'xmlns'  => 'http://www.w3.org/2000/svg',
            'xmlns:xlink' => 'http://www.w3.org/1999/xlink',
        ] + $attributes);
    }

    public static function createSvg($width, $height)
    {
        return Html::tag('svg', [
            'xmlns'  => 'http://www.w3.org/2000/svg',
            'width'  => strpos($width, '%') === false ? SvgUtils::float($width) : $width,
            'height' => static::float($height),
            'xmlns:xlink' => 'http://www.w3.org/1999/xlink',
        ]);
    }
}
