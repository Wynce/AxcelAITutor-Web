<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AI Tutorials - Learn Artificial Intelligence</title>
    <link rel="icon" href="{{asset('storage/settings/axcel_logo.png')}}">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7f6;
            color: #333;
        }

        .hero-section {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 200px 20px;
        }

        .hero-section img {
            height: 60px;
            margin-bottom: 20px; /* Space between logo and text */
        }

        .hero-section h1 {
            font-size: 48px;
            margin: 0;
        }

        .hero-section p {
            font-size: 20px;
            margin: 20px 0;
        }

        .hero-section .cta-button {
            background-color: white;
            color: #007bff;
            padding: 15px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
            display: inline-block;
        }

        .hero-section .cta-button:hover {
            background-color: #e2e6ea;
        }
      
       /* .content-section {
            padding: 60px 20px;
            max-width: 1200px;
            margin: auto;
        }

        .content-section h2 {
            text-align: center;
            font-size: 36px;
            margin-bottom: 40px;
        }

        .tutorials-grid {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .tutorial-item {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 30%;
        }

        .tutorial-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-bottom: 15px;
        }

        .tutorial-item h3 {
            font-size: 24px;
            margin-bottom: 15px;
        }
*/
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px 0;
            /*margin-top: 40px;*/
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <div class="hero-section">
        <img src="{{ asset('storage/settings/axcel_logo.png') }}" alt="Axcel AI Logo">
        <h1>Axcel AI Tutor</h1>
        <p>Start your journey into the world of AI with our step-by-step tutorials.</p>
        <a href="#" class="cta-button">Start Learning Now</a>
    </div>

    <!-- Popular Tutorials Section -->
    <!--<div class="content-section">
     <h2>Popular Tutorials</h2>
        <div class="tutorials-grid">
            <div class="tutorial-item">
                <img src="{{asset('storage/settings/machine_learning.png')}}" alt="Tutorial 1">
                <h3>Introduction to Machine Learning</h3>
                <p>Learn the basics of machine learning, including supervised and unsupervised learning.</p>
            </div>
            <div class="tutorial-item">
                <img src="{{asset('storage/settings/neural_networks_beginners.webp')}}" alt="Tutorial 2">
                <h3>Neural Networks for Beginners</h3>
                <p>Explore neural networks and how they are applied in modern AI applications.</p>
            </div>
            <div class="tutorial-item">
                <img src="{{asset('storage/settings/nlp.jpg')}}" alt="Tutorial 3">
                <h3>Natural Language Processing (NLP)</h3>
                <p>Dive into NLP and understand how machines interpret human language.</p>
            </div>
        </div>
    </div>-->

    <!-- Footer Section -->
    <div class="footer">
        <p>&copy; 2024 Axcel AI Tutor. All Rights Reserved.</p>
    </div>
</body>
</html>
