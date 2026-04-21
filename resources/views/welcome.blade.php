<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'KomunitiKu') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <header class="sticky top-0 z-50 border-b border-teal-100 bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
            <a href="#utama" class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-teal-600 to-cyan-600 text-sm font-bold text-white shadow-sm">KK</span>
                <span class="leading-tight">
                    <span class="block text-base font-semibold tracking-tight text-slate-900">KomunitiKu</span>
                    <span class="block text-xs font-medium text-teal-700">Digital Community Management</span>
                </span>
            </a>

            <div class="flex items-center gap-2">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 rounded-md bg-teal-700 px-4 py-2 text-sm font-medium text-white hover:bg-teal-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M3 13.5L12 6l9 7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M6.75 11.25V19.5h10.5v-8.25" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M15.75 8.25V5.625A2.625 2.625 0 0013.125 3h-6.75A2.625 2.625 0 003.75 5.625v12.75A2.625 2.625 0 006.375 21h6.75a2.625 2.625 0 002.625-2.625V15.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 12h11.25m0 0l-3-3m3 3l-3 3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Sign In</span>
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-md border border-teal-700 bg-teal-700 px-4 py-2 text-sm font-medium text-white hover:bg-teal-600" style="background-color:#0f766e;color:#ffffff;border-color:#0f766e;">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 4.5v15m7.5-7.5h-15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>Sign Up</span>
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <main>
        <section id="utama" class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
            <div class="max-w-3xl">
                <p class="mb-3 inline-block rounded-full bg-teal-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-teal-800">
                    Platform Pengurusan Persatuan
                </p>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                    Sistem digital korporat untuk urus ahli, aktiviti, pengumuman dan yuran.
                </h1>
                <p class="mt-4 text-base leading-7 text-slate-600 sm:text-lg">
                    KomunitiKu membantu persatuan beroperasi lebih tersusun dengan kawalan akses berasaskan peranan, rekod kehadiran, dan aliran bayaran yang jelas.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="rounded-md bg-teal-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-teal-600">
                            Mula Sekarang
                        </a>
                    @endif
                    <a href="#ciri" class="rounded-md border border-teal-200 bg-white px-5 py-2.5 text-sm font-semibold text-teal-800 hover:bg-teal-50">
                        Lihat Ciri
                    </a>
                </div>
            </div>
        </section>

        <section id="ciri" class="border-y border-slate-200 bg-white">
            <div class="mx-auto grid max-w-6xl gap-6 px-4 py-14 sm:px-6 lg:grid-cols-3 lg:px-8">
                <article class="rounded-xl border border-slate-200 p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-slate-900">Pengurusan Ahli</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Simpan profil ahli, status keahlian, dan struktur persatuan secara teratur.</p>
                </article>
                <article class="rounded-xl border border-slate-200 p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-slate-900">Aktiviti & Kehadiran</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Rancang aktiviti, hebahkan maklumat, dan rekod kehadiran ahli dengan pantas.</p>
                </article>
                <article class="rounded-xl border border-slate-200 p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-slate-900">Yuran & Bayaran</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Tetapkan yuran persatuan, semak transaksi, dan jejak bayaran secara telus.</p>
                </article>
            </div>
        </section>

        <section id="peranan" class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">Peranan Akses (RBAC)</h2>
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <p class="text-sm font-semibold text-slate-900">super_admin</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-slate-600">
                        <li>Akses penuh merentas semua persatuan</li>
                        <li>Kawalan global sistem</li>
                    </ul>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <p class="text-sm font-semibold text-slate-900">jawatankuasa</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-slate-600">
                        <li>Urus aktiviti, pengumuman, keahlian, dan yuran persatuan sendiri</li>
                    </ul>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <p class="text-sm font-semibold text-slate-900">pengerusi</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-slate-600">
                        <li>Maklumat persatuan, senarai ahli, dan kelulusan permohonan keahlian</li>
                    </ul>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <p class="text-sm font-semibold text-slate-900">setiausaha</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-slate-600">
                        <li>Skop pengerusi serta pengurusan pengumuman (API)</li>
                    </ul>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <p class="text-sm font-semibold text-slate-900">bendahari</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-slate-600">
                        <li>Jenis yuran, invois, semakan bayaran, dan tunggakan</li>
                    </ul>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <p class="text-sm font-semibold text-slate-900">ahli</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-slate-600">
                        <li>Baca maklumat persatuan</li>
                        <li>Daftar kehadiran dan buat bayaran yuran</li>
                    </ul>
                </div>
            </div>
        </section>
    </main>

    <footer id="hubungi" class="border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl flex-col gap-2 px-4 py-6 text-sm text-slate-600 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <p>&copy; {{ now()->year }} KomunitiKu. Hak cipta terpelihara.</p>
            <div class="flex gap-4">
                <a href="#" class="hover:text-slate-900">Privasi</a>
                <a href="#" class="hover:text-slate-900">Terma</a>
            </div>
        </div>
    </footer>
</body>
</html>
