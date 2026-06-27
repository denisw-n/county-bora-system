<!DOCTYPE html>
<html>
<head>
    <title>Test</title>
</head>
<body>

<h1>County Bora</h1>

<form method="POST" action="{{ route('login.submit') }}">
    @csrf

    <input
        type="email"
        name="email"
        placeholder="Email">

    <input
        type="password"
        name="password"
        placeholder="Password">

    <button type="submit">
        Login
    </button>

</form>

</body>
</html>