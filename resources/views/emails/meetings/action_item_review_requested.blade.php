<x-mail::message>
# Halo, {{ $organizer->name }}

**{{ $reporter->name }}** telah menyelesaikan tugas dan meminta review dari Anda.

---

**Detail Tindak Lanjut:**

| | |
|---|---|
| **Judul Tugas** | {{ $actionItem->title }} |
| **Prioritas** | {{ $actionItem->priority_label }} |
| **Batas Waktu** | {{ \Carbon\Carbon::parse($actionItem->due_date)->format('l, d F Y') }} |
| **Meeting Asal** | {{ $actionItem->meeting->title ?? '-' }} |

**Deskripsi Tugas:**
{{ $actionItem->description }}

@if($actionItem->completion_notes)

**Catatan Penyelesaian dari Pelapor:**
{{ $actionItem->completion_notes }}

@endif

---

@if($files->count() > 0)
**File Lampiran ({{ $files->count() }} file):**

File-file di bawah sudah **terlampir langsung di email ini** — buka dari panel lampiran di email client Anda.

@foreach($files as $file)
- 📎 **{{ $file->file_name }}** ({{ $file->file_size_formatted }})
@if($file->description)
  Keterangan: {{ $file->description }}
@endif

  [⬇ Download {{ $file->file_name }}]({{ route('action-items.download-file', [$actionItem->id, $file->id]) }})
@endforeach

@else
_Pelapor tidak melampirkan file apapun._
@endif

---

Silakan periksa lampiran dan lakukan verifikasi melalui sistem.

<x-mail::button :url="url('/action-items/' . $actionItem->id)">
Lihat & Review Tugas
</x-mail::button>

Terima kasih,
{{ config('app.name') }}
</x-mail::message>
