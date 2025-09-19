@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\VerticalAlignment;
    use Filament\Support\Facades\FilamentAsset;
    use Filament\Support\Facades\FilamentView;
    use Filament\Tables\Columns\Column;
    use Filament\Tables\Columns\ColumnGroup;
    use Filament\Tables\Enums\ActionsPosition;
    use Filament\Tables\Enums\FiltersLayout;
    use Filament\Tables\Enums\RecordCheckboxPosition;
    use Filament\Tables\View\TablesRenderHook;
    use Illuminate\Support\Str;

    $actions = $getActions();
    $columns = $getVisibleColumns();
    $header = $getHeader();
    $headerActions = array_filter(
        $getHeaderActions(),
        fn (\Filament\Actions\Action | \Filament\Actions\ActionGroup $action): bool => $action->isVisible(),
    );
    $headerActionsPosition = $getHeaderActionsPosition();
    $heading = $getHeading();
    $group = $getGrouping();
    $groups = $getGroups();
    $description = $getDescription();
    $isLoaded = $isLoaded();
    $hasHeader = $header || $heading || $description || $headerActions;
    $records = $isLoaded ? $getRecords() : null;
@endphp

<div
    @if (! $isLoaded)
        wire:init="loadTable"
    @endif
    @if (FilamentView::hasSpaMode())
        x-load="visible"
    @else
        x-load
    @endif
    x-load-src="{{ FilamentAsset::getAlpineComponentSrc('filament-relation-nested', 'filament-relation-nested') }}"
    x-data="filamentRelationNested()"
    @class([
        'fi-ta',
        'animate-pulse' => $records === null,
    ])
>
    <x-filament-tables::container>
        <div
            @if (! $hasHeader) x-cloak @endif
        x-bind:hidden="! @js($hasHeader)"
            x-show="@js($hasHeader)"
            class="fi-ta-header-ctn divide-y divide-gray-200 dark:divide-white/10"
        >
            {{ FilamentView::renderHook(TablesRenderHook::HEADER_BEFORE, scopes: static::class) }}

            @if ($header)
                {{ $header }}
            @elseif (($heading || $description || $headerActions))
                <x-filament-tables::header
                    :actions="$headerActions"
                    :actions-position="$headerActionsPosition"
                    :description="$description"
                    :heading="$heading"
                />
            @endif

            {{ FilamentView::renderHook(TablesRenderHook::HEADER_AFTER, scopes: static::class) }}
        </div>

        <div
            @class([
                'fi-ta-content relative divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 dark:border-t-white/10',
                '!border-t-0' => ! $hasHeader,
            ])
        >
            <div x-ref="tree" class="pb-4">
                @if (($records !== null) && count($records))
                    <nav class="text-base lg:text-sm pe-4 pt-4">
                        <ul class="filament-relation-manager" data-id>
                            @foreach($records as $record)
                                @include('filament-relation-nested::tree-row', ['record' => $record])
                            @endforeach
                        </ul>
                    </nav>
                @elseif ($records === null)
                    <div class="flex h-32 items-center justify-center">
                        <x-filament::loading-indicator class="h-8 w-8"/>
                    </div>
                @elseif ($emptyState = $getEmptyState())
                    {{ $emptyState }}
                @else
                    <x-filament-tables::empty-state
                        :actions="$getEmptyStateActions()"
                        :description="$getEmptyStateDescription()"
                        :heading="$getEmptyStateHeading()"
                        :icon="$getEmptyStateIcon()"
                    />
                @endif
            </div>
        </div>

    </x-filament-tables::container>

    <x-filament-actions::modals/>
</div>
