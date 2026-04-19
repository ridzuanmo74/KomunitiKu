<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight">
            {{ __('Maklumat Persatuan') }}
        </h2>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-2 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <div
        class="space-y-4"
        x-data="{
            detailOpen: false,
            detail: {},
            openDetail(d) {
                this.detail = d;
                this.detailOpen = true;
                document.body.classList.add('overflow-y-hidden');
            },
            closeDetail() {
                this.detailOpen = false;
                this.detail = {};
                document.body.classList.remove('overflow-y-hidden');
            },
        }"
        @keydown.escape.window="if (detailOpen) closeDetail()"
    >
        <div class="kk-card p-4">
            <form method="get" action="{{ route('committee.associations.info') }}" class="flex flex-col gap-4 lg:flex-row lg:flex-wrap lg:items-end">
                <div class="min-w-[12rem] flex-1">
                    <label for="committee_association_search" class="block text-xs font-medium text-kk-sidebar-muted">{{ __('Cari persatuan') }}</label>
                    <input
                        id="committee_association_search"
                        type="search"
                        name="q"
                        value="{{ $searchQuery }}"
                        placeholder="{{ __('Nama, kod, bandar, ROS…') }}"
                        class="mt-1 w-full rounded-md border-kk-border text-sm shadow-sm focus:border-kk-accent focus:ring-kk-accent/30"
                    />
                </div>
                <div>
                    <label for="committee_association_per_page" class="block text-xs font-medium text-kk-sidebar-muted">{{ __('Rekod setiap halaman') }}</label>
                    <select
                        id="committee_association_per_page"
                        name="per_page"
                        class="mt-1 rounded-md border-kk-border text-sm shadow-sm focus:border-kk-accent focus:ring-kk-accent/30"
                        onchange="this.form.submit()"
                    >
                        @foreach ([10, 25, 50, 100] as $size)
                            <option value="{{ $size }}" @selected((int) $perPage === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="inline-flex items-center rounded-md bg-kk-nav-from px-3 py-2 text-sm font-medium text-kk-nav-fg shadow-sm hover:bg-kk-nav-to">
                        {{ __('Cari') }}
                    </button>
                    @if ($searchQuery !== '')
                        <a
                            href="{{ route('committee.associations.info', array_filter(['association' => $association?->id, 'per_page' => $perPage])) }}"
                            class="inline-flex items-center rounded-md border border-kk-border bg-kk-surface-muted px-3 py-2 text-sm font-medium text-kk-sidebar-text hover:bg-kk-sidebar-hover"
                        >{{ __('Set semula') }}</a>
                    @endif
                    @if ($canManageRegistry)
                        <a
                            href="{{ route('committee.associations.create') }}"
                            class="inline-flex items-center rounded-md bg-kk-accent px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-600"
                        >{{ __('Daftar persatuan baharu') }}</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="kk-card overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-kk-border/60 text-sm">
                    <thead class="bg-kk-sidebar-hover/80">
                        <tr>
                            <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">{{ __('Nama') }}</th>
                            <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">{{ __('Kod') }}</th>
                            <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">{{ __('Status') }}</th>
                            <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">{{ __('Negeri') }}</th>
                            <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">{{ __('Bandar') }}</th>
                            <th scope="col" class="relative px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">
                                <span class="sr-only">{{ __('Tindakan') }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-kk-border/40 bg-kk-surface">
                        @forelse ($associationList as $row)
                            @php
                                $detailPayload = [
                                    'name' => $row->name,
                                    'code' => $row->code,
                                    'status_label' => $row->is_active ? __('Aktif') : __('Tidak aktif'),
                                    'description' => $row->description ?: '—',
                                    'ros_registration_number' => $row->ros_registration_number ?: '—',
                                    'established_date' => $row->established_date?->format('Y-m-d') ?: '—',
                                    'address' => $row->address ?: '—',
                                    'postcode' => $row->postcode ?: '—',
                                    'city' => $row->city ?: '—',
                                    'state_name' => $row->state?->name ?: '—',
                                    'phone' => $row->phone ?: '—',
                                    'official_email' => $row->official_email ?: '—',
                                    'latitude' => $row->latitude !== null ? (string) $row->latitude : '—',
                                    'longitude' => $row->longitude !== null ? (string) $row->longitude : '—',
                                    'map_url' => $row->latitude !== null && $row->longitude !== null
                                        ? 'https://www.openstreetmap.org/?mlat='.$row->latitude.'&mlon='.$row->longitude.'#map=16/'.$row->latitude.'/'.$row->longitude
                                        : null,
                                ];
                            @endphp
                            <tr @class(['hover:bg-kk-sidebar-hover/50', 'bg-kk-accent-soft/70' => $association && $association->id === $row->id])>
                                <td class="whitespace-nowrap px-4 py-2.5 font-medium text-gray-900">{{ $row->name }}</td>
                                <td class="whitespace-nowrap px-4 py-2.5 text-gray-700">{{ $row->code }}</td>
                                <td class="whitespace-nowrap px-4 py-2.5 text-gray-700">{{ $row->is_active ? __('Aktif') : __('Tidak aktif') }}</td>
                                <td class="whitespace-nowrap px-4 py-2.5 text-gray-700">{{ $row->state?->name ?: '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-2.5 text-gray-700">{{ $row->city ?: '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-2.5 text-right text-gray-700">
                                    <div class="inline-flex items-center justify-end gap-1">
                                        <button
                                            type="button"
                                            class="inline-flex rounded p-1.5 text-indigo-600 hover:bg-indigo-50 hover:text-indigo-800"
                                            title="{{ __('Lihat butiran') }}"
                                            @click="openDetail(@js($detailPayload))"
                                        >
                                            <span class="sr-only">{{ __('Lihat') }}</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </button>
                                        <a
                                            href="{{ route('committee.associations.members', ['association' => $row->id]) }}"
                                            class="inline-flex rounded p-1.5 text-teal-700 hover:bg-teal-50 hover:text-teal-900"
                                            title="{{ __('Senarai ahli') }}"
                                        >
                                            <span class="sr-only">{{ __('Senarai ahli') }}</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.433-5.592M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                            </svg>
                                        </a>
                                        @if ($canManageRegistry)
                                            <a
                                                href="{{ route('committee.associations.edit', $row) }}"
                                                class="inline-flex rounded p-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900"
                                                title="{{ __('Sunting') }}"
                                            >
                                                <span class="sr-only">{{ __('Sunting') }}</span>
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                            </a>
                                            <form
                                                method="post"
                                                action="{{ route('committee.associations.destroy', $row) }}"
                                                class="inline"
                                                onsubmit="return confirm(@json(__('Padam persatuan ini? Tindakan tidak boleh diundur.')));"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="inline-flex rounded p-1.5 text-red-600 hover:bg-red-50 hover:text-red-800"
                                                    title="{{ __('Padam') }}"
                                                >
                                                    <span class="sr-only">{{ __('Padam') }}</span>
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                                    @if ($associationList->total() === 0 && $searchQuery === '')
                                        @if ($canManageRegistry)
                                            <p>{{ __('Tiada persatuan berdaftar.') }}</p>
                                            <a
                                                href="{{ route('committee.associations.create') }}"
                                                class="mt-2 inline-block font-medium text-indigo-600 hover:text-indigo-800"
                                            >{{ __('Daftar persatuan baharu') }}</a>
                                        @else
                                            {{ __('Tiada persatuan dikaitkan untuk peranan anda.') }}
                                        @endif
                                    @else
                                        {{ __('Tiada persatuan sepadan dengan carian anda.') }}
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($associationList->hasPages())
                <div class="border-t border-kk-border bg-kk-surface-muted px-4 py-3">
                    {{ $associationList->links() }}
                </div>
            @endif
        </div>

        <div
            x-show="detailOpen"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
            style="display: none;"
            role="dialog"
            aria-modal="true"
            aria-labelledby="association-detail-title"
        >
            <div
                class="kk-modal-backdrop fixed inset-0 transition-opacity"
                x-show="detailOpen"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="closeDetail()"
            ></div>

            <div
                class="kk-modal-surface relative mx-auto mb-6 max-h-[min(90vh,42rem)] w-full max-w-2xl overflow-y-auto"
                x-show="detailOpen"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-3 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-3 sm:translate-y-0 sm:scale-95"
                @click.stop
            >
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-kk-border bg-gradient-to-r from-kk-modal-from to-kk-modal-to px-4 py-3">
                    <h3 id="association-detail-title" class="text-base font-semibold text-kk-nav-fg" x-text="detail.name"></h3>
                    <button
                        type="button"
                        class="rounded-md p-2 text-kk-nav-muted hover:bg-white/10 hover:text-kk-nav-fg"
                        @click="closeDetail()"
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
                            <dd class="font-medium text-gray-900" x-text="detail.name"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('Kod') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="detail.code"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('Status') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="detail.status_label"></dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-gray-500">{{ __('Penerangan') }}</dt>
                            <dd class="whitespace-pre-wrap text-gray-900" x-text="detail.description"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('No. pendaftaran ROS') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="detail.ros_registration_number"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('Tarikh ditubuhkan') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="detail.established_date"></dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-gray-500">{{ __('Alamat') }}</dt>
                            <dd class="whitespace-pre-wrap text-gray-900" x-text="detail.address"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('Poskod') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="detail.postcode"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('Bandar') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="detail.city"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('Negeri') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="detail.state_name"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('Telefon / hotline') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="detail.phone"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('Emel rasmi') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="detail.official_email"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('Latitud') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="detail.latitude"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ __('Longitud') }}</dt>
                            <dd class="font-medium text-gray-900" x-text="detail.longitude"></dd>
                        </div>
                    </dl>
                    <p class="mt-4 text-sm" x-show="detail.map_url">
                        <a
                            :href="detail.map_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="font-medium text-indigo-600 hover:text-indigo-800"
                        >{{ __('Lihat di peta (OpenStreetMap)') }}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
