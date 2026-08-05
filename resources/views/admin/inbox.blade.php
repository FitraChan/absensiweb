@extends('layout.main-admin')
@section('tittle-admin')
  Pengaturan
@endsection
@section('content-admin')


<div class="container">
       <h2>Inbox</h2>

       @if($messages->isEmpty())


           <p>Tidak ada pesan masuk.</p>
       @else
       <ul class="list-group">
            @foreach($messages as $message)
                <li class="list-group-item">
                    <a href="{{ route('messages.read', $message->id) }}" class="{{ $message->is_read ? 'text-muted' : 'font-weight-bold' }}">
                        <strong>{{ $message->sender->name }}:</strong> {{ $message->message }}
                    </a>
                    <span class="badge badge-{{ $message->is_read ? 'success' : 'danger' }}">
                        {{ $message->is_read ? 'Dibaca' : 'Belum Dibaca' }}
                    </span>
                </li>
            @endforeach
        </ul>
       @endif
   </div>
@endsection
