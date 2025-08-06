{{-- resources/views/components/post-card.blade.php --}}
@props(['post', 'showAuthor' => true, 'showActions' => false])

<article class="post-card" style="border-bottom: 1px solid var(--stone-200); padding-bottom: 30px; margin-bottom: 30px;">
    @if($post->image)
        <div style="margin-bottom: 20px;">
            <img src="{{ $post->image_url }}" alt="{{ $post->title }}" 
                 style="width: 100%; max-height: 400px; object-fit: cover; border-radius: 12px;">
        </div>
    @endif

    @if($showAuthor)
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
            <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->display_name }}" class="user-avatar">
            <div>
                <strong style="color: var(--stone-800); font-family: 'Merriweather', serif;">
                    <a href="{{ route('user.profile', $post->user->username) }}" style="text-decoration: none; color: inherit;">
                        {{ $post->user->display_name }}
                    </a>
                </strong>
                <div style="color: var(--stone-500); font-size: 14px;">
                    {{ $post->time_ago }}
                    @if(!$post->is_published)
                        <span style="background-color: #fbbf24; color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px; margin-left: 8px;">
                            DRAFT
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <h3 style="margin-bottom: 15px;">
        <a href="{{ route('posts.show', $post) }}" 
           style="text-decoration: none; color: var(--stone-800);">
            {{ $post->title }}
        </a>
    </h3>

    <p style="color: var(--stone-600); line-height: 1.7; margin-bottom: 15px;">
        {{ $post->excerpt }}
    </p>

    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 15px;">
            <button class="btn btn-secondary" style="padding: 8px 16px; font-size: 14px;" disabled>
                <i class="fas fa-heart"></i> Like
            </button>
            <button class="btn btn-secondary" style="padding: 8px 16px; font-size: 14px;" disabled>
                <i class="fas fa-comment"></i> Comment
            </button>
            <a href="{{ route('posts.show', $post) }}" class="btn btn-secondary" style="padding: 8px 16px; font-size: 14px;">
                Read More
            </a>
        </div>
        
        @if($showActions && $post->user_id === auth()->id())
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('posts.edit', $post) }}" class="btn btn-secondary" style="padding: 8px 16px; font-size: 14px;">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form method="POST" action="{{ route('posts.destroy', $post) }}" 
                      style="display: inline;" 
                      onsubmit="return confirm('Are you sure you want to delete this post?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-secondary" style="background-color: #dc2626; color: white; padding: 8px 16px; font-size: 14px;">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        @endif
    </div>
</article>