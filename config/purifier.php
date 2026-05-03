<?php

return [
    'encoding'      => 'UTF-8',
    'finalize'      => true,
    'cachePath'     => storage_path('app/purifier'),
    'cacheFileMode' => 0755,

    'settings'      => [
        'default' => [
            'HTML.Doctype'             => 'HTML 4.01 Transitional',
            'HTML.Allowed'             => 'h1,h2,h3,h4,h5,h6,p,br,strong,b,em,i,u,ul,ol,li,a[href|target|rel],table[class],thead,tbody,tfoot,tr,th[scope],td,caption,blockquote,sup,sub,span[style],div,img[src|alt|width|height|style],hr,pre,code,dl,dt,dd',
            'CSS.AllowedProperties'    => 'font-weight,font-style,text-decoration,text-align,margin,padding,color,background-color,width,height,max-width',
            'AutoFormat.AutoParagraph' => false,
            'AutoFormat.RemoveEmpty'   => false,
            'HTML.TargetBlank'         => true,
            'HTML.Nofollow'            => true,
            'URI.AllowedSchemes'       => ['http' => true, 'https' => true, 'mailto' => true],
        ],

        // Strict mode: no HTML allowed (for plain-text contexts)
        'strict' => [
            'HTML.Allowed' => '',
        ],
    ],
];
