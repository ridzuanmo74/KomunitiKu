@php
    /** @var \App\Models\Association|null $association */
    $a = $association ?? null;
@endphp

<div class="space-y-4">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Nama') }} <span class="text-red-600">*</span></label>
        <input type="text" name="name" id="name" value="{{ old('name', $a?->name) }}" required
            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="code" class="block text-sm font-medium text-gray-700">{{ __('Kod') }} <span class="text-red-600">*</span></label>
        <input type="text" name="code" id="code" value="{{ old('code', $a?->code) }}" required
            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
        @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">{{ __('Penerangan') }}</label>
        <textarea name="description" id="description" rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $a?->description) }}</textarea>
        @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="flex items-center gap-2">
        <input type="hidden" name="is_active" value="0" />
        <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
            @checked(old('is_active', $a?->is_active ?? true)) />
        <label for="is_active" class="text-sm font-medium text-gray-700">{{ __('Persatuan aktif') }}</label>
        @error('is_active')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="ros_registration_number" class="block text-sm font-medium text-gray-700">{{ __('No. pendaftaran ROS') }}</label>
        <input type="text" name="ros_registration_number" id="ros_registration_number" value="{{ old('ros_registration_number', $a?->ros_registration_number) }}"
            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
        @error('ros_registration_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="established_date" class="block text-sm font-medium text-gray-700">{{ __('Tarikh ditubuhkan') }} <span class="text-red-600">*</span></label>
        <input type="date" name="established_date" id="established_date" value="{{ old('established_date', $a?->established_date?->format('Y-m-d')) }}"
            max="{{ now()->format('Y-m-d') }}" required
            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
        @error('established_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="address" class="block text-sm font-medium text-gray-700">{{ __('Alamat') }} <span class="text-red-600">*</span></label>
        <textarea name="address" id="address" rows="3" required minlength="5"
            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $a?->address) }}</textarea>
        @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="postcode" class="block text-sm font-medium text-gray-700">{{ __('Poskod') }} <span class="text-red-600">*</span></label>
            <input type="text" name="postcode" id="postcode" value="{{ old('postcode', $a?->postcode) }}" required
                inputmode="numeric" pattern="[0-9]{5}" minlength="5" maxlength="5" autocomplete="postal-code"
                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
            @error('postcode')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="city" class="block text-sm font-medium text-gray-700">{{ __('Bandar') }}</label>
            <input type="text" name="city" id="city" value="{{ old('city', $a?->city) }}"
                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
            @error('city')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
    <div>
        <label for="state_id" class="block text-sm font-medium text-gray-700">{{ __('Negeri') }} <span class="text-red-600">*</span></label>
        <select name="state_id" id="state_id" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">{{ __('— Pilih —') }}</option>
            @foreach ($states as $state)
                <option value="{{ $state->id }}" @selected((string) old('state_id', $a?->state_id) === (string) $state->id)>{{ $state->name }}</option>
            @endforeach
        </select>
        @error('state_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="phone" class="block text-sm font-medium text-gray-700">{{ __('Telefon / hotline') }} <span class="text-red-600">*</span></label>
        <input type="text" name="phone" id="phone" value="{{ old('phone', $a?->phone) }}" required autocomplete="tel"
            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
        @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="official_email" class="block text-sm font-medium text-gray-700">{{ __('Emel rasmi') }} <span class="text-red-600">*</span></label>
        <input type="email" name="official_email" id="official_email" value="{{ old('official_email', $a?->official_email) }}" required autocomplete="email"
            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
        @error('official_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="latitude" class="block text-sm font-medium text-gray-700">{{ __('Latitud') }}</label>
            <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $a?->latitude) }}" inputmode="decimal"
                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
            @error('latitude')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="longitude" class="block text-sm font-medium text-gray-700">{{ __('Longitud') }}</label>
            <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $a?->longitude) }}" inputmode="decimal"
                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
            @error('longitude')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
    @error('association')
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
