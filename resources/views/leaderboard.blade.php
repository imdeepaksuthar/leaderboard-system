<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Leaderboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="bg-black text-white p-8 font-sans">
    <div class="max-w-xl mx-auto">
        <!-- Top Section -->
        <form method="POST" action="/leaderboard/recalculate">
            @csrf
            <div class="text-center mb-6">
                <button class="px-5 py-2 bg-gray-300 text-black font-semibold rounded-lg hover:bg-gray-400"
                    type="submit">Recalculate</button>
            </div>
        </form>

        <!-- Filter Section -->
        <form method="GET" action="/leaderboard">
            <div class="flex flex-col sm:flex-row sm:items-end gap-4 mb-6">
                <!-- User ID Input -->
                <div class="w-full sm:w-1/2">
                    <label class="block text-sm text-gray-400 mb-1">User ID</label>
                    <input type="text" name="user_id" placeholder="User ID" value="{{ $userId }}"
                        class="w-full px-4 py-2 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-gray-500" />
                </div>

                <!-- Filter Dropdown -->
                <div class="w-full sm:w-1/4">
                    <label class="block text-sm text-gray-400 mb-1">Filter By</label>
                    <select name="filter"
                        class="w-full px-4 py-2 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-gray-500">
                        <option value="day" {{ $filter == 'day' ? 'selected' : '' }}>Day</option>
                        <option value="month" {{ $filter == 'month' ? 'selected' : '' }}>Month</option>
                        <option value="year" {{ $filter == 'year' ? 'selected' : '' }}>Year</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="w-full sm:w-auto">
                    <label class="block text-sm invisible mb-1">Submit</label>
                    <button type="submit"
                        class="w-full px-5 py-2 bg-gray-300 text-black font-semibold rounded-lg hover:bg-gray-400">Filter</button>
                </div>
            </div>
        </form>


        <!-- Table Headers -->
        <div class="grid grid-cols-4 gap-4 text-sm text-gray-400 mb-2 px-2">
            <div>ID</div>
            <div>Name</div>
            <div>Points</div>
            <div class="text-right">Rank</div>
        </div>

        <!-- Leaderboard Rows -->
        <div class="space-y-3">
            @foreach ($ranked as $user)
                <div
                    class="flex justify-between items-center bg-gradient-to-r from-gray-800 to-gray-900 rounded-xl px-4 py-3 border-2 border-white">
                    <span class="w-1/4">{{ $user->id }}</span>
                    <span class="w-1/4">{{ $user->name }}</span>
                    <span class="w-1/4">{{ $user->total_points }}</span>
                    <span class="w-1/4 text-right text-gray-400">#{{ $user->rank }}</span>
                </div>
            @endforeach

        </div>
    </div>
</body>
<!-- ... HTML remains the same ... -->

<script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>
<script>
    const socket = io("http://localhost:3000");

    // Emit recalculate on form submit and prevent default Laravel post
    document.querySelector('form[action="/leaderboard/recalculate"]').addEventListener('submit', function(e) {
        e.preventDefault();
        socket.emit('recalculate');
    });

    // When leaderboard updated, reload page
    socket.on("updateLeaderboard", () => {
        console.log("🔄 updateLeaderboard received from server");
        setTimeout(() => {
            window.location.reload();
        }, 500); // Delay to ensure Laravel update finishes
    });
</script>


</html>
