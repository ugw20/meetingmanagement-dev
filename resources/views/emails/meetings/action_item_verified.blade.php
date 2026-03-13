<x-mail::message>
# Halo, {{ $assignee->name }}

Laporan tugas Anda telah **diverifikasi dan diterima** oleh {{ $organizer->name }}.

---

**Detail Tindak Lanjut:**

| | |
|---|---|
| **Judul Tugas** | {{ $actionItem->title }} |
| **Prioritas** | {{ $actionItem->priority_label }} |
| **Meeting Asal** | {{ $actionItem->meeting->title ?? '-' }} |
| **Diverifikasi oleh** | {{ $organizer->name }} |
| **Tanggal Verifikasi** | {{ now()->format('l, d F Y H:i') }} |

Tugas ini telah **ditutup** dan dinyatakan selesai. Terima kasih atas kerja keras Anda!

<x-mail::button :url="url('/action-items/' . $actionItem->id)" color="success">
Lihat Detail Tugas
</x-mail::button>

Terima kasih,
{{ config('app.name') }}
</x-mail::message>
