@extends('layouts.app')

@section('title', 'Semua Notifikasi')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-bell mr-2"></i>Semua Notifikasi</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Notifikasi</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Pusat Pemberitahuan</h3>
                        <div class="card-tools">
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-check-double mr-1"></i> Tandai Semua Dibaca
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($notifications->isEmpty())
                            <div class="text-center p-5">
                                <h4 class="text-muted"><i class="far fa-bell-slash mb-3" style="font-size: 3rem;"></i><br>Belum ada notifikasi apapun.</h4>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        @foreach($notifications as $notification)
                                            <tr class="{{ is_null($notification->read_at) ? 'bg-light font-weight-bold' : '' }}">
                                                <td width="5%" class="text-center align-middle">
                                                    <i class="fas {{ $notification->data['icon'] ?? 'fa-bell' }} {{ $notification->data['iconColor'] ?? 'text-primary' }} text-lg"></i>
                                                </td>
                                                <td>
                                                    <p class="mb-1 text-md">{{ $notification->data['title'] ?? 'Pemberitahuan Sistem' }}</p>
                                                    <p class="mb-0 text-sm {{ is_null($notification->read_at) ? 'text-dark' : 'text-muted' }}">
                                                        {{ $notification->data['message'] ?? 'Anda memiliki pemberitahuan baru.' }}
                                                    </p>
                                                </td>
                                                <td width="20%" class="text-right align-middle text-muted text-sm">
                                                    <i class="far fa-clock mr-1"></i> {{ $notification->created_at->diffForHumans() }}
                                                    <br>
                                                    <small>{{ $notification->created_at->format('d M Y, H:i') }}</small>
                                                </td>
                                                <td width="10%" class="text-center align-middle">
                                                    @if(isset($notification->data['url']) && $notification->data['url'])
                                                        <a href="{{ route('notifications.read', $notification->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                                            Aksi <i class="fas fa-arrow-right ml-1"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                    @if($notifications->hasPages())
                        <div class="card-footer clearfix">
                            <div class="float-right">
                                {{ $notifications->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
