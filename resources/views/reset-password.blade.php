<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
      <link rel="icon" href="{{asset('storage/settings/axcel_logo.png')}}">
    <style>
        /* Add your CSS styles here */
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

        h1 {
            margin-bottom: 20px;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 5px;
            text-align: left;
            color: #666;
        }

        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background-color: #5cb85c;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        button:hover {
            background-color: #4cae4c;
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

        .error-message {
            color: #d9534f; /* Red color for error messages */
            font-size: 12px;
            margin-top: -10px; /* Adjust positioning */
            margin-bottom: 10px; /* Space between error and input */
            text-align: left; /* Align error messages to the left */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reset Password</h1>

        @if (Session('success'))
            <div class="flash-message success">
                {{ session('success') }}
            </div>
        @endif
        
        @if (Session('error'))
            <div class="flash-message error">
                {{ session('error') }}
            </div>
        @endif

        
        <form id="resetPasswordForm" method="POST" action="{{ route('password.update') }}" onsubmit="return validateForm()">
            @csrf
            <input type="hidden" name="token" value="{{ request()->query('token') }}">
            <input type="hidden" name="email" value="{{ request()->query('email') }}">
            
            <div>
                <label for="password">New Password</label>
                <input type="password" name="password" id="password" required>
                <div class="error-message" id="passwordError"></div>
            </div>
            
            <div>
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required>
                <div class="error-message" id="passwordConfirmationError"></div>
            </div>
            
            <button type="submit" style="background-color:#EA6F35;border:1px solid #EA6F35;">Reset Password</button>
        </form>
    </div>

    <script>
        function validateForm() {
            // Clear previous error messages
            document.getElementById('passwordError').innerText = '';
            document.getElementById('passwordConfirmationError').innerText = '';

            // Get the password and confirmation values
            var password = document.getElementById('password').value;
            var passwordConfirmation = document.getElementById('password_confirmation').value;
            var isValid = true;

            // Check password length
            if (password.length < 8) {
                document.getElementById('passwordError').innerText = 'Password must be at least 8 characters.';
                isValid = false;
            }

            // Check if passwords match
            if (password !== passwordConfirmation) {
                document.getElementById('passwordConfirmationError').innerText = 'Passwords do not match.';
                isValid = false;
            }

            return isValid; // Prevent form submission if not valid
        }
    </script>
</body>
</html>
