<x-mail::message>
# Halo, {{ $participant->name }}

Ada pembaruan pada **Tindak Lanjut** Anda dari meeting **{{ $meeting->title }}**.

**Judul Tugas:** {{ $actionItem->title }}<br>
**Status:** {{ ucfirst($actionItem->status) }}<br>
**Prioritas:** {{ $actionItem->priority_label }}<br>
**Batas Waktu:** {{ \Carbon\Carbon::parse($actionItem->due_date)->format('l, d F Y') }}<br>

**Deskripsi:**
{{ $actionItem->description }}

@if($actionItem->completion_notes)
**Catatan Penyelesaian:**
{{ $actionItem->completion_notes }}
@endif

Silakan periksa detailnya melalui sistem.

<x-mail::button :url="url('/action-items/'.$actionItem->id)">
Lihat Detail Tugas
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
