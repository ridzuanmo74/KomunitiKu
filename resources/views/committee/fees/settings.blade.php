<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold leading-tight">
            {{ __('Tetapan Yuran') }}
        </h2>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="kk-card overflow-hidden p-0">
        <div class="flex items-center justify-between border-b border-kk-border px-4 py-3 text-sm text-kk-sidebar-text">
            <span>{{ __('Persatuan') }}: {{ $association?->name ?: __('Tiada') }}</span>
            <button
                type="button"
                data-open-modal="create-fee-modal"
                class="inline-flex items-center rounded-md bg-kk-accent px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-teal-600"
            >
                {{ __('Tambah Yuran Baharu') }}
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-kk-border/60 text-sm">
                <thead class="bg-kk-sidebar-hover/80">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">{{ __('Nama Yuran') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">{{ __('Kekerapan') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">{{ __('Hari Jatuh Tempo') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">{{ __('Amaun') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">{{ __('Status') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-kk-sidebar-muted">{{ __('Tindakan') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-kk-border/40 bg-kk-surface">
            @forelse ($fees as $fee)
                    <tr class="hover:bg-kk-sidebar-hover/50">
                        <td class="px-4 py-2.5 font-medium text-gray-900">{{ $fee->name }}</td>
                        <td class="px-4 py-2.5 text-gray-700">{{ __($fee->frequencyLabel()) }}</td>
                        <td class="px-4 py-2.5 text-gray-700">{{ $fee->due_day ?: '-' }}</td>
                        <td class="px-4 py-2.5 text-gray-700">RM {{ number_format((float) $fee->amount, 2) }}</td>
                        <td class="px-4 py-2.5 text-gray-700">{{ $fee->is_active ? __('Aktif') : __('Tidak Aktif') }}</td>
                        <td class="px-4 py-2.5 text-gray-700">
                            <div class="inline-flex items-center justify-end gap-1">
                                <button
                                    type="button"
                                    data-open-modal="edit-fee-modal-{{ $fee->id }}"
                                    class="inline-flex rounded p-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900"
                                    title="{{ __('Kemaskini') }}"
                                >
                                    <span class="sr-only">{{ __('Kemaskini') }}</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </button>
                                <form action="{{ route('committee.fees.destroy', $fee) }}" method="POST" onsubmit="return confirm('{{ __('Padam yuran ini?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex rounded p-1.5 text-red-600 hover:bg-red-50 hover:text-red-800" title="{{ __('Padam') }}">
                                        <span class="sr-only">{{ __('Padam') }}</span>
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
            @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">{{ __('Tiada yuran didaftarkan.') }}</td>
                    </tr>
            @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="create-fee-modal" data-modal class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="kk-modal-backdrop fixed inset-0" data-close-modal></div>
        <div class="kk-modal-surface relative w-full max-w-3xl overflow-hidden">
            <div class="flex items-center justify-between border-b border-kk-border bg-gradient-to-r from-kk-modal-from to-kk-modal-to px-4 py-3">
                <h3 class="text-sm font-semibold text-kk-nav-fg">{{ __('Tambah Yuran Baharu') }}</h3>
                <button type="button" data-close-modal class="rounded-md p-1.5 text-kk-nav-muted hover:bg-white/10 hover:text-kk-nav-fg">&times;</button>
            </div>
            <form action="{{ route('committee.fees.store') }}" method="POST" class="grid gap-4 px-4 py-4 md:grid-cols-2">
                @csrf
                <input type="hidden" name="form_context" value="create" />
                <div>
                    <label for="create-name" class="mb-1 block text-sm font-medium text-gray-700">{{ __('Nama Yuran') }}</label>
                    <input id="create-name" name="name" type="text" value="{{ old('name') }}" required data-autofocus class="w-full rounded-md border-kk-border text-sm shadow-sm focus:border-kk-accent focus:ring-kk-accent/30" />
                    @if (old('form_context') !== 'edit')
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    @endif
                </div>
                <div>
                    <label for="create-amount" class="mb-1 block text-sm font-medium text-gray-700">{{ __('Amaun (RM)') }}</label>
                    <input id="create-amount" name="amount" type="number" min="1" step="0.01" value="{{ old('amount') }}" required class="w-full rounded-md border-kk-border text-sm shadow-sm focus:border-kk-accent focus:ring-kk-accent/30" />
                    @if (old('form_context') !== 'edit')
                        @error('amount')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    @endif
                </div>
                <div>
                    <label for="create-frequency" class="mb-1 block text-sm font-medium text-gray-700">{{ __('Kekerapan') }}</label>
                    <select id="create-frequency" name="frequency" data-frequency-select class="w-full rounded-md border-kk-border text-sm shadow-sm focus:border-kk-accent focus:ring-kk-accent/30">
                        <option value="one_time" @selected(old('frequency') === 'one_time')>{{ __('Sekali Bayar') }}</option>
                        <option value="monthly" @selected(old('frequency') === 'monthly')>{{ __('Bulanan') }}</option>
                        <option value="yearly" @selected(old('frequency', 'yearly') === 'yearly')>{{ __('Tahunan') }}</option>
                    </select>
                    @if (old('form_context') !== 'edit')
                        @error('frequency')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    @endif
                </div>
                <div data-due-day-wrapper>
                    <label for="create-due-day" class="mb-1 block text-sm font-medium text-gray-700">{{ __('Hari Jatuh Tempo (1-31)') }}</label>
                    <input id="create-due-day" name="due_day" type="number" min="1" max="31" value="{{ old('due_day') }}" class="w-full rounded-md border-kk-border text-sm shadow-sm focus:border-kk-accent focus:ring-kk-accent/30" />
                    @if (old('form_context') !== 'edit')
                        @error('due_day')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    @endif
                </div>
                <div>
                    <label for="create-is-active" class="mb-1 block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                    <select id="create-is-active" name="is_active" class="w-full rounded-md border-kk-border text-sm shadow-sm focus:border-kk-accent focus:ring-kk-accent/30">
                        <option value="1" @selected(old('is_active', '1') === '1')>{{ __('Aktif') }}</option>
                        <option value="0" @selected(old('is_active') === '0')>{{ __('Tidak Aktif') }}</option>
                    </select>
                    @if (old('form_context') !== 'edit')
                        @error('is_active')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    @endif
                </div>
                <div class="md:col-span-2 flex justify-end gap-2">
                    <button type="button" data-close-modal class="rounded-md border border-kk-border bg-kk-surface-muted px-4 py-2 text-sm font-semibold text-kk-sidebar-text hover:bg-kk-sidebar-hover">{{ __('Batal') }}</button>
                    <button type="submit" class="rounded-md bg-kk-accent px-4 py-2 text-sm font-semibold text-white hover:bg-teal-600">{{ __('Simpan Yuran') }}</button>
                </div>
            </form>
        </div>
    </div>

    @foreach ($fees as $fee)
        @php
            $isEditingFeeWithErrors = old('form_context') === 'edit'
                && (string) old('editing_fee_id') === (string) $fee->id;
        @endphp
        <div id="edit-fee-modal-{{ $fee->id }}" data-modal class="fixed inset-0 z-50 hidden items-center justify-center p-4">
            <div class="kk-modal-backdrop fixed inset-0" data-close-modal></div>
            <div class="kk-modal-surface relative w-full max-w-3xl overflow-hidden">
                <div class="flex items-center justify-between border-b border-kk-border bg-gradient-to-r from-kk-modal-from to-kk-modal-to px-4 py-3">
                    <h3 class="text-sm font-semibold text-kk-nav-fg">{{ __('Kemaskini Yuran') }}</h3>
                    <button type="button" data-close-modal class="rounded-md p-1.5 text-kk-nav-muted hover:bg-white/10 hover:text-kk-nav-fg">&times;</button>
                </div>
                <form action="{{ route('committee.fees.update', $fee) }}" method="POST" class="grid gap-4 px-4 py-4 md:grid-cols-2">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="form_context" value="edit" />
                    <input type="hidden" name="editing_fee_id" value="{{ $fee->id }}" />
                    <div>
                        <label for="edit-name-{{ $fee->id }}" class="mb-1 block text-sm font-medium text-gray-700">{{ __('Nama Yuran') }}</label>
                        <input id="edit-name-{{ $fee->id }}" name="name" type="text" value="{{ old('name', $fee->name) }}" required data-autofocus class="w-full rounded-md border-kk-border text-sm shadow-sm focus:border-kk-accent focus:ring-kk-accent/30" />
                        @if ($isEditingFeeWithErrors)
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                    <div>
                        <label for="edit-amount-{{ $fee->id }}" class="mb-1 block text-sm font-medium text-gray-700">{{ __('Amaun (RM)') }}</label>
                        <input id="edit-amount-{{ $fee->id }}" name="amount" type="number" min="1" step="0.01" value="{{ old('amount', (float) $fee->amount) }}" required class="w-full rounded-md border-kk-border text-sm shadow-sm focus:border-kk-accent focus:ring-kk-accent/30" />
                        @if ($isEditingFeeWithErrors)
                            @error('amount')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                    <div>
                        <label for="edit-frequency-{{ $fee->id }}" class="mb-1 block text-sm font-medium text-gray-700">{{ __('Kekerapan') }}</label>
                        <select id="edit-frequency-{{ $fee->id }}" name="frequency" data-frequency-select class="w-full rounded-md border-kk-border text-sm shadow-sm focus:border-kk-accent focus:ring-kk-accent/30">
                            <option value="one_time" @selected((string) old('frequency', $fee->frequency) === 'one_time')>{{ __('Sekali Bayar') }}</option>
                            <option value="monthly" @selected((string) old('frequency', $fee->frequency) === 'monthly')>{{ __('Bulanan') }}</option>
                            <option value="yearly" @selected((string) old('frequency', $fee->frequency) === 'yearly')>{{ __('Tahunan') }}</option>
                        </select>
                        @if ($isEditingFeeWithErrors)
                            @error('frequency')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                    <div data-due-day-wrapper>
                        <label for="edit-due-day-{{ $fee->id }}" class="mb-1 block text-sm font-medium text-gray-700">{{ __('Hari Jatuh Tempo (1-31)') }}</label>
                        <input id="edit-due-day-{{ $fee->id }}" name="due_day" type="number" min="1" max="31" value="{{ old('due_day', $fee->due_day) }}" class="w-full rounded-md border-kk-border text-sm shadow-sm focus:border-kk-accent focus:ring-kk-accent/30" />
                        @if ($isEditingFeeWithErrors)
                            @error('due_day')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                    <div>
                        <label for="edit-is-active-{{ $fee->id }}" class="mb-1 block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                        <select id="edit-is-active-{{ $fee->id }}" name="is_active" class="w-full rounded-md border-kk-border text-sm shadow-sm focus:border-kk-accent focus:ring-kk-accent/30">
                            <option value="1" @selected((string) old('is_active', $fee->is_active ? '1' : '0') === '1')>{{ __('Aktif') }}</option>
                            <option value="0" @selected((string) old('is_active', $fee->is_active ? '1' : '0') === '0')>{{ __('Tidak Aktif') }}</option>
                        </select>
                        @if ($isEditingFeeWithErrors)
                            @error('is_active')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                    <div class="md:col-span-2 flex justify-end gap-2">
                        <button type="button" data-close-modal class="rounded-md border border-kk-border bg-kk-surface-muted px-4 py-2 text-sm font-semibold text-kk-sidebar-text hover:bg-kk-sidebar-hover">{{ __('Batal') }}</button>
                        <button type="submit" class="rounded-md bg-kk-nav-from px-4 py-2 text-sm font-semibold text-kk-nav-fg hover:bg-kk-nav-to">{{ __('Kemaskini') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    <script>
        const openModal = (modalId) => {
            const modal = document.getElementById(modalId);
            if (!modal) {
                return;
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            const autofocusInput = modal.querySelector('[data-autofocus]');
            if (autofocusInput instanceof HTMLElement) {
                autofocusInput.focus();
            }
        };

        const closeModal = (modal) => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };

        document.querySelectorAll('[data-open-modal]').forEach((button) => {
            button.addEventListener('click', () => openModal(button.dataset.openModal));
        });

        document.querySelectorAll('[data-modal]').forEach((modal) => {
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal(modal);
                }
            });
            modal.querySelectorAll('[data-close-modal]').forEach((closeButton) => {
                closeButton.addEventListener('click', () => closeModal(modal));
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') {
                return;
            }

            document.querySelectorAll('[data-modal]').forEach((modal) => {
                if (!modal.classList.contains('hidden')) {
                    closeModal(modal);
                }
            });
        });

        document.querySelectorAll('form').forEach((form) => {
            const frequency = form.querySelector('[data-frequency-select]');
            const dueDayWrapper = form.querySelector('[data-due-day-wrapper]');
            const dueDayInput = dueDayWrapper ? dueDayWrapper.querySelector('input[name="due_day"]') : null;

            if (!frequency || !dueDayWrapper || !dueDayInput) {
                return;
            }

            const syncDueDayVisibility = () => {
                const isMonthly = frequency.value === 'monthly';
                dueDayWrapper.classList.toggle('hidden', !isMonthly);
                dueDayInput.required = isMonthly;
                if (!isMonthly) {
                    dueDayInput.value = '';
                }
            };

            frequency.addEventListener('change', syncDueDayVisibility);
            syncDueDayVisibility();
        });

        @if ($errors->any())
            @if (old('form_context') === 'edit' && old('editing_fee_id'))
                openModal('edit-fee-modal-{{ old('editing_fee_id') }}');
            @else
                openModal('create-fee-modal');
            @endif
        @endif
    </script>
</x-app-layout>
