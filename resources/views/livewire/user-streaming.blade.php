<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Browse Music</h1>

        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <input type="text" wire:model.live="searchQuery" placeholder="Search for music or artists..." 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600">
                </div>
                <div>
                    <select wire:model.live="categoryFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Music Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Music List -->
            <div class="lg:col-span-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($allMusic as $music)
                        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition p-6">
                            <div class="w-full h-48 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-lg mb-4 flex items-center justify-center">
                                <span class="text-6xl">🎵</span>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2 truncate">{{ $music->title }}</h3>
                            <p class="text-gray-600 mb-1">By {{ $music->artist->name }}</p>
                            <p class="text-sm text-gray-500 mb-4">{{ $music->category->name }}</p>
                            
                            <div class="flex justify-between text-sm text-gray-500 mb-4">
                                <span>👁️ {{ $music->viewsCount() }}</span>
                                <span>❤️ {{ $music->likesCount() }}</span>
                                <span>💬 {{ $music->commentsCount() }}</span>
                            </div>

                            <button wire:click="playMusic({{ $music->id }})" 
                                class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition">
                                Play Now
                            </button>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $allMusic->links() }}
                </div>
            </div>

            <!-- Now Playing Panel -->
            <div class="lg:col-span-1">
                @if($selectedMusic)
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-8">
                        <h2 class="text-2xl font-semibold mb-4">Now Playing</h2>
                        
                        <div class="w-full h-64 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-lg mb-4 flex items-center justify-center">
                            <span class="text-8xl">🎵</span>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $selectedMusic->title }}</h3>
                        <p class="text-gray-600 mb-1">By {{ $selectedMusic->artist->name }}</p>
                        <p class="text-sm text-gray-500 mb-4">{{ $selectedMusic->category->name }}</p>

                        <!-- Artist Follow Button -->
                        @if($selectedMusic->artist_id !== auth()->id())
                            <button wire:click="toggleFollow({{ $selectedMusic->artist_id }})" 
                                class="w-full mb-4 px-4 py-2 rounded-lg transition {{ $this->isFollowing($selectedMusic->artist_id) ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'bg-indigo-600 text-white hover:bg-indigo-700' }}">
                                {{ $this->isFollowing($selectedMusic->artist_id) ? 'Unfollow' : 'Follow Artist' }}
                            </button>
                        @endif

                        <!-- Like/Dislike Buttons -->
                        <div class="flex space-x-2 mb-4">
                            <button wire:click="toggleLike({{ $selectedMusic->id }}, true)" 
                                class="flex-1 px-4 py-2 rounded-lg transition {{ $this->getUserLike($selectedMusic->id) === 'like' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-green-100' }}">
                                ❤️ Like ({{ $selectedMusic->likesCount() }})
                            </button>
                            <button wire:click="toggleLike({{ $selectedMusic->id }}, false)" 
                                class="flex-1 px-4 py-2 rounded-lg transition {{ $this->getUserLike($selectedMusic->id) === 'dislike' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-red-100' }}">
                                👎 Dislike
                            </button>
                        </div>

                        <!-- Stats -->
                        <div class="flex justify-around text-center py-4 border-t border-b border-gray-200 mb-4">
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $selectedMusic->viewsCount() }}</p>
                                <p class="text-sm text-gray-500">Views</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $selectedMusic->likesCount() }}</p>
                                <p class="text-sm text-gray-500">Likes</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $selectedMusic->commentsCount() }}</p>
                                <p class="text-sm text-gray-500">Comments</p>
                            </div>
                        </div>

                        <!-- Comments Section -->
                        <div class="mb-4">
                            <h3 class="font-semibold mb-2">Comments</h3>
                            
                            @if(session('comment_success'))
                                <div class="bg-green-100 text-green-700 px-3 py-2 rounded mb-2 text-sm">
                                    {{ session('comment_success') }}
                                </div>
                            @endif

                            <form wire:submit.prevent="addComment" class="mb-4">
                                <textarea wire:model="comment" placeholder="Add a comment..." 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-indigo-600" 
                                    rows="3"></textarea>
                                @error('comment') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                <button type="submit" class="mt-2 w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition">
                                    Post Comment
                                </button>
                            </form>

                            <div class="space-y-3 max-h-64 overflow-y-auto">
                                @foreach($selectedMusic->comments as $comment)
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="font-semibold text-sm text-gray-900">{{ $comment->user->name }}</p>
                                        <p class="text-sm text-gray-700 mt-1">{{ $comment->content }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $comment->created_at->diffForHumans() }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow-md p-6 text-center text-gray-500">
                        <p class="text-lg">Select a song to start playing</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

