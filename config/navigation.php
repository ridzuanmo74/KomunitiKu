<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sidebar navigation (authenticated app layout)
    |--------------------------------------------------------------------------
    |
    | Dashboard and account profile are provided in the top bar
    | (resources/views/layouts/navigation.blade.php), not here.
    |
    | Items support type "link" or "group". Groups may define "visible" as a
    | closure(User|null $user): bool to hide the whole group.
    |
    */
    'items' => [
        [
            'type' => 'group',
            'id' => 'persatuan-saya',
            'label' => 'Persatuan Saya',
            'visible' => fn ($user) => $user && ! $user->isSuperAdmin(),
            'children' => [
                [
                    'type' => 'link',
                    'label' => 'Senarai Persatuan',
                    'route' => 'member.associations.index',
                    'route_is' => 'member.associations.index',
                ],
                [
                    'type' => 'link',
                    'label' => 'Tukar Persatuan Aktif',
                    'route' => 'member.associations.index',
                    'route_is' => 'member.associations.index',
                ],
                [
                    'type' => 'link',
                    'label' => 'Permohonan Keahlian',
                    'route' => 'member.membership.applications',
                    'route_is' => 'member.membership.applications',
                ],
                [
                    'type' => 'link',
                    'label' => 'Kelulusan Keahlian',
                    'route' => 'committee.associations.approvals',
                    'route_is' => 'committee.associations.approvals',
                    'visible' => fn ($user) => $user && $user->canManageCommitteeMembership() && ! $user->isSuperAdmin(),
                ],
            ],
        ],
        [
            'type' => 'group',
            'id' => 'keahlian',
            'label' => 'Keahlian',
            'visible' => fn ($user) => $user && ! $user->isSuperAdmin(),
            'children' => [
                [
                    'type' => 'link',
                    'label' => 'Profil Keahlian',
                    'route' => 'member.membership.profile',
                    'route_is' => 'member.membership.profile',
                ],
                [
                    'type' => 'link',
                    'label' => 'Kad Ahli / Bukti Keahlian',
                    'route' => 'member.membership.card',
                    'route_is' => 'member.membership.card',
                ],
            ],
        ],
        [
            'type' => 'group',
            'id' => 'yuran',
            'label' => 'Yuran',
            'visible' => fn ($user) => $user && ! $user->isSuperAdmin(),
            'children' => [
                [
                    'type' => 'link',
                    'label' => 'Jenis Yuran',
                    'route' => 'member.fees.index',
                    'route_is' => 'member.fees.index',
                ],
                [
                    'type' => 'link',
                    'label' => 'Invois / Tuntutan',
                    'route' => 'member.invoices.index',
                    'route_is' => 'member.invoices.index',
                ],
                [
                    'type' => 'link',
                    'label' => 'Bayaran Saya',
                    'route' => 'member.payments.index',
                    'route_is' => 'member.payments.index',
                ],
                [
                    'type' => 'link',
                    'label' => 'Resit',
                    'route' => 'member.receipts.index',
                    'route_is' => 'member.receipts.index',
                ],
            ],
        ],
        [
            'type' => 'group',
            'id' => 'aktiviti',
            'label' => 'Aktiviti',
            'visible' => fn ($user) => $user && ! $user->isSuperAdmin(),
            'children' => [
                [
                    'type' => 'link',
                    'label' => 'Kalendar Aktiviti',
                    'route' => 'member.activities.index',
                    'route_is' => 'member.activities.index',
                ],
                [
                    'type' => 'link',
                    'label' => 'Kehadiran',
                    'route' => 'member.attendances.index',
                    'route_is' => 'member.attendances.index',
                ],
            ],
        ],
        [
            'type' => 'group',
            'id' => 'pengurusan-persatuan',
            'label' => 'Pengurusan Persatuan',
            'visible' => fn ($user) => $user && ($user->isSuperAdmin() || $user->canManageCommitteeMembership()),
            'children' => [
                [
                    'type' => 'link',
                    'label' => 'Maklumat Persatuan',
                    'route' => 'committee.associations.info',
                    'route_is' => 'committee.associations.info',
                ],
                [
                    'type' => 'link',
                    'label' => 'Daftar persatuan baharu',
                    'route' => 'committee.associations.create',
                    'route_is' => 'committee.associations.create',
                    'visible' => fn ($user) => $user && $user->isSuperAdmin(),
                ],
            ],
        ],
        [
            'type' => 'group',
            'id' => 'pengurusan-yuran',
            'label' => 'Pengurusan Yuran',
            'visible' => fn ($user) => $user && $user->canAccessCommitteeFees(),
            'children' => [
                [
                    'type' => 'link',
                    'label' => 'Tetapan Yuran',
                    'route' => 'committee.fees.settings',
                    'route_is' => 'committee.fees.settings',
                ],
                [
                    'type' => 'link',
                    'label' => 'Jana Invois Berkala',
                    'route' => 'committee.fees.invoices.generate',
                    'route_is' => 'committee.fees.invoices.generate',
                ],
                [
                    'type' => 'link',
                    'label' => 'Semakan Bayaran',
                    'route' => 'committee.fees.payments.review',
                    'route_is' => 'committee.fees.payments.review',
                ],
                [
                    'type' => 'link',
                    'label' => 'Tunggakan',
                    'route' => 'committee.fees.arrears',
                    'route_is' => 'committee.fees.arrears',
                ],
            ],
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
