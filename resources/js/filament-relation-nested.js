import Sortable from 'sortablejs';

export default function filamentRelationNested() {
  return {
    init: function () {
      this.initFilamentMenuTree();
      Livewire.hook("commit", () => this.initFilamentMenuTree());
    },

    initFilamentMenuTree:  function () {
      if (this.$refs.tree) {
        const e = this.$refs.tree.querySelectorAll(".filament-relation-manager");
        for (let t = 0; t < e.length; t++) {
          console.log('sortable', e[t]);
          Sortable.create(e[t], {
            group: "tree-nested",
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 1,
            handle: ".handle",
            onEnd: e => {
              let t = {
                id: e.item.dataset.id,
                parent: e.to.dataset.id,
                from: e.oldIndex,
                to: e.newIndex
              };
              t.parent === t.ancestor && t.from === t.to || Livewire.dispatch("filament-relation-nested-moved", t);
            }
          });
        }
      }
    }
  }
}
