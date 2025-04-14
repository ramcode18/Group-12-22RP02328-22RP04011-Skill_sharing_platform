<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen bg-gray-100">
    <div class="min-h-screen flex flex-col">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <main class="flex-grow">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <x-footer />
    </div>

    <!-- Scripts for Reactions -->
    <script>
        function react(skillId, type) {
            const token = document.querySelector('meta[name="csrf-token"]').content;
            fetch(`/skills/${skillId}/react`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ type })
            })
            .then(response => response.json())
            .then(data => {
                document.querySelector(`#likes-count-${skillId}`).textContent = data.likes_count;
                document.querySelector(`#dislikes-count-${skillId}`).textContent = data.dislikes_count;
                updateReactionButtons(skillId, data.user_reaction);
            });
        }

        function removeReaction(skillId) {
            const token = document.querySelector('meta[name="csrf-token"]').content;
            fetch(`/skills/${skillId}/react`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token
                }
            })
            .then(response => response.json())
            .then(data => {
                document.querySelector(`#likes-count-${skillId}`).textContent = data.likes_count;
                document.querySelector(`#dislikes-count-${skillId}`).textContent = data.dislikes_count;
                updateReactionButtons(skillId, null);
            });
        }

        function updateReactionButtons(skillId, userReaction) {
            const likeBtn = document.querySelector(`#like-btn-${skillId}`);
            const dislikeBtn = document.querySelector(`#dislike-btn-${skillId}`);
            
            likeBtn.classList.remove('text-green-600', 'text-gray-400');
            dislikeBtn.classList.remove('text-red-600', 'text-gray-400');
            
            if (userReaction === 'like') {
                likeBtn.classList.add('text-green-600');
                dislikeBtn.classList.add('text-gray-400');
            } else if (userReaction === 'dislike') {
                likeBtn.classList.add('text-gray-400');
                dislikeBtn.classList.add('text-red-600');
            } else {
                likeBtn.classList.add('text-gray-400');
                dislikeBtn.classList.add('text-gray-400');
            }
        }
    </script>
</body>
</html>
