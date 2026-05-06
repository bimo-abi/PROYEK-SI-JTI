<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . " - SI-JTI" : "SI-JTI - Sistem Informasi Surat"; ?></title>

    <!-- Link Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome untuk Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            /* Pastikan path gambar background benar menggunakan base path atau relatif yang tepat */
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../../assets/img/jti-bg.jpg');
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .auth-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .btn-primary {
            background-color: #00a0f0;
            border: none;
            border-radius: 10px;
        }

        .btn-secondary {
            background-color: #8a8a8a;
            border: none;
            border-radius: 10px;
        }

        .form-control {
            background-color: #e0e0e0;
            border: none;
            border-radius: 10px;
            padding: 12px;
        }

        /* Tambahan CSS agar layout sidebar tidak berantakan jika dipanggil setelah header */
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }
    </style>
</head>

<body>