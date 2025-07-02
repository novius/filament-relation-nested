<?php

namespace Novius\FilamentRelationNested\Filament\Tables\Columns;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class TreeColumn extends TextColumn
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->label(trans('filament-relation-nested::messages.column_tree'));
        $this->width(1);
        $this->html();
        $this->sortable(function (Table $table) {
            if ($table->getRelationship()) {
                $model = $table->getRelationship()->getModel();
                if (in_array(NodeTrait::class, class_uses_recursive($model), true)) {
                    /** @var Model&NodeTrait $model */
                    return [$model->getLftName()];
                }
            }

            return false;
        });
        $this->state(function (Model $record, Table $table) {
            if (! in_array(NodeTrait::class, class_uses_recursive($record), true)) {
                return '';
            }

            /** @var Model&NodeTrait $record */
            if (! empty($table->getSortColumn()) && $table->getSortColumn() !== $record->getLftName()) {
                return '';
            }

            $level = $record->ancestors->count();
            if ($level === 0) {
                return '<code>□</code>';
            }

            $isLast = $record->isLeaf();
            $hasNextSibling = $record->nextSiblings()->count() !== 0;

            $prefix = '<code>'.str_repeat('┃&nbsp;&nbsp;',
                $level - 1); // Espaces pour l'indentation

            if ($isLast && ! $hasNextSibling) {
                return $prefix.'┗━━□'.'</code>';
            }

            return $prefix.'┣━━□'.'</code>';
        });
    }
}
