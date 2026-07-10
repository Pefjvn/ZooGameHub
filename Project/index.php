<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opening Page</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at top, #9bd18b 0%, #4b8a55 55%, #163b1f 100%);
            font-family: Arial, sans-serif;
            color: #fff;
        }
        .popup {
            position: relative;
            width: 100%;
            max-width: 960px;
            max-height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(16, 47, 22, 0.9);
            border: 2px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.35);
            border-radius: 24px;
            overflow: hidden;
        }
        .popup::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top left, rgba(255,255,255,0.1), transparent 35%),
                        radial-gradient(circle at bottom right, rgba(255,255,255,0.08), transparent 25%);
            pointer-events: none;
        }
        .popup img {
            width: 100%;
            height: auto;
            max-height: 100%;
            object-fit: cover;
            border-radius: 16px;
            box-shadow: inset 0 0 40px rgba(0, 0, 0, 0.4);
        }
        .top-button {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 12px 18px;
            background: rgba(255, 255, 255, 0.95);
            color: #1b3b1c;
            text-decoration: none;
            font-weight: bold;
            border-radius: 999px;
            border: 1px solid rgba(27, 59, 28, 0.5);
            transition: transform 0.2s ease, background 0.2s ease;
        }
        .top-button:hover {
            transform: translateY(-2px);
            background: #f4f8f0;
        }
    </style>
</head>
<body>
    <div class="popup">
        <a class="top-button" href="homepage.html" title="Close">X</a>
        <img src="images/Panda.jpg" alt="Panda">
    </div>
</body>
</html>