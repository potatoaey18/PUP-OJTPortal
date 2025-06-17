<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page - PUP ITECH OJT Portal</title>
    
    <link rel="shortcut icon" href="images/pupLogo.png">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Source+Serif+4:opsz,wght@8..60,200;8..60,400;8..60,700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Source Serif 4', serif;
            min-height: 100vh;
            background: url('./image/landing.jpg') no-repeat center/cover fixed;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .container {
            max-width: 40%;
            min-height: 100vh;
            background-color: rgba(218, 218, 218, 0.8);
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .logo {
            height: 80px;
            margin-bottom: 1rem;
        }

        h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 300;
            color: #000;
            margin: 0.5rem 0;
        }

        h3 {
            font-size: 1.75rem;
            font-weight: 600;
            color: #D11010;
            margin: 0.5rem 0;
            padding: 0 1rem;
            line-height: 1.3;
        }

        h4 {
            font-size: 1.2rem;
            font-weight: 400;
            color: #000;
            margin: 0.5rem 0;
        }

        h6 {
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            font-weight: 300;
            color: #333;
            margin: 0.5rem 0;
        }

        .buttons-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 1rem 0;
            width: 100%;
        }

        button {
            width: 100%;
            max-width: 400px;
            padding: 0.9rem;
            margin: 0.5rem 0;
            border: none;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 1.1rem;
            font-weight: 500;
            color: #fff;
            background-color: #800000;
            cursor: pointer;
            transition: opacity 0.3s ease;
        }

        button:hover {
            opacity: 0.85;
        }


        .footer {
            margin-top: 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .footer button {
            background-color: #0047AB;
            color: white;
            max-width: 300px;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .container {
                max-width: 80%;
                padding: 1.5rem;
            }

            .logo {
                height: 80px;
            }

            h2 {
                font-size: 1.75rem;
            }

            h3 {
                font-size: 1.4rem;
                padding: 0 0.5rem;
            }

            h4 {
                font-size: 1rem;
            }

            button {
                font-size: 1rem;
                padding: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                max-width: 100%;
                padding: 1rem;
            }

            .logo {
                height: 60px;
            }

            h2 {
                font-size: 1.5rem;
            }

            h3 {
                font-size: 1.2rem;
                line-height: 1.4;
            }

            h4 {
                font-size: 0.9rem;
            }

            button {
                font-size: 0.9rem;
                padding: 0.7rem;
            }

            .footer button {
                font-size: 0.8rem;
                padding: 0.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="image/pupLogo.png" alt="Polytechnic University of the Philippines Logo" class="logo">
        <h3>Polytechnic University of the Philippines - ITECH</h3>
        <h4>On the Job Training Portal</h4>
        <h2>Kamusta Teknolohista!</h2>
        <div class="buttons-container">
            <button onclick="window.location.href='pending/login.php'">Login</button>
<button onclick="window.location.href='pending/signup.php'">Sign Up</button>

        </div>
        <div class="footer">
            <p>Need help?</p>
            <button onclick="window.location.href='chatbot.php'">Chat with an AI Assistant</button>
            <h6>© 2025 Polytechnic University of the Philippines - ITECH</h6>
            <h6>All rights reserved.</h6>
        </div>
    </div>
</body>
</html>