<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OJT Portal - Customer Support</title>
  
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

    .iframe-container {
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

    .iframe-container h2 {
      font-family: 'Poppins', sans-serif;
      font-size: 2.5rem;
      font-weight: 300;
      color: #000;
      margin: 1rem 0;
    }

    iframe {
      width: 100%;
      height: 60vh; /* Adjusted to be responsive */
      border: none;
      border-radius: 8px;
      max-width: 800px; /* Matches original max-width for iframe content */
    }

    .back-button {
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

    .back-button:hover {
      opacity: 0.85;
    }

    @media (max-width: 768px) {
      .iframe-container {
        width: 80%;
        padding: 1.5rem;
      }

      .logo {
        height: 80px;
      }

      .iframe-container h2 {
        font-size: 1.75rem;
      }

      .back-button {
        font-size: 1rem;
        padding: 0.8rem;
      }

      iframe {
        height: 50vh;
      }
    }

    @media (max-width: 480px) {
      .iframe-container {
        width: 100%;
        padding: 1rem;
      }

      .logo {
        height: 60px;
      }

      .iframe-container h2 {
        font-size: 1.5rem;
      }

      .back-button {
        font-size: 0.9rem;
        padding: 0.7rem;
      }

      iframe {
        height: 40vh;
      }
    }
  </style>
</head>
<body>
  <div class="iframe-container">
    <img src="image/pupLogo.png" alt="Polytechnic University of the Philippines Logo" class="logo">
    <iframe id="JotFormIFrame-019739bc5cf27e3d96d9c6e3f88ddce79b30" title="Customer Support AI Agent"
        onload="window.parent.scrollTo(0,0)" allowtransparency="true"
        allow="geolocation; microphone; camera; fullscreen"
        src="https://agent.jotform.com/019739bc5cf27e3d96d9c6e3f88ddce79b30?embedMode=iframe&background=0&shadow=1"
        frameborder="0" style="
            min-width:100%;
            max-width:100%;
            height:688px;
            border:none;
            width:100%;
        " scrolling="no">
        </iframe>
        <script src='https://cdn.jotfor.ms/s/umd/latest/for-form-embed-handler.js'></script>
        <script>
        window.jotformEmbedHandler("iframe[id='JotFormIFrame-019739bc5cf27e3d96d9c6e3f88ddce79b30']",
            "https://www.jotform.com")
        </script>
    <button class="back-button" onclick="window.location.href='index.php'">Back to OJT Portal</button>
  </div>
</body>
</html>