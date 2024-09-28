<?php

return [
    'use_database' => true,
    'scan_directories' => [
        app_path(),
        resource_path(),
    ],
    'file_extensions' => ['php', 'js', 'ts', 'vue'],
];