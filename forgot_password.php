<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OJT Portal - Forgot Password</title>
  
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
      justify-content: center;
      align-items: center;
      overflow-y: auto;
      overflow-x: hidden;
    }

    .container {
      width: 100%;
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
      height: 100px;
      margin-bottom: 1rem;
    }

    .container h2 {
      font-family: 'Poppins', sans-serif;
      font-size: 2.5rem;
      font-weight: 300;
      color: #000;
      margin: 1rem 0;
    }

    .form-container {
      width: 100%;
      max-width: 400px;
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .form-container input {
      width: 100%;
      padding: 0.9rem;
      border: none;
      border-radius: 8px;
      font-family: 'Poppins', sans-serif;
      font-size: 1rem;
      font-weight: 400;
      color: #333;
      background-color: #fff;
      outline: none;
    }

    .form-container input:focus {
      box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
    }

    .submit-button, .back-button {
      width: 100%;
      max-width: 400px;
      padding: 0.9rem;
      margin: 1rem 0;
      border: none;
      border-radius: 8px;
      font-family: 'Poppins', sans-serif;
      font-size: 1.1rem;
      font-weight: 500;
      color: #fff;
      background-color: #444444;
      cursor: pointer;
      transition: opacity 0.3s ease;
    }

    .submit-button:hover, .back-button:hover {
      opacity: 0.85;
    }

    @media (max-width: 768px) {
      .container {
        width: 80%;
        padding: 1.5rem;
      }

      .logo {
        height: 80px;
      }

      .container h2 {
        font-size: 1.75rem;
      }

      .submit-button, .back-button {
        font-size: 1rem;
        padding: 0.8rem;
      }
    }

    @media (max-width: 480px) {
      .container {
        width: 100%;
        padding: 1rem;
      }

      .logo {
        height: 60px;
      }

      .container h2 {
        font-size: 1.5rem;
      }

      .submit-button, .back-button {
        font-size: 0.9rem;
        padding: 0.7rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <img src="image/pupLogo.png" alt="Polytechnic University of the Philippines Logo" class="logo">
    <h2>Forgot Password</h2>
    <div class="form-container">
      <input type="email" placeholder="Enter your email address" required>
      <button class="submit-button">Reset Password</button>
    </div>
    <button class="back-button" onclick="window.location.href='index.php'">Back to OJT Portal</button>
  </div>
</body>
</html>