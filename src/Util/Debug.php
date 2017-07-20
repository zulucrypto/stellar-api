<?php


namespace ZuluCrypto\StellarSdk\Util;


class Debug
{
    /**
     * Returns a printable hex string of the binary $data
     *
     * Adapted from: https://stackoverflow.com/a/4225813/2056308
     *
     * - Modified to return a string instead of echo it
     *
     * @param        $data
     * @param string $newline
     * @return string
     */
    public static function hexDump($data, $newline = "\n")
    {
        $output = '';
        static $from = '';
        static $to = '';

        static $width = 16; # number of bytes per line

        static $pad = '.'; # padding for non-visible characters

        if ($from==='')
        {
            for ($i=0; $i<=0xFF; $i++)
            {
                $from .= chr($i);
                $to .= ($i >= 0x20 && $i <= 0x7E) ? chr($i) : $pad;
            }
        }

        $hex = str_split(bin2hex($data), $width*2);
        $chars = str_split(strtr($data, $from, $to), $width);

        $offset = 0;
        foreach ($hex as $i => $line)
        {
            $output .= sprintf('%6X',$offset).' : '.implode(' ', str_split($line,2)) . ' [' . $chars[$i] . ']' . $newline;
            $offset += $width;
        }

        return $output;
    }
}