/**
 * Custom Location Data for Country, Province, and City selection
 * Created as an alternative to Laravel Countries package
 */
const locationData = {
    Indonesia: {
        provinces: [
            'Aceh', 'Bali', 'Bangka Belitung', 'Banten', 'Bengkulu', 
            'DKI Jakarta', 'Daerah Istimewa Yogyakarta', 'Gorontalo', 'Jambi',
            'Jawa Barat', 'Jawa Tengah', 'Jawa Timur', 'Kalimantan Barat',
            'Kalimantan Selatan', 'Kalimantan Tengah', 'Kalimantan Timur', 'Kalimantan Utara',
            'Kepulauan Riau', 'Lampung', 'Maluku', 'Maluku Utara', 'Nusa Tenggara Barat',
            'Nusa Tenggara Timur', 'Papua', 'Papua Barat', 'Riau', 'Sulawesi Barat',
            'Sulawesi Selatan', 'Sulawesi Tengah', 'Sulawesi Tenggara', 'Sulawesi Utara',
            'Sumatera Barat', 'Sumatera Selatan', 'Sumatera Utara'
        ],
        cities: {
            'DKI Jakarta': ['Jakarta Pusat', 'Jakarta Utara', 'Jakarta Barat', 'Jakarta Selatan', 'Jakarta Timur', 'Kepulauan Seribu'],
            'Jawa Barat': ['Bandung', 'Bekasi', 'Bogor', 'Cimahi', 'Cirebon', 'Depok', 'Sukabumi', 'Tasikmalaya', 'Banjar', 
                        'Kabupaten Bandung', 'Kabupaten Bandung Barat', 'Kabupaten Bekasi', 'Kabupaten Bogor', 'Kabupaten Ciamis', 
                        'Kabupaten Cianjur', 'Kabupaten Cirebon', 'Kabupaten Garut', 'Kabupaten Indramayu', 'Kabupaten Karawang', 
                        'Kabupaten Kuningan', 'Kabupaten Majalengka', 'Kabupaten Purwakarta', 'Kabupaten Subang', 
                        'Kabupaten Sukabumi', 'Kabupaten Sumedang', 'Kabupaten Tasikmalaya'],
            'Jawa Tengah': ['Semarang', 'Surakarta', 'Pekalongan', 'Salatiga', 'Tegal', 'Magelang', 'Kabupaten Banjarnegara', 
                        'Kabupaten Banyumas', 'Kabupaten Batang', 'Kabupaten Blora', 'Kabupaten Boyolali', 'Kabupaten Brebes', 
                        'Kabupaten Cilacap', 'Kabupaten Demak', 'Kabupaten Grobogan', 'Kabupaten Jepara', 'Kabupaten Karanganyar'],
            'Jawa Timur': ['Surabaya', 'Malang', 'Kediri', 'Batu', 'Blitar', 'Madiun', 'Mojokerto', 'Pasuruan', 'Probolinggo'],
            'Bali': ['Denpasar', 'Kabupaten Badung', 'Kabupaten Bangli', 'Kabupaten Buleleng', 'Kabupaten Gianyar', 
                   'Kabupaten Jembrana', 'Kabupaten Karangasem', 'Kabupaten Klungkung', 'Kabupaten Tabanan'],
            'Aceh': ['Banda Aceh', 'Langsa', 'Lhokseumawe', 'Sabang', 'Subulussalam'],
            // Tambahkan lebih banyak provinsi dan kota sesuai kebutuhan
        }
    },
    Malaysia: {
        provinces: ['Johor', 'Kedah', 'Kelantan', 'Kuala Lumpur', 'Labuan', 'Malacca', 'Negeri Sembilan', 
                   'Pahang', 'Penang', 'Perak', 'Perlis', 'Putrajaya', 'Sabah', 'Sarawak', 'Selangor', 'Terengganu'],
        cities: {
            'Kuala Lumpur': ['Kuala Lumpur'],
            'Selangor': ['Shah Alam', 'Petaling Jaya', 'Subang Jaya', 'Klang', 'Ampang Jaya', 'Kajang']
        }
    },
    Singapore: {
        provinces: ['Central Region', 'East Region', 'North Region', 'North-East Region', 'West Region'],
        cities: {
            'Central Region': ['Downtown Core', 'Marina East', 'Marina South', 'Museum', 'Newton', 'Orchard']
        }
    },
    Thailand: {
        provinces: ['Bangkok', 'Chiang Mai', 'Chiang Rai', 'Khon Kaen', 'Pattaya', 'Phuket'],
        cities: {
            'Bangkok': ['Bangkok'],
            'Phuket': ['Patong', 'Phuket Town']
        }
    },
    Philippines: {
        provinces: ['Metro Manila', 'Cebu', 'Davao', 'Iloilo', 'Negros Occidental', 'Cagayan de Oro'],
        cities: {
            'Metro Manila': ['Manila', 'Quezon City', 'Makati', 'Taguig', 'Pasig', 'Pasay']
        }
    }
}; 