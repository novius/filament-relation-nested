<?php

namespace Novius\FilamentRelationNested\Actions;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

/**
 * Move node on tree
 */
class MoveItem
{
    public function __invoke(Model $node, ?int $parent, int $from, int $to): void
    {
        /** @var Model&NodeTrait $node */
        if ($parent === $node->getAttribute($node->getParentIdName())) {
            $this->moveItem($node, $from, $to);

            return;
        }

        if ($parent === null) {
            $this->moveToRoot(
                node: $node,
                position: $to,
            );

            return;
        }

        /** @var Model&NodeTrait $parentNode */
        $parentNode = $node->query()->findOrFail($parent);

        $parentNode->prependNode($node);
        if ($to > 0) {
            $node->down($to);
        }
    }

    private function moveItem(Model $node, int $from, int $to): void
    {
        /** @var Model&NodeTrait $node */
        $shift = $from - $to;
        if ($shift === 0) {
            return;
        }

        if ($from > $to) {
            $node->up($shift);

            return;
        }

        $node->down($shift);
    }

    private function moveToRoot(Model $node, int $position): void
    {
        /** @var Model&NodeTrait $node */
        $node->saveAsRoot();

        $siblingsCount = $node->refresh()->siblings()->count();
        $shift = $siblingsCount - $position;

        $node->up($shift);
    }
}
