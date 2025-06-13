<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>No Internet Connection</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: sans-serif;
      color: #333;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      text-align: center;
      background: #fff;
    }
    .container {
      max-width: 400px;
      padding: 16px;
    }
    .illustration {
      width: 100%;
      max-width: 240px;
      margin: 0 auto 24px;
      opacity: 0.8;
    }
    h1 {
      font-size: 1.5em;
      margin-bottom: 8px;
    }
    p {
      font-size: 1em;
      margin-bottom: 24px;
      color: #666;
    }
    .retry-button {
      background: #e53935;
      color: #fff;
      border: none;
      padding: 12px 24px;
      font-size: 1em;
      border-radius: 24px;
      cursor: pointer;
    }
    .retry-button:hover {
      background: #d32f2f;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Oops, No Internet Connection</h1>
    <p>Make sure wifi or cellular data is turned on and then try again.</p>
    <button class="retry-button" onclick="location.reload();">
      TRY AGAIN
    </button>
  </div>
</body>
</html>