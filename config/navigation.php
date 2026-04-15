<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sidebar navigation (authenticated app layout)
    |--------------------------------------------------------------------------
    |
    | Items support type "link" or "group". Groups may define "visible" as a
    | closure(User|null $user): bool to hide the whole group.
    |
    */
    'items' => [
        [
            'type' => 'link',
            'label' => 'Dashboard',
            'route' => 'dashboard',
            'route_is' => 'dashboard',
        ],
        [
            'type' => 'link',
            'label' => 'Profile',
            'route' => 'profile.edit',
            'route_is' => 'profile.*',
        ],
        [
            'type' => 'group',
            'id' => 'administration',
            'label' => 'Administration',
            'visible' => fn ($user) => $user && $user->isSuperAdmin(),
            'children' => [
                [
                    'type' => 'link',
                    'label' => 'User',
                    'route' => 'admin.users.index',
                    'route_is' => 'admin.users.*',
                ],
                [
                    'type' => 'link',
                    'label' => 'Role',
                    'route' => 'admin.roles.index',
                    'route_is' => 'admin.roles.*',
                ],
            ],
        ],
    ],
];
