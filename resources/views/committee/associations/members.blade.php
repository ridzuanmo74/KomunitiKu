@php
    $propertyLabel = static function (?string $v): string {
        return match ($v) {
            'owner' => __('Pemilik'),
            'tenant' => __('Penyewa'),
            'family_member' => __('Ahli keluarga'),
            default => '—',
        };
    };
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight">
            {{ __('Ahli Persatuan') }}
        </h2>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-2 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-800">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div
        class="space-y-4"
        x-data="{
            editOpen: false,
            viewOpen: false,
            viewDetail: {},
            formAction: '',
            memberName: '',
            memberEmail: '',
            form: {
                membership_no: '',
                phone: '',
                address: '',
                postcode: '',
                city: '',
                state_id: '',
                latitude: '',
                longitude: '',
                property_relationship: '',
                is_voting_eligible: false,
            },
            openViewDetail(d) {
                if (this.editOpen) {
                    this.closeEdit();
                }
                this.viewDetail = d;
                this.viewOpen = true;
                document.body.classList.add('overflow-y-hidden');
            },
            closeViewDetail() {
                this.viewOpen = false;
                this.viewDetail = {};
                if (! this.editOpen) {
                    document.body.classList.remove('overflow-y-hidden');
                }
            },
            openEdit(payload) {
                if (this.viewOpen) {
                    this.closeViewDetail();
                }
                this.formAction = payload.updateUrl;
                this.memberName = payload.memberName ?? '';
                this.memberEmail = payload.memberEmail ?? '';
                this.form = { ...payload.form };
                this.editOpen = true;
                document.body.classList.add('overflow-y-hidden');
            },
            closeEdit() {
                this.editOpen = false;
                if (! this.viewOpen) {
                    document.body.classList.remove('overflow-y-hidden');
                }
            },
        }"
        @keydown.escape.window="if (viewOpen) { closeViewDetail(); } else if (editOpen) { closeEdit(); }"
    >
        <div class="kk-card p-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
                <p class="text-sm text-kk-sidebar-text">
                    <span class="font-medium text-kk-sidebar-muted">{{ __('Persatuan') }}:</span>
                    <span class="text-gray-900">{{ $association?->name ?: __('Tiada') }}</span>
                </p>
                <div class="flex flex-wrap gap-2">
                    <a
                        href="{{ $association ? route('committee.associations.info', ['association' => $association->id]) : route('committee.associations.info') }}"
                        class="inline-flex items-center rounded-md border border-kk-border bg-kk-surface-muted px-3 py-2 text-sm font-medium text-kk-sidebar-text shadow-sm hover:bg-kk-sidebar-hover"
                    >{{ __('Maklumat persatuan') }}</a>
                </div>
            </div>
        </div>

        <div class="kk-card overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-kk-border/60 text-sm">
                    <thead class="bg-kk-sidebar-hover/80">
                        <tr>
                            <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">{{ __('Nama') }}</th>
                            <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">{{ __('Emel') }}</th>
                            <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">{{ __('No Ahli') }}</th>
                            <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">{{ __('Negeri') }}</th>
                            <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">{{ __('Hubungan harta') }}</th>
                            <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">{{ __('Layak mengundi') }}</th>
                            <th scope="col" class="relative px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">
                                <span class="sr-only">{{ __('Tindakan') }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-kk-border/40 bg-kk-surface">
                        @forelse ($members as $member)
                            @php
                                $p = $member->pivot;
                                $updateUrl = $association
                                    ? route('committee.associations.members.update', [
                                        'user' => $member->id,
                                        'association' => $association->id,
                                    ])
                                    : '#';
                                $editPayload = [
                                    'updateUrl' => $updateUrl,
                                    'memberName' => $member->name,
                                    'memberEmail' => $member->email,
                                    'form' => [
                                        'membership_no' => (string) ($p->membership_no ?? ''),
                                        'phone' => (string) ($p->phone ?? ''),
                                        'address' => (string) ($p->address ?? ''),
                                        'postcode' => (string) ($p->postcode ?? ''),
                                        'city' => (string) ($p->city ?? ''),
                                        'state_id' => $p->state_id !== null ? (string) $p->state_id : '',
                                        'latitude' => $p->latitude !== null ? (string) $p->latitude : '',
                                        'longitude' => $p->longitude !== null ? (string) $p->longitude : '',
                                        'property_relationship' => (string) ($p->property_relationship ?? ''),
                                        'is_voting_eligible' => (bool) $p->is_voting_eligible,
                                    ],
                                ];
                                $viewPayload = [
                                    'name' => $member->name,
                                    'email' => $member->email,
                                    'phone' => $p->phone ?: '—',
                                    'membership_no' => $p->membership_no ?: '—',
                                    'joined_at' => $p->joined_at?->format('Y-m-d') ?: '—',
                                    'is_active_label' => $p->is_active ? __('Aktif') : __('Tidak aktif'),
                                    'address' => $p->address ?: '—',
                                    'postcode' => $p->postcode ?: '—',
                                    'city' => $p->city ?: '—',
                                    'state_name' => ($p->relationLoaded('state') && $p->state) ? $p->state->name : '—',
                                    'latitude' => $p->latitude !== null ? (string) $p->latitude : '—',
                                    'longitude' => $p->longitude !== null ? (string) $p->longitude : '—',
                                    'property_relationship_label' => $propertyLabel($p->property_relationship),
                                    'voting_label' => $p->is_voting_eligible ? __('Ya') : __('Tidak'),
                                ];
                            @endphp
                            <tr class="hover:bg-kk-sidebar-hover/50">
                                <td class="whitespace-nowrap px-4 py-2.5 font-medium text-gray-900">{{ $member->name }}</td>
                                <td class="px-4 py-2.5 text-gray-700">{{ $member->email }}</td>
                                <td class="whitespace-nowrap px-4 py-2.5 text-gray-700">{{ $p->membership_no ?: '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-2.5 text-gray-700">{{ $p->relationLoaded('state') && $p->state ? $p->state->name : '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-2.5 text-gray-700">{{ $propertyLabel($p->property_relationship) }}</td>
                                <td class="whitespace-nowrap px-4 py-2.5 text-gray-700">{{ $p->is_voting_eligible ? __('Ya') : __('Tidak') }}</td>
                                <td class="whitespace-nowrap px-4 py-2.5 text-right text-gray-700">
                                    @if ($association)
                                        <div class="inline-flex items-center justify-end gap-1">
                                            <button
                                                type="button"
                                                class="inline-flex rounded p-1.5 text-indigo-600 hover:bg-indigo-50 hover:text-indigo-800"
                                                title="{{ __('Lihat maklumat ahli') }}"
                                                @click="openViewDetail(@js($viewPayload))"
                                            >
                                                <span class="sr-only">{{ __('Lihat maklumat ahli') }}</span>
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </button>
                                            <button
                                                type="button"
                                                class="inline-flex rounded p-1.5 text-teal-700 hover:bg-teal-50 hover:text-teal-900"
                                                title="{{ __('Sunting maklumat ahli') }}"
                                                @click="openEdit(@js($editPayload))"
                                            >
                                                <span class="sr-only">{{ __('Sunting') }}</span>
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-gray-500">{{ __('Tiada ahli untuk dipaparkan.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div
            x-show="viewOpen"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
            style="display: none;"
            role="dialog"
            aria-modal="true"
            aria-labelledby="member-view-title"
        >
            <div
                class="kk-modal-backdrop fixed inset-0 transition-opacity"
                x-show="viewOpen"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="closeViewDetail()"
            ></div>

            <div
                class="kk-modal-surface relative mx-auto mb-6 max-h-[min(90vh,48rem)] w-full max-w-2xl overflow-y-auto"
                x-show="viewOpen"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-3 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-3 sm:translate-y-0 sm:scale-95"
                @click.stop
            >
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-kk-border bg-gradient-to-r from-kk-modal-from to-kk-modal-to px-4 py-3">
                    <h3 id="member-view-title" class="text-base font-semibold text-kk-nav-fg">{{ __('Maklumat ahli') }}</h3>
                    <button
                        type="button"
                        class="rounded-md p-2 text-kk-nav-muted hover:bg-white/10 hover:text-kk-nav-fg"
                        @click="closeViewDetail()"
                    >
                        <span class="sr-only">{{ __('Tutup') }}</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="px-4 py-4">
                    <dl class="grid gap-3 text-sm sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <dt class="text-gray-500">{{ __('Nama') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="viewDetail.name"></dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-gray-500">{{ __('Emel') }}</dt>
                            <dd class="break-all font-medium text-gray-900" x-text="viewDetail.email"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('Telefon') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="viewDetail.phone"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('No Ahli') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="viewDetail.membership_no"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('Tarikh sertai') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="viewDetail.joined_at"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('Status keahlian') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="viewDetail.is_active_label"></dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-gray-500">{{ __('Alamat') }}</dt>
                            <dd class="whitespace-pre-wrap font-medium text-gray-900" x-text="viewDetail.address"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('Poskod') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="viewDetail.postcode"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('Bandar') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="viewDetail.city"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('Negeri') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="viewDetail.state_name"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('Latitud') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="viewDetail.latitude"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('Longitud') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="viewDetail.longitude"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('Hubungan dengan harta') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="viewDetail.property_relationship_label"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('Layak mengundi') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="viewDetail.voting_label"></dd>
                        </div>
                    </dl>
                    <div class="mt-6 flex justify-end border-t border-kk-border pt-4">
                        <button
                            type="button"
                            class="inline-flex items-center rounded-md bg-kk-nav-from px-3 py-2 text-sm font-medium text-kk-nav-fg shadow-sm hover:bg-kk-nav-to"
                            @click="closeViewDetail()"
                        >{{ __('Tutup') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div
            x-show="editOpen"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
            style="display: none;"
            role="dialog"
            aria-modal="true"
            aria-labelledby="member-edit-title"
        >
            <div
                class="kk-modal-backdrop fixed inset-0 transition-opacity"
                x-show="editOpen"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="closeEdit()"
            ></div>

            <div
                class="kk-modal-surface relative mx-auto mb-6 max-h-[min(90vh,48rem)] w-full max-w-2xl overflow-y-auto"
                x-show="editOpen"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-3 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-3 sm:translate-y-0 sm:scale-95"
                @click.stop
            >
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-kk-border bg-gradient-to-r from-kk-modal-from to-kk-modal-to px-4 py-3">
                    <h3 id="member-edit-title" class="text-base font-semibold text-kk-nav-fg">{{ __('Maklumat ahli') }}</h3>
                    <button
                        type="button"
                        class="rounded-md p-2 text-kk-nav-muted hover:bg-white/10 hover:text-kk-nav-fg"
                        @click="closeEdit()"
                    >
                        <span class="sr-only">{{ __('Tutup') }}</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form method="post" :action="formAction" class="space-y-4 px-4 py-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="member_readonly_name" class="block text-xs font-medium text-kk-sidebar-muted">{{ __('Nama') }}</label>
                        <input
                            id="member_readonly_name"
                            type="text"
                            readonly
                            disabled
                            :value="memberName"
                            tabindex="-1"
                            class="mt-1 w-full cursor-not-allowed rounded-md border-kk-border bg-kk-surface-muted text-sm text-gray-700 shadow-sm"
                        />
                    </div>
                    <div>
                        <label for="member_readonly_email" class="block text-xs font-medium text-kk-sidebar-muted">{{ __('Emel') }}</label>
                        <input
                            id="member_readonly_email"
                            type="email"
                            readonly
                            disabled
                            :value="memberEmail"
                            tabindex="-1"
                            class="mt-1 w-full cursor-not-allowed rounded-md border-kk-border bg-kk-surface-muted text-sm text-gray-700 shadow-sm"
                        />
                    </div>
                    <div>
                        <label for="member_phone" class="block text-xs font-medium text-kk-sidebar-muted">
                            {{ __('Telefon') }}<span class="text-red-600" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="member_phone"
                            type="tel"
                            name="phone"
                            required
                            x-model="form.phone"
                            autocomplete="tel"
                            class="mt-1 w-full rounded-md border-kk-border text-sm shadow-sm focus:border-kk-accent focus:ring-kk-accent/30"
                        />
                    </div>
                    <div>
                        <label for="member_membership_no" class="block text-xs font-medium text-kk-sidebar-muted">{{ __('No Ahli') }}</label>
                        <input
                            id="member_membership_no"
                            type="text"
                            name="membership_no"
                            x-model="form.membership_no"
                            class="mt-1 w-full rounded-md border-kk-border text-sm shadow-sm focus:border-kk-accent focus:ring-kk-accent/30"
                        />
                    </div>
                    <div>
                        <label for="member_address" class="block text-xs font-medium text-kk-sidebar-muted">
                            {{ __('Alamat') }}<span class="text-red-600" aria-hidden="true">*</span>
                        </label>
                        <textarea
                            id="member_address"
                            name="address"
                            rows="3"
                            required
                            x-model="form.address"
                            class="mt-1 w-full rounded-md border-kk-border text-sm shadow-sm focus:border-kk-accent focus:ring-kk-accent/30"
                        ></textarea>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="member_postcode" class="block text-xs font-medium text-kk-sidebar-muted">
                                {{ __('Poskod') }}<span class="text-red-600" aria-hidden="true">*</span>
                            </label>
                            <input
                                id="member_postcode"
                                type="text"
                                name="postcode"
                                required
                                x-model="form.postcode"
                                class="mt-1 w-full rounded-md border-kk-border text-sm shadow-sm focus:border-kk-accent focus:ring-kk-accent/30"
                            />
                        </div>
                        <div>
                            <label for="member_city" class="block text-xs font-medium text-kk-sidebar-muted">{{ __('Bandar') }}</label>
                            <input
                                id="member_city"
                                type="text"
                                name="city"
                                x-model="form.city"
                                class="mt-1 w-full rounded-md border-kk-border text-sm shadow-sm focus:border-kk-accent focus:ring-kk-accent/30"
                            />
                        </div>
                    </div>
                    <div>
                        <label for="member_state_id" class="block text-xs font-medium text-kk-sidebar-muted">
                            {{ __('Negeri') }}<span class="text-red-600" aria-hidden="true">*</span>
                        </label>
                        <select
                            id="member_state_id"
                            name="state_id"
                            required
                            x-model="form.state_id"
                            class="mt-1 w-full rounded-md border-kk-border text-sm shadow-sm focus:border-kk-accent focus:ring-kk-accent/30"
                        >
                            <option value="">{{ __('— Pilih —') }}</option>
                            @foreach ($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="member_latitude" class="block text-xs font-medium text-kk-sidebar-muted">{{ __('Latitud') }}</label>
                            <input
                                id="member_latitude"
                                type="text"
                                name="latitude"
                                x-model="form.latitude"
                                class="mt-1 w-full rounded-md border-kk-border text-sm shadow-sm focus:border-kk-accent focus:ring-kk-accent/30"
                            />
                        </div>
                        <div>
                            <label for="member_longitude" class="block text-xs font-medium text-kk-sidebar-muted">{{ __('Longitud') }}</label>
                            <input
                                id="member_longitude"
                                type="text"
                                name="longitude"
                                x-model="form.longitude"
                                class="mt-1 w-full rounded-md border-kk-border text-sm shadow-sm focus:border-kk-accent focus:ring-kk-accent/30"
                            />
                        </div>
                    </div>
                    <div>
                        <label for="member_property_relationship" class="block text-xs font-medium text-kk-sidebar-muted">
                            {{ __('Hubungan dengan harta') }}<span class="text-red-600" aria-hidden="true">*</span>
                        </label>
                        <select
                            id="member_property_relationship"
                            name="property_relationship"
                            required
                            x-model="form.property_relationship"
                            class="mt-1 w-full rounded-md border-kk-border text-sm shadow-sm focus:border-kk-accent focus:ring-kk-accent/30"
                        >
                            <option value="">{{ __('— Pilih —') }}</option>
                            <option value="owner">{{ __('Pemilik') }}</option>
                            <option value="tenant">{{ __('Penyewa') }}</option>
                            <option value="family_member">{{ __('Ahli keluarga') }}</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <input
                            id="member_is_voting_eligible"
                            type="checkbox"
                            name="is_voting_eligible"
                            value="1"
                            class="rounded border-kk-border text-kk-accent focus:ring-kk-accent/30"
                            :checked="form.is_voting_eligible === true || form.is_voting_eligible === 1"
                            @change="form.is_voting_eligible = $event.target.checked"
                        />
                        <label for="member_is_voting_eligible" class="text-sm text-kk-sidebar-text">{{ __('Layak mengundi') }}</label>
                    </div>
                    <div class="flex flex-wrap justify-end gap-2 border-t border-kk-border pt-4">
                        <button
                            type="button"
                            class="inline-flex items-center rounded-md border border-kk-border bg-kk-surface-muted px-3 py-2 text-sm font-medium text-kk-sidebar-text hover:bg-kk-sidebar-hover"
                            @click="closeEdit()"
                        >{{ __('Batal') }}</button>
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-md bg-kk-nav-from px-3 py-2 text-sm font-medium text-kk-nav-fg shadow-sm hover:bg-kk-nav-to"
                        >{{ __('Simpan') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
