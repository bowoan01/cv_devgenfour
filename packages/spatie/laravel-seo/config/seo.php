<?php

return [
    'default_title' => env('SEO_DEFAULT_TITLE', env('APP_NAME', 'Devgenfour')),
    'default_description' => env('SEO_DEFAULT_DESCRIPTION', 'Devgenfour builds digital products that empower businesses.'),
    'default_keywords' => env('SEO_DEFAULT_KEYWORDS', 'software development, UI/UX, Devgenfour'),
    'canonical' => env('APP_URL', 'http://localhost'),
];
