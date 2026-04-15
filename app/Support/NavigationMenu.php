<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;

class NavigationMenu
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function sidebarItems(?User $user): array
    {
        $items = config('navigation.items', []);

        return collect($items)
            ->map(fn (array $item) => self::filterItem($item, $user))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>|null
     */
    private static function filterItem(array $item, ?User $user): ?array
    {
        $visible = Arr::get($item, 'visible');
        if ($visible instanceof \Closure) {
            if (! $visible($user)) {
                return null;
            }
            $item = Arr::except($item, ['visible']);
        }

        if (($item['type'] ?? '') === 'group' && isset($item['children'])) {
            $children = collect($item['children'])
                ->map(fn (array $child) => self::filterItem($child, $user))
                ->filter()
                ->values()
                ->all();

            if ($children === []) {
                return null;
            }

            $item['children'] = $children;
        }

        return $item;
    }

    /**
     * Initial open state for Alpine: group id => bool.
     *
     * @param  list<array<string, mixed>>  $items
     * @return array<string, bool>
     */
    public static function sidebarOpenGroups(array $items): array
    {
        $open = [];

        foreach ($items as $item) {
            if (($item['type'] ?? '') !== 'group' || empty($item['children']) || empty($item['id'])) {
                continue;
            }

            $id = (string) $item['id'];
            $open[$id] = collect($item['children'])->contains(function (array $child): bool {
                $pattern = $child['route_is'] ?? null;

                return is_string($pattern) && Request::routeIs($pattern);
            });
        }

        return $open;
    }
}
