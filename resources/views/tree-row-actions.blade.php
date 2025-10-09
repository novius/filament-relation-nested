@php
    use Filament\Actions\BulkAction;
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\VerticalAlignment;
    use Filament\Tables\Enums\RecordActionsPosition;

    $recordActionsAlignment = $getRecordActionsAlignment();
    $recordActionsPosition = $getRecordActionsPosition();
    $recordActions = array_reduce(
        $defaultRecordActions,
        static function (array $carry, $action) use ($record): array {
            $action = $action->getClone();

            if (! $action instanceof BulkAction) {
                $action->record($record);
            }

            if ($action->isHidden()) {
                return $carry;
            }

            $carry[] = $action;

            return $carry;
        },
        initial: [],
    );
@endphp
@if (count($recordActions))
    <div class="tree-row-actions frn:flex frn:flex-row frn:items-center frn:gap-x-2 frn:pe-2 frn:ps-2">
        <div
            @class([
                'fi-ta-actions fi-wrapped sm:fi-not-wrapped',
                match ($recordActionsAlignment ?? Alignment::Start) {
                    Alignment::Start => 'fi-align-start',
                    Alignment::Center => 'fi-align-center',
                    Alignment::End => 'fi-align-end',
                } => $contentGrid,
                'fi-align-start md:fi-align-end' => ! $contentGrid,
                'fi-ta-actions-before-columns-position' => $recordActionsPosition === RecordActionsPosition::BeforeColumns,
            ])
        >
            @foreach ($recordActions as $action)
                {{ $action }}
            @endforeach
        </div>
    </div>
@endif
