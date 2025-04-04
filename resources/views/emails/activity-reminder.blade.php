<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pengingat Aktivitas</title>
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
        .activity-info {
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
        <h1>Pengingat Aktivitas</h1>
    </div>
    
    <div class="content">
        <p>Halo <strong>{{ $activity->name }}</strong>,</p>
        
        <p>Ini adalah pengingat bahwa Anda memiliki aktivitas yang akan dimulai dalam waktu 1 jam lagi.</p>
        
        <div class="activity-info">
            <h3>Detail Aktivitas:</h3>
            <p><strong>Nama Aktivitas:</strong> {{ $activity->name }}</p>
            <p><strong>Departemen:</strong> {{ $activity->department->name }}</p>
            <p><strong>Tipe Aktivitas:</strong> {{ $activity->activity_type }}</p>
            <p><strong>Lokasi:</strong> {{ $activity->province }}, {{ $activity->city }}</p>
            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($activity->activity_date)->format('d F Y') }}</p>
            @if($activity->start_time)
                <p><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($activity->start_time)->format('H:i') }} 
                @if($activity->end_time)
                    - {{ \Carbon\Carbon::parse($activity->end_time)->format('H:i') }}
                @endif
                </p>
            @endif
            <p><strong>Deskripsi:</strong> {{ $activity->description ?? 'Tidak ada deskripsi' }}</p>
        </div>
        
        <p>Silakan siapkan diri Anda untuk aktivitas yang akan datang.</p>
        
        <a href="{{ route('activity.view', $activity->id) }}" class="button">Lihat Detail Aktivitas</a>
        
        <p>Terima kasih,<br>
        Tim Administrasi</p>
    </div>
    
    <div class="footer">
        <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html> 