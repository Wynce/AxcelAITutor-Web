<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Response</title>
     <link rel="icon" href="{{asset('storage/settings/axcel_logo.png')}}">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        .flash-message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            color: white;
        }

        .flash-message.success {
            background-color: #5cb85c;
        }

        .flash-message.error {
            background-color: #d9534f;
        }
    </style>
</head>
<body>
    <div class="container">
        @if (@$success)
            <div class="flash-message success">
                {{ @$success }}
            </div>
        @endif

        @if (@$error)
            <div class="flash-message error">
                {{ @$error }}
            </div>
        @endif

    </div>
</body>
</html>
