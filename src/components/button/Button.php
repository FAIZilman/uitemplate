<?php

namespace Uitemplate\laravel\View\Components\Ui;

use Illuminate\View\Component;

class Button extends Component
{
    public function __construct(
        public string $type = 'primary',
        public string $size = 'md',
        public bool $fullWidth = true,
    ) {
    }

    public function render()
    {
        return view('components.ui.button');
    }
}