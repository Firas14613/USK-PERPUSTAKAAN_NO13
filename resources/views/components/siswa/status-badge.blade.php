@props(['status'])

@php
    $map = [
        'pending' => ['bg' => 'bg-tertiary-fixed', 'text' => 'text-on-tertiary-fixed', 'label' => 'Pending'],
        'dipinjam' => ['bg' => 'bg-tertiary-fixed-dim', 'text' => 'text-on-tertiary-fixed-variant', 'label' => 'Dipinjam'],
        'terlambat' => ['bg' => 'bg-error-container', 'text' => 'text-on-error-container', 'label' => 'Terlambat'],
        'dikembalikan' => ['bg' => 'bg-secondary-fixed', 'text' => 'text-on-secondary-fixed', 'label' => 'Dikembalikan'],
        'ditolak' => ['bg' => 'bg-surface-container-highest', 'text' => 'text-outline', 'label' => 'Ditolak'],
        'hilang' => ['bg' => 'bg-error-container', 'text' => 'text-on-error-container', 'label' => 'Hilang'],
    ];

    $style = $map[$status] ?? ['bg' => 'bg-surface-container-high', 'text' => 'text-on-surface-variant', 'label' => ucfirst((string) $status)];
@endphp

<span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest {{ $style['bg'] }} {{ $style['text'] }}">
    {{ $style['label'] }}
</span>

