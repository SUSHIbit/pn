@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Following</h2>
    
    @foreach($users as $user)
        @include('components.user-card', ['user' => $user])
    @endforeach
    
    {{ $users->links() }}
</div>
@endsection