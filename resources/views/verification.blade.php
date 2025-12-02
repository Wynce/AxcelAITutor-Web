<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link rel="icon" href="{{asset('storage/settings/axcel_logo.png')}}">
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #6D83F2, #A8E6FF);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }

        /* Centered container for the verification message */
        .verification-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .verification-container h1 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #fff;
        }

        .verification-container p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            color: #fff;
        }

        /* Success/Failure messages styling */
        .alert-success {
            color: #28a745;
        }

        .alert-danger {
            color: #dc3545;
        }

        /* Button styling */
        .btn {
            background-color: #6D83F2;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #4F6AD3;
        }

        /* Link Styling */
        a {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body>

    <div class="verification-container">
        <!-- Dynamic success or error message -->
        <h1 class="{{ $status ? 'alert-success' : 'alert-danger' }}">
            {{ $message }}
        </h1>

        @if ($status)
            <p>Thank you for verifying your email. Your account is now active! You can now log in and enjoy all the features of our application.!</p>
        @else
            <p>There was an issue with your email verification. Please try again.</p>
        @endif

       
    </div>

</body>
</html>
