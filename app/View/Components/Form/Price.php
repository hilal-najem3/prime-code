<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class Price extends Component
{
    public function __construct(
        public string $name = 'price',
        public bool $required = false,
        public ?string $label = null,
        public Collection $currencies
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.form.price');
    }
}
