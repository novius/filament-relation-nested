@php
    use Filament\Actions\BulkAction;
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\VerticalAlignment;
    use Filament\Tables\Enums\RecordActionsPosition;

    $recordActionsPosition = $getRecordActionsPosition();
    $recordKey = $getRecordKey($record);
    $columnsLayout = $getColumnsLayout();
@endphp
<li class="frn:ps-4 frn:my-2" data-id="{{ $record->getKey() }}">
    <div
        class="frn:flex frn:flex-row frn:items-center frn:rounded-xl frn:shadow-sm frn:ring-1 frn:dark:ring-gray-950/5 frn:ring-gray-950/5">

        @if ($recordActionsPosition === RecordActionsPosition::BeforeColumns)
            @include('filament-relation-nested::tree-row-actions', ['record' => $record])
        @endif

        <div class="frn:flex frn:flex-row frn:items-center frn:ps-2">
            @if($record->children->isNotEmpty())
                <span class="frn:pe-2">
                    <x-filament::icon
                        icon="heroicon-o-chevron-{{ \Illuminate\Support\Arr::get($this->opened, $record->getKey()) ? 'down' : 'right' }}"
                        class="frn:cursor-pointer frn:h-5 frn:w-5 frn:text-gray-400 frn:dark:text-gray-400"
                        wire:click="toggleOpen({{ $record->getKey() }})"
                    />
                </span>
            @endif
            <span class="frn:pe-2">
                <x-filament::icon
                    icon="heroicon-o-ellipsis-vertical"
                    class="handle frn:cursor-move frn:h-5 frn:w-5 frn:text-gray-400 frn:dark:text-gray-400"
                />
            </span>
        </div>

        @if ($recordActionsPosition === RecordActionsPosition::BeforeCells)
            @include('filament-relation-nested::tree-row-actions', ['record' => $record])
        @endif

        <div class="frn:sm:flex frn:flex-col frn:sm:flex-row frn:items-center frn:grow frn:ps-2">
            <div class="frn:flex frn:flex-col frn:grow frn:sm:py-0">
                <div
                    class="fi-ta-record-content frn:px-3 frn:py-4"
                >
                    @foreach ($columnsLayout as $columnsLayoutComponent)
                        {{
                            $columnsLayoutComponent
                                ->record($record)
                                ->recordKey($recordKey)
                                ->rowLoop($loop)
                                ->renderInLayout()
                        }}
                    @endforeach
                </div>
            </div>
        </div>

        @if (in_array($recordActionsPosition, [RecordActionsPosition::AfterCells, RecordActionsPosition::AfterColumns], true))
            @include('filament-relation-nested::tree-row-actions', ['record' => $record])
        @endif
    </div>

    <ul class="filament-relation-manager" data-id="{{ $record->getKey() }}">
        @if(\Illuminate\Support\Arr::get($this->opened, $record->getKey()))
            @foreach($record->children->sortBy($record->getLftName()) as $child)
                @include('filament-relation-nested::tree-row', ['record' => $child])
            @endforeach
        @endif
    </ul>

    <x-filament-actions::modals/>
</li>
