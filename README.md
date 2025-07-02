# Laravel Filament Menu manager

[![Novius CI](https://github.com/novius/filament-relation-nested/actions/workflows/main.yml/badge.svg?branch=main)](https://github.com/novius/filament-relation-nested/actions/workflows/main.yml)
[![Packagist Release](https://img.shields.io/packagist/v/novius/filament-relation-nested.svg?maxAge=1800&style=flat-square)](https://packagist.org/packages/novius/filament-relation-nested)
[![License: AGPL v3](https://img.shields.io/badge/License-AGPL%20v3-blue.svg)](http://www.gnu.org/licenses/agpl-3.0)

## Introduction

This [Laravel Filament](https://filamentphp.com/) package allows you to manage menus in your Laravel Filament admin panel.

## Requirements

* PHP >= 8.2
* Laravel Filament >= 3.3
* Laravel Framework >= 11.0 

## Installation

```sh
composer require novius/filament-relation-nested
```

Publish Filament assets

```sh
php artisan filament:assets
```

Then, launch migrations 

```sh
php artisan migrate
```

In your `AdminFilamentPanelProvider` add the `MenuManagerPlugin` :

```php
use Novius\FilamentRelationNested\Filament\MenuManagerPlugin;

class AdminFilamentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            // ...
            ->plugins([
                MenuManagerPlugin::make(),
            ])
            // ...
            ;
    }
}
```

### Configuration

Some options that you can override are available.

```sh
php artisan vendor:publish --provider="Novius\FilamentRelationNested\LaravelFilamentMenuServiceProvider" --tag="config"
```

## Usage

### Blade directive

You can display menu with : 

```bladehtml
<x-filament-relation-nested::menu 
    menu-slug="slug-of-menu" 
    locale="fr" {{-- optional, will use the current locale by default --}}   
/>
```

### Write your owned template

#### Template class

```php
namespace App\Menus\Templates;

use Novius\FilamentRelationNested\Concerns\IsMenuTemplate;
use Novius\FilamentRelationNested\Contracts\MenuTemplate;

class MyMenuTemplate implements MenuTemplate // Must implement MenuTemplate interface
{
    use IsMenuTemplate; // This trait will defined required method with default implementation

    public function key(): string
    {
        return 'my-template';
    }

    public function name(): string
    {
        return 'My template';
    }

    public function hasTitle(): bool
    {
        return true; // Define if the menu need a title displaying in front. False by default if you don't implement this method
    }

    public function fields(): array
    {
        return [
            \Filament\Forms\Components\DatePicker::make('date'), // You can add additionals fields on items
        ];
    }

    public function casts(): array
    {
        return [
            'date' => 'date:Y-m-d', // If you add additionals fields on items, you can define their casts
        ];
    }

    public function view(): string
    {
        return 'menus.my-template'; // Define the view used to display this the menu
    }

    public function viewItem(): string
    {
        return 'menus.my-template-item'; // Define the view used to display an item of the menu
    }
}
```
#### Template views

First the view to display the menu :

```bladehtml
@php
    use Novius\FilamentRelationNested\Models\Menu;
    /** @var Menu $menu */
@endphp
<nav>
    @if ($menu->template->hasTitle())
    <div>
        {{ $menu->title ?? $menu->name }}
    </div>
    @endif
    <ul>
        @foreach($items as $item)
            {!! $menu->template->renderItem($menu, $item) !!}
        @endforeach
    </ul>
</nav>

```

The the view to display an item of the menu

```bladehtml
@php
    use Novius\FilamentRelationNested\Enums\LinkType;use Novius\FilamentRelationNested\Models\Menu;
    use Novius\FilamentRelationNested\Models\MenuItem;

    /** @var Menu $menu */
    /** @var MenuItem $item */
@endphp
<li>
    @if ($item->link_type === LinkType::html)
        {!! $item->html !!}
    @else
        <a href="{{ $item->href() }}" 
           @class([
                $item->htmlClasses,
                'active' => $menu->template->isActiveItem($item),
           ]) 
            {{ $item->target_blank ? 'target="_blank"' : '' }}
        >
            {{ $item->title }}
            
            {{-- If you add additionals fields on items, you can access it like this --}}
            {{ $item->extras->date?->format('Y-m-d') ?? '' }} 
        </a>
    @endif

    @if ($item->children->isNotEmpty())
        <ul 
            @class([
                'open' =>  $menu->template->containtActiveItem($item),
            ])
        >
            @foreach($item->children as $item)
                {!! $menu->template->renderItem($menu, $item) !!}
            @endforeach
        </ul>
    @endif
</li>
```

### Manage internal link possibilities

Laravel Filament Menu uses [Laravel Linkable](https://github.com/novius/laravel-linkable) to manage linkable routes and models. Please read the documentation.

## Lint

Run php-cs with:

```sh
composer run-script lint
```

## Contributing

Contributions are welcome!
Leave an issue on Github, or create a Pull Request.


## Licence

This package is under [GNU Affero General Public License v3](http://www.gnu.org/licenses/agpl-3.0.html) or (at your option) any later version.
