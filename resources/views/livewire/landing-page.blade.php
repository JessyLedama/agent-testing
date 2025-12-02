<div class="min-h-screen">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-5xl font-bold mb-4">Welcome to MusicStream</h1>
            <p class="text-xl mb-8">Discover amazing music from talented artists around the world</p>
            @guest
                <div class="space-x-4">
                    <a href="/register" class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                        Get Started
                    </a>
                    <a href="/login" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-indigo-600 transition">
                        Login
                    </a>
                </div>
            @endguest
        </div>
    </div>

    <!-- Random Music Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Discover Music</h2>
            <button wire:click="$refresh" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                🔄 Refresh
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($randomMusic as $music)
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

                    @auth
                        <a href="/streaming?music={{ $music->id }}" class="block w-full bg-indigo-600 text-white text-center py-2 rounded-lg hover:bg-indigo-700 transition">
                            Play Now
                        </a>
                    @else
                        <a href="/login" class="block w-full bg-gray-400 text-white text-center py-2 rounded-lg hover:bg-gray-500 transition">
                            Login to Play
                        </a>
                    @endauth
                </div>
            @endforeach
        </div>

        @if($randomMusic->isEmpty())
            <div class="text-center py-12">
                <p class="text-gray-500 text-lg">No music available yet. Check back soon!</p>
            </div>
        @endif
    </div>
</div>

