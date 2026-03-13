<x-mail::message>
# Halo, {{ $assignee->name }}

Laporan tugas Anda telah **ditinjau** oleh {{ $organizer->name }} dan memerlukan **revisi**.

---

**Detail Tindak Lanjut:**

| | |
|---|---|
| **Judul Tugas** | {{ $actionItem->title }} |
| **Prioritas** | {{ $actionItem->priority_label }} |
| **Meeting Asal** | {{ $actionItem->meeting->title ?? '-' }} |
| **Ditolak oleh** | {{ $organizer->name }} |

---

**Catatan Revisi dari {{ $organizer->name }}:**

> {{ $revisionNotes }}

Silakan perbaiki sesuai catatan di atas, upload ulang file bukti, lalu kirim laporan kembali.

<x-mail::button :url="url('/action-items/' . $actionItem->id)" color="error">
Lihat & Perbaiki Tugas
</x-mail::button>

Terima kasih,
{{ config('app.name') }}
</x-mail::message>
