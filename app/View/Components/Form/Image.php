<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Image extends Component
{
    public string $label;
    public string $name;
    public bool $multiple;
    public $existing;
    public string $deletedMedia;

    /**
     * Create a new component instance.
     */
    public function __construct(string $label = '', string $name, bool $multiple = false, $existing = [], $deletedMedia = 'deleted_media')
    {
        $this->deletedMedia = $deletedMedia;
        $this->existing = $existing;
        $this->multiple = $multiple;
        $this->name = $name;
        $this->label = $label;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.image');
    }
}
