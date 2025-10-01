<?php

namespace Novius\FilamentRelationNested\Filament\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Table;
use Kalnoy\Nestedset\QueryBuilder;
use Throwable;

class FixTreeAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(trans('filament-relation-nested::messages.fix_tree'));
        $this->icon('heroicon-s-wrench');
        $this->action(function (Table $table, Action $action): void {
            /** @var ?QueryBuilder $relation */
            $relation = $table->getRelationship();

            try {
                $relation?->fixTree();
            } catch (Throwable $e) {
                report($e);

                Notification::make()
                    ->danger()
                    ->title($e->getMessage())
                    ->send();

                $action->failure();

                return;
            }

            $this->dispatch('filament-relation-nested-updated');

            Notification::make()
                ->success()
                ->title(trans('filament-relation-nested::messages.tree_fixed'))
                ->send();

            $action->success();
        });
    }

    public function getName(): ?string
    {
        if (blank($this->name)) {
            return 'fixTree';
        }

        return $this->name;
    }
}
