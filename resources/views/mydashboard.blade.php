<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Skill Sharing Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }
        header {
            background: #333;
            color: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .container {
            padding: 20px;
        }
        a {
            color: #3498db;
            text-decoration: none;
        }
        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .dashboard-links a {
            display: inline-block;
            margin-right: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<header>
    <h1>Welcome to the Skill Sharing Platform</h1>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" style="background: none; border: none; color: white; cursor: pointer;">Logout</button>
    </form>
</header>

<div class="container">
    <div class="card">
        <h2>Hello, {{ Auth::user()->name }}!</h2>
        <p>Your role: <strong>{{ Auth::user()->role }}</strong></p>
    </div>

    <div class="card dashboard-links">
        <h3>Dashboard Options</h3>
        <a href="{{ route('skills.shared') }}">My Shared Skills</a>
        <a href="{{ route('skills.requested') }}">Requested Skills</a>
        <a href="{{ route('skills.explore') }}">Explore Skills</a>
        <a href="{{ route('profile.edit') }}">Edit Profile</a>
    </div>
</div>

</body>
</html>
