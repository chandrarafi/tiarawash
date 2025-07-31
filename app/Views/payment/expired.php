<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #0088cc;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .expired-container {
            max-width: 500px;
            width: 100%;
            padding: 2rem;
        }

        .expired-card {
            background: white;
            border-radius: 20px;
            padding: 3rem 2rem;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }

        .expired-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--danger-color), #e74c3c);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            color: white;
            font-size: 2.5rem;
        }

        .expired-title {
            color: var(--danger-color);
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .expired-message {
            color: #6c757d;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .booking-info {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
        }

        .info-value {
            color: #6c757d;
        }

        .btn-home {
            background: linear-gradient(135deg, var(--primary-color), #1976d2);
            border: none;
            border-radius: 12px;
            padding: 0.75rem 2rem;
            color: white;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 136, 204, 0.3);
            color: white;
        }

        .expired-animation {
            animation: slideInDown 0.8s ease-out;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="expired-container">
        <div class="expired-card expired-animation">
            <div class="expired-icon">
                <i class="fas fa-clock"></i>
            </div>

            <h1 class="expired-title">Waktu Pembayaran Habis</h1>

            <p class="expired-message">
                Maaf, batas waktu pembayaran untuk booking Anda telah berakhir.
                Booking telah dibatalkan secara otomatis.
            </p>

            <div class="booking-info">
                <div class="info-row">
                    <span class="info-label">Kode Booking:</span>
                    <span class="info-value"><?= esc($kode_booking) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Batas Waktu:</span>
                    <span class="info-value"><?= date('d M Y H:i', strtotime($expires_at)) ?> WIB</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value text-danger">
                        <i class="fas fa-times-circle me-1"></i>
                        Dibatalkan
                    </span>
                </div>
            </div>

            <a href="<?= site_url('/') ?>" class="btn-home">
                <i class="fas fa-home"></i>
                Kembali ke Beranda
            </a>

            <div class="mt-4">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Anda dapat membuat booking baru kapan saja
                </small>
            </div>
        </div>
    </div>
</body>

</html>