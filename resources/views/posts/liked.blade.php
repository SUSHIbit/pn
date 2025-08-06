@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Posts You've Liked</h2>
    
    @if($posts->count() > 0)
        @foreach($posts as $post)
            @include('components.post-card', ['post' => $post, 'showAuthor' => true])
        @endforeach
        
        {{ $posts->links() }}
    @else
        <p>You haven't liked any posts yet.</p>
    @endif
</div>
@endsection