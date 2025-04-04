<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pengingat Booking Ruangan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #22428e;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .booking-info {
            background-color: white;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 5px;
            margin: 15px 0;
        }
        .button {
            display: inline-block;
            background-color: #22428e;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            text-align: center;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Pengingat Booking Ruangan</h1>
    </div>
    
    <div class="content">
        <p>Halo <strong>{{ $booking->nama }}</strong>,</p>
        
        <p>Ini adalah pengingat bahwa Anda memiliki booking ruang meeting dalam waktu 1 jam lagi.</p>
        
        <div class="booking-info">
            <h3>Detail Booking:</h3>
            <p><strong>Ruangan:</strong> {{ $booking->meetingRoom->name }}</p>
            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($booking->date)->format('d F Y') }}</p>
            <p><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</p>
            <p><strong>Departemen:</strong> {{ $booking->department }}</p>
            <p><strong>Deskripsi:</strong> {{ $booking->description ?? 'Tidak ada deskripsi' }}</p>
        </div>
        
        <p>Silakan siapkan diri Anda untuk meeting yang akan datang.</p>
        
        <a href="{{ route('bookings.view', $booking->id) }}" class="button">Lihat Detail Booking</a>
        
        <p>Jika Anda perlu membatalkan atau mengubah booking, silakan lakukan sesegera mungkin.</p>
        
        <p>Terima kasih,<br>
        Tim Administrasi</p>
    </div>
    
    <div class="footer">
        <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html> 