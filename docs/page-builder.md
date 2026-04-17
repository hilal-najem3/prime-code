# Page Builder System

## Overview

The Page Builder system allows dynamic rendering of website pages using structured content blocks stored in the database.

Each page contains a `content` JSON field that defines the layout and components of the page.

---

## Block Structure

Each block follows this structure:

```json
{
  "type": "string",
  "variant": "string",
  "settings": {},
  "data": {}
}
```

---

## Properties

### type

Defines the component type.

Examples:

- hero
- features
- contact

---

### variant

Defines the design variation of the block.

Examples:

- default
- video
- slider
- grid
- card

---

### settings

Defines layout and styling behavior.

Examples:

```json
{
  "height": "full",
  "overlay": true,
  "columns": 3
}
```

---

### data

Contains the actual content of the block.

Supports multilingual values:

```json
{
  "title": {
    "en": "Welcome",
    "ar": "Welcome"
  }
}
```

---

## Localization

- UI text → Laravel lang files (`__()`)
- Content → JSON fields per language

Always use helper:

```php
trans_field($field)
```

---

## Rendering

Blocks are rendered using Blade:

```blade
@foreach ($page->content as $block)

    @php
        $view = 'themes.default.blocks.'
            . $block['type']
            . '.'
            . ($block['variant'] ?? 'default');
    @endphp

    @includeIf($view, [
        'data' => $block['data'],
        'settings' => $block['settings'] ?? [],
        'lang' => $lang
    ])

@endforeach
```

---

## File Structure

```
resources/themes/default/blocks/

  hero/
    default.blade.php
    video.blade.php
    slider.blade.php

  features/
    grid.blade.php
    card.blade.php
```

---

## Rules

- Blade = presentation only
- No business logic in Blade
- Content must be validated in Request
- Structure enforced in Service
- Always support multilingual fields
- Use variants instead of creating new types

---

## Best Practices

- type = component
- variant = design
- settings = layout
- data = content

Avoid:

- hardcoded text
- mixing logic in Blade
- creating too many block types

---

## Example

```json
{
  "type": "hero",
  "variant": "video",
  "settings": {
    "overlay": true
  },
  "data": {
    "title": {
      "en": "Welcome"
    }
  }
}
```
