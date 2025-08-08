@extends('layouts.app')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; color: var(--stone-800);">Advanced Search</h2>
        <a href="{{ route('search') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Search
        </a>
    </div>

    <div style="background-color: var(--stone-50); padding: 20px; border-radius: 12px; margin-bottom: 30px;">
        <h3 style="color: var(--stone-700); margin-bottom: 10px;">Find exactly what you're looking for</h3>
        <p style="color: var(--stone-600); margin-bottom: 0;">
            Use the filters below to search for specific content, authors, hashtags, and date ranges.
        </p>
    </div>

    <form method="GET" action="{{ route('search.advanced.results') }}">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div>
                <div class="form-group">
                    <label for="keywords" class="form-label">Keywords</label>
                    <input type="text" id="keywords" name="keywords" class="form-control" 
                           value="{{ request('keywords') }}"
                           placeholder="Search in titles and content...">
                    <p style="font-size: 14px; color: var(--stone-500); margin-top: 5px;">
                        Search for specific words or phrases in post titles and content
                    </p>
                </div>

                <div class="form-group">
                    <label for="author" class="form-label">Author</label>
                    <input type="text" id="author" name="author" class="form-control" 
                           value="{{ request('author') }}"
                           placeholder="Username or display name...">
                    <p style="font-size: 14px; color: var(--stone-500); margin-top: 5px;">
                        Find posts by specific authors
                    </p>
                </div>

                <div class="form-group">
                    <label for="hashtags" class="form-label">Hashtags</label>
                    <input type="text" id="hashtags" name="hashtags" class="form-control" 
                           value="{{ request('hashtags') }}"
                           placeholder="technology, news, lifestyle">
                    <p style="font-size: 14px; color: var(--stone-500); margin-top: 5px;">
                        Comma-separated hashtags (without # symbol)
                    </p>
                </div>
            </div>

            <div>
                <div class="form-group">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" id="date_from" name="date_from" class="form-control" 
                           value="{{ request('date_from') }}">
                </div>

                <div class="form-group">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" id="date_to" name="date_to" class="form-control" 
                           value="{{ request('date_to') }}">
                </div>

                <div class="form-group">
                    <label for="sort_by" class="form-label">Sort Results By</label>
                    <select id="sort_by" name="sort_by" class="form-control">
                        <option value="newest" {{ request('sort_by') === 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort_by') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="most_liked" {{ request('sort_by') === 'most_liked' ? 'selected' : '' }}>Most Liked</option>
                        <option value="most_commented" {{ request('sort_by') === 'most_commented' ? 'selected' : '' }}>Most Commented</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Content Type</label>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">
                            <input type="checkbox" name="has_image" value="1" {{ request('has_image') ? 'checked' : '' }}>
                            <span>Posts with images</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">
                            <input type="checkbox" name="has_comments" value="1" {{ request('has_comments') ? 'checked' : '' }}>
                            <span>Posts with comments</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">
                            <input type="checkbox" name="popular_only" value="1" {{ request('popular_only') ? 'checked' : '' }}>
                            <span>Popular posts only (5+ likes)</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 15px; border-top: 1px solid var(--stone-200); padding-top: 20px; margin-top: 30px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Search
            </button>
            <a href="{{ route('search.advanced') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Clear All
            </a>
            <a href="{{ route('search') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Basic Search
            </a>
        </div>
    </form>
</div>

<!-- Quick Search Templates -->
<div class="card">
    <h3 style="margin-bottom: 20px; color: var(--stone-700); display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-magic" style="color: var(--stone-500);"></i>
        Quick Search Templates
    </h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
        <div class="search-template" style="border: 1px solid var(--stone-200); border-radius: 8px; padding: 15px; cursor: pointer; transition: all 0.2s;">
            <h4 style="margin-bottom: 8px; color: var(--stone-800);">Recent Popular Posts</h4>
            <p style="color: var(--stone-600); font-size: 14px; margin-bottom: 10px;">
                Find the most liked posts from the last week
            </p>
            <div style="font-size: 12px; color: var(--stone-500);">
                Sort by: Most Liked • Date: Last 7 days • Popular only
            </div>
        </div>

        <div class="search-template" style="border: 1px solid var(--stone-200); border-radius: 8px; padding: 15px; cursor: pointer; transition: all 0.2s;">
            <h4 style="margin-bottom: 8px; color: var(--stone-800);">Tech & Innovation</h4>
            <p style="color: var(--stone-600); font-size: 14px; margin-bottom: 10px;">
                Latest posts about technology and innovation
            </p>
            <div style="font-size: 12px; color: var(--stone-500);">
                Hashtags: technology, innovation, ai, coding
            </div>
        </div>

        <div class="search-template" style="border: 1px solid var(--stone-200); border-radius: 8px; padding: 15px; cursor: pointer; transition: all 0.2s;">
            <h4 style="margin-bottom: 8px; color: var(--stone-800);">News & Current Events</h4>
            <p style="color: var(--stone-600); font-size: 14px; margin-bottom: 10px;">
                Stay updated with news and current events
            </p>
            <div style="font-size: 12px; color: var(--stone-500);">
                Hashtags: news, breaking, politics, world
            </div>
        </div>

        <div class="search-template" style="border: 1px solid var(--stone-200); border-radius: 8px; padding: 15px; cursor: pointer; transition: all 0.2s;">
            <h4 style="margin-bottom: 8px; color: var(--stone-800);">Lifestyle & Tips</h4>
            <p style="color: var(--stone-600); font-size: 14px; margin-bottom: 10px;">
                Discover lifestyle content and helpful tips
            </p>
            <div style="font-size: 12px; color: var(--stone-500);">
                Hashtags: lifestyle, tips, health, productivity
            </div>
        </div>
    </div>
</div>

<!-- Search Tips -->
<div class="card">
    <h3 style="margin-bottom: 20px; color: var(--stone-700); display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-lightbulb" style="color: var(--stone-500);"></i>
        Search Tips
    </h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <div>
            <h4 style="margin-bottom: 10px; color: var(--stone-800); font-size: 16px;">Keywords</h4>
            <ul style="margin: 0; padding-left: 20px; color: var(--stone-600); font-size: 14px;">
                <li style="margin-bottom: 5px;">Use specific words for better results</li>
                <li style="margin-bottom: 5px;">Try different synonyms if needed</li>
                <li>Combine multiple keywords for precision</li>
            </ul>
        </div>
        <div>
            <h4 style="margin-bottom: 10px; color: var(--stone-800); font-size: 16px;">Date Ranges</h4>
            <ul style="margin: 0; padding-left: 20px; color: var(--stone-600); font-size: 14px;">
                <li style="margin-bottom: 5px;">Leave empty for all-time search</li>
                <li style="margin-bottom: 5px;">Use single date for "since" searches</li>
                <li>Combine both for specific periods</li>
            </ul>
        </div>
        <div>
            <h4 style="margin-bottom: 10px; color: var(--stone-800); font-size: 16px;">Hashtags</h4>
            <ul style="margin: 0; padding-left: 20px; color: var(--stone-600); font-size: 14px;">
                <li style="margin-bottom: 5px;">Don't include the # symbol</li>
                <li style="margin-bottom: 5px;">Separate multiple tags with commas</li>
                <li>Use popular hashtags for more results</li>
            </ul>
        </div>
    </div>
</div>

@push('styles')
<style>
.search-template:hover {
    border-color: var(--stone-400);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transform: translateY(-1px);
}

.search-template:active {
    transform: translateY(0);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle search template clicks
    const templates = document.querySelectorAll('.search-template');
    
    templates.forEach((template, index) => {
        template.addEventListener('click', function() {
            const form = document.querySelector('form');
            
            // Clear existing values
            form.querySelectorAll('input').forEach(input => {
                if (input.type === 'checkbox') {
                    input.checked = false;
                } else {
                    input.value = '';
                }
            });
            
            const sortBy = document.getElementById('sort_by');
            
            // Apply template-specific filters
            switch(index) {
                case 0: // Recent Popular Posts
                    document.getElementById('date_from').value = getDateDaysAgo(7);
                    sortBy.value = 'most_liked';
                    document.querySelector('input[name="popular_only"]').checked = true;
                    break;
                    
                case 1: // Tech & Innovation
                    document.getElementById('hashtags').value = 'technology, innovation, ai, coding';
                    sortBy.value = 'newest';
                    break;
                    
                case 2: // News & Current Events
                    document.getElementById('hashtags').value = 'news, breaking, politics, world';
                    sortBy.value = 'newest';
                    break;
                    
                case 3: // Lifestyle & Tips
                    document.getElementById('hashtags').value = 'lifestyle, tips, health, productivity';
                    sortBy.value = 'newest';
                    break;
            }
        });
    });
    
    // Helper function to get date X days ago
    function getDateDaysAgo(days) {
        const date = new Date();
        date.setDate(date.getDate() - days);
        return date.toISOString().split('T')[0];
    }
    
    // Auto-set max date for date_to when date_from is selected
    document.getElementById('date_from').addEventListener('change', function() {
        const dateTo = document.getElementById('date_to');
        if (this.value && !dateTo.value) {
            dateTo.min = this.value;
        }
    });
    
    // Auto-set min date for date_from when date_to is selected
    document.getElementById('date_to').addEventListener('change', function() {
        const dateFrom = document.getElementById('date_from');
        if (this.value) {
            dateFrom.max = this.value;
        }
    });
});
</script>
@endpush
@endsection