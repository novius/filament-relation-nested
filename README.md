# Laravel Filament Relation Nested

[![Novius CI](https://github.com/novius/filament-relation-nested/actions/workflows/main.yml/badge.svg?branch=main)](https://github.com/novius/filament-relation-nested/actions/workflows/main.yml)
[![Packagist Release](https://img.shields.io/packagist/v/novius/filament-relation-nested.svg?maxAge=1800&style=flat-square)](https://packagist.org/packages/novius/filament-relation-nested)
[![License: AGPL v3](https://img.shields.io/badge/License-AGPL%20v3-blue.svg)](http://www.gnu.org/licenses/agpl-3.0)

## Introduction

This [Laravel Filament](https://filamentphp.com/) package allows you to manage relation that uses the [kalnoy/nestedset](https://packagist.org/packages/kalnoy/nestedset) in your Laravel Filament admin panel.

## Requirements

* PHP >= 8.2
* Laravel Filament >= 4
* Laravel Framework >= 11.0 

## Installation

```sh
composer require novius/filament-relation-nested
```

Publish Filament assets

```sh
php artisan filament:assets
```

## Usage

### Relation Manager

First create a RelationManager for your filament resource that have a relation to a model that uses nestedset package:

```php
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Novius\FilamentRelationNested\Filament\Actions\FixTreeAction;
use Novius\FilamentRelationNested\Filament\Resources\RelationManagers\TreeRelationManager;

class MenuItemsTreeRelationManager extends TreeRelationManager
{
    // Define the relationship name
    protected static string $relationship = 'items';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
            ])
            ->pluralModelLabel('Menu items')
            ->recordTitleAttribute('title')
            ->headerActions([
                CreateAction::make(),
                    
                // Add the FixTreeAction if you want an action that fix the nestedset tree 
                FixTreeAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
```

Then add it to your resource

```php
class MenuResource extends Resource
{
    public static function getRelations(): array
    {
        return [
            // ...
            MenuItemsTreeRelationManager::class,
        ];
    }
}
```

### TreeColumn

You can use this column in a filament table on a model that uses the nestedset package. 
This will display a column which, when sorting is this column, will give an idea of the tree. 

```php
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Exception;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Novius\FilamentRelationNested\Filament\Tables\Columns\TreeColumn;
use Novius\LaravelFilamentMenu\Enums\LinkType;
use Novius\LaravelFilamentMenu\Facades\MenuManager;
use Novius\LaravelFilamentMenu\Filament\Resources\MenuItemResource\Pages\CreateMenuItem;
use Novius\LaravelFilamentMenu\Filament\Resources\MenuItemResource\Pages\EditMenuItem;
use Novius\LaravelFilamentMenu\Filament\Resources\MenuItemResource\RelationManagers\MenuItemsRelationManager;
use Novius\LaravelFilamentMenu\Models\Menu;
use Novius\LaravelFilamentMenu\Models\MenuItem;
use Novius\LaravelLinkable\Filament\Forms\Components\Linkable;
use Wiebenieuwenhuis\FilamentCodeEditor\Components\CodeEditor;

class MenuItemResource extends Resource
{
    public static function table(Table $table): Table
    {
        return $table
            // This will disable the pagination when the table is sorted by the _lft field
            ->paginated(fn (Table $table) => ! empty($table->getSortColumn()) && $table->getSortColumn() !== '_lft')
            // This will default sort the table on the _lft field
            ->defaultSort('_lft')
            ->columns([
                TreeColumn::make('_lft'),

                // ....
            ]);
    }
}
```

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
