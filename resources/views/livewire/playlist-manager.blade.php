<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">My Playlists</h1>

        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Create/Edit Playlist Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-8">
                    <h2 class="text-xl font-semibold mb-4">{{ $editingId ? 'Edit Playlist' : 'Create Playlist' }}</h2>
                    
                    <form wire:submit.prevent="{{ $editingId ? 'update' : 'create' }}">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Playlist Name</label>
                            <input type="text" wire:model="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600">
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea wire:model="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600"></textarea>
                            @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex space-x-2">
                            <button type="submit" class="flex-1 bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition">
                                {{ $editingId ? 'Update' : 'Create' }}
                            </button>
                            @if($editingId)
                                <button type="button" wire:click="cancelEdit" class="flex-1 bg-gray-500 text-white py-2 rounded-lg hover:bg-gray-600 transition">
                                    Cancel
                                </button>
                            @endif
                        </div>
                    </form>

                    <!-- Add Music to Playlist -->
                    @if($playlists->count() > 0 && !$editingId)
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold mb-4">Add Song to Playlist</h3>
                            <div class="space-y-2 max-h-96 overflow-y-auto">
                                @foreach($availableMusic as $music)
                                    <div class="border border-gray-200 rounded-lg p-3">
                                        <p class="font-semibold text-sm">{{ $music->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $music->artist->name }}</p>
                                        <select wire:change="addToPlaylist($event.target.value, {{ $music->id }})" class="mt-2 w-full text-sm px-2 py-1 border border-gray-300 rounded">
                                            <option value="">Add to playlist...</option>
                                            @foreach($playlists as $playlist)
                                                <option value="{{ $playlist->id }}">{{ $playlist->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Playlists List -->
            <div class="lg:col-span-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($playlists as $playlist)
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $playlist->name }}</h3>
                            <p class="text-gray-600 text-sm mb-4">{{ $playlist->description ?? 'No description' }}</p>
                            <p class="text-sm text-gray-500 mb-4">{{ $playlist->music_count }} songs</p>
                            
                            <div class="flex space-x-2">
                                <button wire:click="viewPlaylist({{ $playlist->id }})" class="flex-1 bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition text-sm">
                                    View Songs
                                </button>
                                <button wire:click="edit({{ $playlist->id }})" class="px-4 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 transition text-sm">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $playlist->id }})" onclick="return confirm('Are you sure?')" class="px-4 bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition text-sm">
                                    Delete
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($playlists->isEmpty())
                    <div class="bg-white rounded-lg shadow-md p-12 text-center text-gray-500">
                        <p class="text-lg mb-4">No playlists yet!</p>
                        <p>Create your first playlist to organize your favorite music.</p>
                    </div>
                @endif

                <!-- Playlist Details Modal -->
                @if($selectedPlaylistId)
                    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click.self="$set('selectedPlaylistId', null)">
                        <div class="bg-white rounded-lg p-8 max-w-2xl w-full max-h-[80vh] overflow-y-auto">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-2xl font-bold">Playlist Songs</h2>
                                <button wire:click="$set('selectedPlaylistId', null)" class="text-gray-500 hover:text-gray-700">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            @if($selectedPlaylistMusic->isEmpty())
                                <p class="text-gray-500 text-center py-8">No songs in this playlist yet.</p>
                            @else
                                <div class="space-y-3">
                                    @foreach($selectedPlaylistMusic as $music)
                                        <div class="flex justify-between items-center border border-gray-200 rounded-lg p-4">
                                            <div>
                                                <p class="font-semibold">{{ $music->title }}</p>
                                                <p class="text-sm text-gray-500">{{ $music->artist->name }}</p>
                                            </div>
                                            <button wire:click="removeFromPlaylist({{ $selectedPlaylistId }}, {{ $music->id }})" 
                                                class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition text-sm">
                                                Remove
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

