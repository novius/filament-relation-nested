<?php

namespace Novius\FilamentRelationNested\Filament\Resources\RelationManagers;

use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Kalnoy\Nestedset\NodeTrait;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use LogicException;
use Novius\FilamentRelationNested\Actions\MoveItem;
use Novius\FilamentRelationNested\Filament\Trees\TreeTable;
use RuntimeException;

class TreeRelationManager extends RelationManager
{
    #[Session(key: 'tree-collapsed-{pageClass}-{ownerRecord.id}')]
    public array $opened = [];

    protected bool $allowDeleteParent = false;

    public function mount(): void
    {
        parent::mount();

        $relation = $this->getRelationship();
        $model = $relation->getModel();

        if (! in_array(NodeTrait::class, class_uses_recursive($model), true)) {
            throw new RuntimeException(
                sprintf('Model should use %s', NodeTrait::class),
            );
        }
    }

    public function allowDeleteParent(bool $allowDeleteParent): static
    {
        $this->allowDeleteParent = $allowDeleteParent;

        return $this;
    }

    protected function makeBaseTable(): Table
    {
        return TreeTable::make($this);
    }

    protected function configureDeleteAction(DeleteAction $action): void
    {
        $action
            ->authorize(function (RelationManager $livewire, Model $record): bool {
                /** @var Model&NodeTrait $record */
                return (! $livewire->isReadOnly()) && $livewire->canDelete($record) &&
                    ! ($this->allowDeleteParent === false && $record->children->isNotEmpty());
            });
    }

    public function getTableRecords(): Collection|Paginator|CursorPaginator
    {
        $relation = $this->getRelationship();
        $model = $relation->getModel();
        /** @var Model&NodeTrait $model */
        $query = $model::query()->defaultOrder();

        return $query->get()->toTree();
    }

    #[On('filament-relation-nested-moved')]
    public function moveTreeItem(int $id, ?string $parent, int $from, int $to): void
    {
        if ($parent === '') {
            $parent = null;
        }

        $relation = $this->getRelationship();
        $modelClass = $relation->getModel();
        $query = $modelClass::query();

        /** @var Model $node */
        $node = $query->findOrFail($id);

        try {
            app(MoveItem::class)(
                node: $node,
                parent: $parent === null ? null : (int) $parent,
                from: $from,
                to: $to,
            );
        } catch (LogicException $e) {
            Notification::make()
                ->danger()
                ->title($e->getMessage())
                ->send();

            return;
        }

        Notification::make()
            ->success()
            ->title(trans('filament-relation-nested::messages.item_moved'))
            ->send();
    }

    public function toggleOpen($id): void
    {
        $this->opened[$id] = ! Arr::get($this->opened, $id, false);
    }

    #[On('filament-relation-nested-updated')]
    public function refreshNode(): void {}
}
