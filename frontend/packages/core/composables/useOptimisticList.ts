import { shallowRef } from "vue";

export function useOptimisticList<T extends { id: PropertyKey }>() {
  const items = shallowRef<T[]>([]);

  const set = (data: readonly T[]) => {
    items.value = [...data];
  };

  const add = (item: T) => {
    items.value.unshift(item);
  };

  const update = (updated: T) => {
    items.value = items.value.map((i) => (i.id === updated.id ? updated : i));
  };

  const remove = (id: T["id"]) => {
    items.value = items.value.filter((i) => i.id !== id);
  };

  return {
    items,
    set,
    add,
    update,
    remove,
  };
}
