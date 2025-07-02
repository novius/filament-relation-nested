<?php

namespace Novius\FilamentRelationNested;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;

class FilamentRelationNestedServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $packageDir = dirname(__DIR__);

        $this->loadViewsFrom($packageDir.'/resources/views', 'filament-relation-nested');

        $this->loadTranslationsFrom($packageDir.'/lang', 'filament-relation-nested');
        $this->publishes([$packageDir.'/lang' => lang_path('vendor/filament-relation-nested')], 'lang');

        FilamentAsset::register([
            AlpineComponent::make('filament-relation-nested', __DIR__.'/../resources/dist/filament-relation-nested.js'),
        ], package: 'filament-relation-nested');
    }

    /**
     * Register any application services.
     */
    public function register(): void {}
}
