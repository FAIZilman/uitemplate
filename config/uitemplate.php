<?php

return [

    'button' => [
        'type' => 'file',
        'stub' => 'button.blade.php',
    ],

    'card' => [
        'type' => 'folder',
        'stub' => 'card',
        'children' => ['header', 'content', 'footer', 'image'],
        'default_children' => ['content'],
    ],

];