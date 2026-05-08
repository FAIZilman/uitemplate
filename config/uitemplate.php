<?php

return [
    'button' => [
        'type' => 'file',
        'stub' => 'button',
    ],

    'card' => [
        'type' => 'folder',
        'stub' => 'card',
        'children' => ['header', 'content', 'footer', 'image'],
        'default_children' => ['content'],
    ],

];