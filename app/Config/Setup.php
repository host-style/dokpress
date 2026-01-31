<?php

namespace App\Config;

class Setup
{
    public static function CONTENT_SECURITY_POLICY(): string
    {
        $data = [
            'default-src' => [
                "'self'",
            ],
            'script-src' => [
                "'self'",
                'https://cdn.jsdelivr.net',
            ],
            'style-src' => [
                "'self'",
                'https://fonts.googleapis.com',
            ],
            'font-src' => [
                "'self'",
                'https://fonts.gstatic.com',
            ],
            'img-src' => [
                "'self'",
                'data:',
            ],
        ];

        $csp = '';
        foreach($data as $k => $v) {
            $csp .= $k . ' ' . implode(' ', $v) . '; ';
        }

        return  $csp;
    }
}
