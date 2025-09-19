@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\VerticalAlignment;
    use Filament\Support\Facades\FilamentAsset;
    use Filament\Support\Facades\FilamentView;
    use Filament\Tables\Actions\HeaderActionsPosition;
    use Filament\Tables\Columns\Column;
    use Filament\Tables\Columns\ColumnGroup;
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
    $headingTag = $getHeadingTag();
    $secondLevelHeadingTag = $heading ? $getHeadingTag(1) : $headingTag;
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
    <div
        @class([
            'fi-ta-ctn',
            'fi-ta-ctn-with-header' => $hasHeader,
        ])
    >
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
                <div
                    @class([
                        'fi-ta-header',
                        'fi-ta-header-adaptive-actions-position' => $headerActions && ($headerActionsPosition === HeaderActionsPosition::Adaptive),
                    ])
                >
                    @if ($heading || $description)
                        <div>
                            @if ($heading)
                                <{{ $headingTag }}
                                    class="fi-ta-header-heading"
                                >
                                {{ $heading }}
                        </{{ $headingTag }}>
                    @endif

                    @if ($description)
                        <p class="fi-ta-header-description">
                            {{ $description }}
                        </p>
                    @endif
                </div>
            @endif

            @if ($headerActions)
                <div class="fi-ta-actions fi-align-start fi-wrapped">
                    @foreach ($headerActions as $action)
                        {{ $action }}
                    @endforeach
                </div>
            @endif
        </div>
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
                    <div class="fi-ta-empty-state">
                        <div class="fi-ta-empty-state-content">
                            <div class="fi-ta-empty-state-icon-bg">
                                {{ \Filament\Support\generate_icon_html($getEmptyStateIcon(), size: \Filament\Support\Enums\IconSize::Large) }}
                            </div>

                            <{{ $secondLevelHeadingTag }}
                                class="fi-ta-empty-state-heading"
                            >
                              {{ $getEmptyStateHeading() }}
                            </{{ $secondLevelHeadingTag }}>

                            @if (filled($emptyStateDescription = $getEmptyStateDescription()))
                                <p class="fi-ta-empty-state-description">
                                    {{ $emptyStateDescription }}
                                </p>
                            @endif

                            @if ($emptyStateActions = array_filter(
                                     $getEmptyStateActions(),
                                     fn (\Filament\Actions\Action | \Filament\Actions\ActionGroup $action): bool => $action->isVisible(),
                                 ))
                                <div
                                    class="fi-ta-actions fi-align-center fi-wrapped"
                                >
                                    @foreach ($emptyStateActions as $action)
                                        {{ $action }}
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-filament-actions::modals/>
</div>
