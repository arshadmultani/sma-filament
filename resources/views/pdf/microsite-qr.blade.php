<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ $name }} – Microsite QR</title>
  <style>
    @page {
      size: A4 portrait;
      margin: 0;
    }
    html, body {
      width: 100%;
      height: 100%;
      margin: 0;
      padding: 0;
    }
    body {
      width: 100%;
      height: 100%;
      margin: 0;
      padding: 0;
    }
    .container {
      display: table;
      width: 100%;
      height: 100%;
      margin: 0;
      padding: 0;
    }
    .content {
      display: table-cell;
      vertical-align: middle;
      text-align: center;
      width: 100%;
      height: 100%;
    }
    .title {
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 150px;

    }
    .qr img {
      width: 500px;
      height: 500px;
      margin-bottom: 20px;
    }
    .description {
      font-size: 1.5rem;
      color: #555;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="content">
      <div class="title">Dr. {{ $name }}</div>
      <div class="qr">
        <img src="{{ $qrDataUri }}" alt="QR Code">
      </div>
      <div class="description">
        Scan QR to know more
      </div>
    </div>
  </div>
</body>
</html>
