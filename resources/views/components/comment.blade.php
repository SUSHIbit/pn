{{-- resources/views/components/comment.blade.php --}}
@props(['comment'])

<div class="comment" style="display: flex; gap: 15px; padding: 15px 0; border-bottom: 1px solid var(--stone-200);">
    <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->display_name }}" 
         style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; flex-shrink: 0;">
    
    <div style="flex: 1;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
            <strong style="color: var(--stone-800); font-size: 14px;">
                <a href="{{ route('user.profile', $comment->user->username) }}" 
                   style="text-decoration: none; color: inherit;">
                    {{ $comment->user->display_name }}
                </a>
            </strong>
            <span style="color: var(--stone-500); font-size: 13px;">
                {{ $comment->time_ago }}
            </span>
        </div>
        
        <p style="color: var(--stone-700); font-size: 14px; line-height: 1.5; margin-bottom: 8px;">
            {{ $comment->content }}
        </p>
        
        @auth
            @if($comment->user_id === auth()->id() || $comment->post->user_id === auth()->id())
                <div style="display: flex; gap: 10px;">
                    @if($comment->user_id === auth()->id())
                        <button class="edit-comment-btn" 
                                style="background: none; border: none; color: var(--stone-500); font-size: 12px; cursor: pointer;"
                                data-comment-id="{{ $comment->id }}">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    @endif
                    
                    <form method="POST" action="{{ route('comments.destroy', $comment) }}" 
                          style="display: inline;"
                          onsubmit="return confirm('Are you sure you want to delete this comment?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                style="background: none; border: none; color: #dc2626; font-size: 12px; cursor: pointer;">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            @endif
        @endauth
    </div>
</div>