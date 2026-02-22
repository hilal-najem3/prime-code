<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;
use Illuminate\Support\Collection;

class TranslatableInputTable extends Component
{
    public string $label;
    public string $name;
    public Collection $languages;
    public bool $required;
    public array $value;
    public string $type;

    public function __construct(
        string $label = '',
        string $name,
        Collection $languages,
        bool $required = false,
        array $value = [],
        string $type = 'text'
    ) {
        $this->label = $label;
        $this->name = $name;
        $this->languages = $languages;
        $this->required = $required;
        $this->value = $value;
        $this->type = $type;
    }

    public function render()
    {
        return view('components.form.translatable-input-table');
    }
}