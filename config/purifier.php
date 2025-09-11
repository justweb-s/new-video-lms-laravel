<?php

return [
    'encoding' => 'UTF-8',
    'finalize' => true,
    'cachePath' => storage_path('framework/cache/purifier'),
    'cacheFileMode' => 0755,

    'settings' => [
        'default' => [
            // Conservative defaults
            'HTML.Doctype' => 'HTML 4.01 Transitional',
            'HTML.Allowed' => 'div[class],p,strong,br,h1,h2,span[class],ul,ol,li',
            'AutoFormat.AutoParagraph' => false,
            'AutoFormat.RemoveEmpty' => true,
        ],

        // Profile used for Workout Card manual content sanitization
        'workout' => [
            // Allow only tags/attributes used by the builder output
            'HTML.Doctype' => 'HTML 4.01 Transitional',
            'HTML.Allowed' => 'div[class],section[class],table,thead,tbody,tr,th,td[class|data-label],p,strong,br,h1,h2,img[src|alt|class]',
            // Disallow inline styles by default
            'CSS.AllowedProperties' => '',
            'AutoFormat.AutoParagraph' => false,
            'AutoFormat.RemoveEmpty' => true,
        ],
    ],
];
