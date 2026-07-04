@extends('layouts.app')
@section('title', 'Notifikasi')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Notifikasi</h3>

        @if($notifications->where('read_at', null)->count() > 0)
            <form action="{{ route('member.notifications.readAll') }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-outline-primary">
                    Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            @forelse($notifications as $notification)
                <div class="border-bottom py-3">
                    <h6>
                        {{ $notification->data['title'] ?? 'Notifikasi' }}
                        @if(!$notification->read_at)
                            <span class="badge bg-danger">
                            Baru
                        </span>
                        @endif
                    </h6>
                    <p class="mb-2">
                        {{ $notification->data['message'] ?? '-' }}
                    </p>
                    <small class="text-muted">
                        {{ $notification->created_at->diffForHumans() }}
                    </small>

                    @if(!$notification->read_at)
                        <form
                            action="{{ route('member.notifications.read', $notification->id) }}"
                            method="POST"
                            class="mt-2">
                            @csrf
                            <button class="btn btn-sm btn-primary">
                                Tandai Dibaca
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="text-center py-5">
                    Tidak ada notifikasi.
                </div>
            @endforelse
        </div>
    </div>
    <div class="mt-3">
        {{ $notifications->links() }}
    </div>
@endsection
