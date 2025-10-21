<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold">Admin Dashboard</h1>
                </div>
                <div class="flex items-center">
                    <span class="mr-4">Welcome, {{ Auth::user()->full_name }} (Admin)</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="border-4 border-dashed border-gray-200 rounded-lg h-96 p-4">
                <h2 class="text-2xl font-bold mb-4">Admin Control Panel</h2>
                <p>Welcome to the admin dashboard. Here you can manage:</p>
                <ul class="list-disc list-inside mt-4">
                    <li>Users</li>
                    <li>Projects</li>
                    <li>System Settings</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>