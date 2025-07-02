@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\VerticalAlignment;

    $recordAction = $getRecordAction($record);
    $recordKey = $getRecordKey($record);
@endphp
<li class="ps-4 my-2" data-id="{{ $record->getKey() }}">
    <div
        class="flex flex-row items-center rounded-xl shadow-sm ring-1 dark:ring-gray-950/5 ring-gray-950/5">
        <div class="flex flex-row items-center ps-2">
            @if($record->children->isNotEmpty())
                <span class="pe-2">
                    <x-filament::icon
                        icon="heroicon-o-chevron-{{ \Illuminate\Support\Arr::get($this->opened, $record->getKey()) ? 'down' : 'right' }}"
                        class="cursor-pointer h-5 w-5 text-gray-400 dark:text-gray-400"
                        wire:click="toggleOpen({{ $record->getKey() }})"
                    />
                </span>
            @endif
            <span class="pe-2">
                <x-filament::icon
                    icon="heroicon-o-ellipsis-vertical"
                    class="handle cursor-move h-5 w-5 text-gray-400 dark:text-gray-400"
                />
            </span>
        </div>
        <div class="sm:flex flex-col sm:flex-row items-center grow ps-2">
            <div class="flex flex-col grow sm:py-0">
                @foreach ($columns as $column)
                    @php
                        $column->record($record);
                        $column->rowLoop($loop->parent);
                    @endphp
                    <x-filament-tables::cell
                        tag="div"
                        :wire:key="$this->getId() . '.table.record.' . $recordKey . '.column.' . $column->getName()"
                        :attributes="
                                                \Filament\Support\prepare_inherited_attributes($column->getExtraCellAttributeBag())
                                                    ->class([
                                                        'fi-table-cell-' . str($column->getName())->camel()->kebab(),
                                                        match ($column->getVerticalAlignment()) {
                                                            VerticalAlignment::Start => 'align-top',
                                                            VerticalAlignment::Center => 'align-middle',
                                                            VerticalAlignment::End => 'align-bottom',
                                                            default => null,
                                                        },
                                                    ])
                                            "
                    >
                        <x-filament-tables::columns.column
                            :column="$column"
                            :is-click-disabled="true"
                            :record="$record"
                            :record-action="$recordAction"
                            :record-key="$recordKey"
                        />
                    </x-filament-tables::cell>
                @endforeach
            </div>
        </div>
        <div class="tree-row-actions flex flex-row items-center gap-x-2 pe-2">
            @if (count($actions))
                <x-filament-tables::actions.cell>
                    <x-filament-tables::actions
                        :actions="$actions"
                        :alignment="Alignment::End"
                        :record="$record"
                    />
                </x-filament-tables::actions.cell>
            @endif
        </div>
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
