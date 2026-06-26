-- WildPedia Indonesia — Skema Database
-- Jalankan file ini di MySQL/MariaDB

CREATE DATABASE IF NOT EXISTS wildpedia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE wildpedia;

-- Tabel utama hewan
CREATE TABLE IF NOT EXISTS animals (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name                VARCHAR(255) NOT NULL,
    latin_name          VARCHAR(255) NOT NULL,
    slug                VARCHAR(255) NOT NULL UNIQUE,
    type                VARCHAR(100) NOT NULL,
    status              ENUM('CR','EN','VU','NT','LC','EW','EX') NOT NULL DEFAULT 'LC',
    habitat             TEXT         NOT NULL,
    food                TEXT         NOT NULL,
    population          VARCHAR(100) NULL,
    size                VARCHAR(100) NULL,
    description         TEXT         NOT NULL,
    fun_fact            TEXT         NULL,
    image_url           VARCHAR(500) NULL,
    conservation_action TEXT         NULL,
    is_endemic          TINYINT(1)   NOT NULL DEFAULT 0,
    created_at          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_type   (type),
    INDEX idx_slug   (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel ancaman
CREATE TABLE IF NOT EXISTS threats (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(255) NOT NULL,
    icon       VARCHAR(10)  NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pivot: hewan - ancaman
CREATE TABLE IF NOT EXISTS animal_threat (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    animal_id  INT UNSIGNED NOT NULL,
    threat_id  INT UNSIGNED NOT NULL,
    UNIQUE KEY unique_pair (animal_id, threat_id),
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE,
    FOREIGN KEY (threat_id) REFERENCES threats(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Log identifikasi foto
CREATE TABLE IF NOT EXISTS identify_logs (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    image_path          VARCHAR(500) NOT NULL,
    api_result          JSON         NULL,
    identified_animal   VARCHAR(255) NULL,
    confidence          DECIMAL(5,2) NULL,
    user_ip             VARCHAR(45)  NULL,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contoh data ancaman
INSERT IGNORE INTO threats (name, icon) VALUES
    ('Perburuan Liar', NULL),
    ('Kehilangan Habitat', NULL),
    ('Deforestasi', NULL),
    ('Perdagangan Ilegal', NULL),
    ('Perubahan Iklim', NULL),
    ('Polusi', NULL),
    ('Konflik Manusia-Satwa', NULL);



-- WildPedia Indonesia — Data Hewan
-- Jalankan SETELAH schema.sql

USE wildpedia;

-- -------------------------------------------------------
-- 1. Ancaman
-- -------------------------------------------------------
INSERT INTO threats (name, icon) VALUES
    ('Perburuan liar',           NULL),
    ('Deforestasi',              NULL),
    ('Alih fungsi lahan',        NULL),
    ('Perdagangan ilegal',       NULL),
    ('Konflik dengan manusia',   NULL),
    ('Perubahan iklim',          NULL),
    ('Polusi',                   NULL),
    ('Kebakaran hutan',          NULL),
    ('Tangkap sampingan',        NULL),
    ('Introduksi spesies asing', NULL),
    ('Penyakit',                 NULL),
    ('Kerusakan terumbu karang', NULL),
    ('Pengambilan telur',        NULL),
    ('Pemeliharaan ilegal',      NULL);

-- -------------------------------------------------------
-- 2. Hewan
-- -------------------------------------------------------
INSERT INTO animals
    (name, latin_name, slug, type, status, habitat, food, population, size, description, fun_fact, image_url, conservation_action, is_endemic)
VALUES

-- === KRITIS (CR) ===
(
    'Harimau Sumatera', 'Panthera tigris sumatrae', 'harimau-sumatera',
    'Mamalia', 'CR',
    'Hutan hujan Sumatera, terutama Riau, Aceh, dan Kerinci Seblat',
    'Rusa sambar, babi hutan, tapir, monyet, dan satwa lain',
    '~400 ekor', 'Panjang 2.5 m, berat 140 kg (jantan)',
    'Harimau Sumatera adalah subspesies harimau yang hanya ditemukan di pulau Sumatera, Indonesia. Merupakan subspesies harimau terkecil yang masih hidup dan satu-satunya yang tersisa di Indonesia setelah harimau Bali dan Jawa punah. Tubuhnya lebih gelap dengan garis hitam lebih rapat dibanding subspesies lain.',
    'Harimau Sumatera adalah perenang yang handal dan sering menjaga mangsanya di tepi sungai. Jantan menandai wilayah hingga 250 km². Anak harimau belajar berburu mulai usia 6 bulan.',
    '/wildpedia/public/assets/animals/Harimau_Sumatera.jpg',
    'Dilindungi UU No. 5/1990, penangkaran di Taman Safari, patroli hutan oleh WWF & KLHK',
    1
),
(
    'Orangutan Sumatra', 'Pongo abelii', 'orangutan-sumatra',
    'Mamalia', 'CR',
    'Hutan primer dataran rendah Aceh dan Sumatera Utara',
    'Buah-buahan (60%), daun muda, kulit kayu, serangga, telur burung',
    '~13.000 ekor', 'Tinggi 140 cm, berat 90 kg (jantan)',
    'Orangutan Sumatera adalah primata terbesar Asia dan salah satu kerabat paling dekat manusia dengan kemiripan DNA 96.9%. Mereka hidup soliter dan menghabiskan hampir seluruh hidupnya di atas pohon. Betina hanya melahirkan sekali setiap 8-9 tahun, laju reproduksi paling lambat di antara semua mamalia.',
    'Orangutan menggunakan lebih dari 54 jenis tanaman obat dan satu-satunya hewan non-manusia yang terbukti menggunakan daun sebagai sarung tangan. Mereka membuat "payung" dari daun saat hujan.',
    '/wildpedia/public/assets/animals/Orangutan_Sumatra.webp',
    'Pusat rehabilitasi Sumatran Orangutan Conservation Programme (SOCP), reforestasi habitat',
    1
),
(
    'Badak Jawa', 'Rhinoceros sondaicus', 'badak-jawa',
    'Mamalia', 'CR',
    'Taman Nasional Ujung Kulon, Banten (satu-satunya populasi liar)',
    'Daun, tunas, kulit pohon, buah jatuh',
    'Kurang dari 80 ekor', 'Panjang 3.8 m, berat 1.4–2 ton',
    'Badak Jawa adalah salah satu mamalia terlangka di dunia. Berbeda dengan badak India, badak Jawa hanya memiliki satu cula dan kulitnya berlipat-lipat seperti baju besi. Mereka hewan soliter dan sangat pemalu — sering terekam hanya melalui kamera jebak.',
    'Tidak ada satu pun Badak Jawa yang hidup di penangkaran di seluruh dunia. Kamera jebak di Ujung Kulon menunjukkan individu baru lahir setiap beberapa tahun — tanda populasi masih bisa berkembang biak.',
    '/wildpedia/public/assets/animals/Badak_Jawa.webp',
    'Penjagaan 24 jam di Ujung Kulon, rencana pembentukan habitat kedua di Halimun-Salak',
    1
),
(
    'Gajah Sumatera', 'Elephas maximus sumatranus', 'gajah-sumatera',
    'Mamalia', 'CR',
    'Riau, Aceh, Jambi, Sumatera Selatan, Lampung',
    'Rumput, daun, kulit pohon, akar, buah. Butuh 150 kg/hari',
    '1.700–2.800 ekor', 'Tinggi 2.5 m, berat 2–5 ton',
    'Gajah Sumatera adalah subspesies terkecil gajah Asia, namun tetap merupakan hewan darat terbesar di Indonesia. Mereka hewan sosial yang hidup dalam kawanan dipimpin betina tertua. Gajah berperan sebagai insinyur ekosistem yang membuka jalur di hutan dan menyebarkan biji.',
    'Gajah Sumatera dapat mengenali diri sendiri di cermin — salah satu dari sedikit hewan yang memiliki kesadaran diri. Mereka juga berduka ketika anggota kawanan mati, bahkan mengunjungi tulang-tulangnya.',
    '/wildpedia/public/assets/animals/Gajah_Sumatera.webp',
    'Conservation Response Unit (CRU) KLHK untuk mitigasi konflik gajah-manusia',
    1
),
(
    'Pesut Mahakam', 'Orcaella brevirostris', 'pesut-mahakam',
    'Mamalia Laut', 'CR',
    'Sungai Mahakam, Kalimantan Timur',
    'Ikan, udang sungai, cumi-cumi',
    '~80 ekor', 'Panjang 2.7 m, berat 150 kg',
    'Pesut Mahakam atau lumba-lumba Irrawaddy adalah salah satu cetacea air tawar paling langka di dunia. Berbeda dari lumba-lumba laut, mereka tidak memiliki paruh dan dahinya melengkung bulat. Mereka terkadang bekerja sama dengan nelayan tradisional untuk menggiring ikan.',
    'Pesut Mahakam dikeramatkan oleh masyarakat Dayak dan dianggap sebagai jelmaan manusia. Dalam beberapa legenda, mereka dipercaya sebagai leluhur yang menjaga sungai.',
    '/wildpedia/public/assets/animals/Pesut_Mahakam.webp',
    'Program pemantauan WWF-Indonesia, penetapan zona perlindungan di Mahakam',
    1
),
(
    'Kura-kura Leher Ular Roti', 'Chelodina mccordi', 'kura-kura-leher-ular-roti',
    'Reptil', 'CR',
    'Pulau Roti, Nusa Tenggara Timur',
    'Ikan kecil, katak, invertebrata air',
    'Kurang dari 250 ekor di alam liar', 'Panjang 30 cm, berat 1.2 kg',
    'Kura-kura Leher Ular Roti adalah salah satu kura-kura paling terancam punah di dunia. Nama "leher ular" karena lehernya sangat panjang yang tidak bisa ditarik ke dalam cangkang, melainkan dilipat ke samping. Dikoleksi besar-besaran untuk perdagangan hewan peliharaan pada tahun 1990-an hingga hampir habis.',
    'Kura-kura ini mampu bertahan berbulan-bulan tanpa air saat musim kemarau panjang dengan cara estivasi (hibernasi saat panas). Hampir seluruh populasinya kini ada di penangkaran luar negeri.',
    '/wildpedia/public/assets/animals/Kura_kura_Leher_Ular_Roti.webp',
    'Program repatriasi dari penangkaran luar negeri, penegakan hukum di Pulau Roti',
    1
),

-- === TERANCAM (EN) ===
(
    'Komodo', 'Varanus komodoensis', 'komodo',
    'Reptil', 'EN',
    'Pulau Komodo, Rinca, Flores, Gili Motang (Nusa Tenggara Timur)',
    'Rusa timor, babi hutan, kerbau, kambing, bahkan bangkai',
    '~1.400 ekor', 'Panjang 3 m, berat 70 kg',
    'Komodo adalah kadal terbesar yang masih hidup di dunia dan merupakan satu-satunya naga yang masih ada. Mereka predator puncak yang bisa membunuh mangsa jauh lebih besar dari tubuhnya. Selain gigitan beracunnya, mereka memiliki indera penciuman luar biasa yang bisa mendeteksi mangsa dari jarak 9.5 km.',
    'Komodo betina bisa bereproduksi secara partenogenesis (tanpa jantan). Anak yang lahir dari cara ini selalu jantan, berguna saat populasi terisolasi. Mereka juga bisa berlari hingga 20 km/jam dalam waktu singkat.',
    '/wildpedia/public/assets/animals/Komodo.webp',
    'Taman Nasional Komodo (Situs Warisan Dunia UNESCO), pembatasan wisatawan',
    1
),
(
    'Elang Jawa', 'Nisaetus bartelsi', 'elang-jawa',
    'Burung', 'EN',
    'Hutan primer pegunungan Jawa Barat dan Tengah (500–3.000 mdpl)',
    'Mamalia kecil, reptil, burung, musang',
    '~600–900 pasang', 'Panjang 60 cm, bentang sayap 110 cm',
    'Elang Jawa diyakini sebagai inspirasi lambang negara Garuda Pancasila. Mereka predator puncak ekosistem hutan Jawa dengan jambul mahkota yang khas. Pasangan Elang Jawa setia seumur hidup dan hanya menghasilkan satu anak per tahun — membuat pemulihan populasinya sangat lambat.',
    'Elang Jawa adalah salah satu dari sedikit spesies burung yang memiliki jambul tegak mirip mahkota. Jantan dan betina bersama-sama membangun sarang yang sama selama bertahun-tahun bahkan puluhan tahun.',
    '/wildpedia/public/assets/animals/Elang_Jawa.webp',
    'Pemantauan sarang oleh FLIGHT dan Raptor Indonesia, pelepasliaran hasil sitaan',
    1
),
(
    'Tapir Asia', 'Tapirus indicus', 'tapir-asia',
    'Mamalia', 'EN',
    'Hutan hujan Sumatera dan Semenanjung Malaya',
    'Daun, pucuk, buah, rumput air',
    '~2.500 ekor', 'Panjang 2.5 m, berat 300–350 kg',
    'Tapir Asia adalah tapir terbesar dan satu-satunya spesies tapir yang ada di Asia — kerabatnya ada di Amerika Selatan. Mereka dikenal dengan pola warna hitam-putih yang khas yang berfungsi sebagai kamuflase di hutan malam. Tapir memiliki belalai kecil fleksibel untuk meraih daun.',
    'Tapir adalah tukang kebun hutan terbaik — biji yang mereka makan disebarkan lewat kotoran di area yang luas. Bayi tapir memiliki pola totol dan garis putih yang akan menghilang saat dewasa.',
    '/wildpedia/public/assets/animals/Tapir_Asia.webp',
    'Tapir Conservation Programme, monitoring populasi di TN Kerinci Seblat dan Bukit Barisan',
    0
),
(
    'Hiu Paus', 'Rhincodon typus', 'hiu-paus',
    'Ikan', 'EN',
    'Perairan tropis dan subtropis dunia, termasuk Teluk Cendrawasih Papua',
    'Plankton, telur ikan, ikan kecil, udang krill',
    'Diperkirakan menurun >50% dalam 75 tahun terakhir', 'Panjang rata-rata 12 m, berat 20 ton',
    'Hiu Paus adalah ikan terbesar di dunia namun tidak berbahaya bagi manusia — mereka filter feeder yang memakan plankton dan ikan kecil. Mulutnya bisa mencapai lebar 1.5 meter. Di Indonesia, mereka sering dijumpai di Teluk Cendrawasih, Papua dan Gorontalo.',
    'Hiu Paus bisa hidup hingga 130 tahun — salah satu vertebrata berumur paling panjang. Pola totol di punggung setiap individu unik seperti sidik jari, digunakan peneliti untuk mengidentifikasi individu.',
    '/wildpedia/public/assets/animals/Hiu_Paus.webp',
    'Dilindungi penuh di Indonesia sejak 2013, monitoring di Teluk Cendrawasih',
    0
),
(
    'Bekantan', 'Nasalis larvatus', 'bekantan',
    'Mamalia', 'EN',
    'Hutan mangrove dan riparian Kalimantan',
    'Daun muda, buah mentah, biji',
    '~7.000 ekor', 'Panjang 76 cm, berat 22 kg (jantan)',
    'Bekantan atau proboscis monkey adalah primata endemik Kalimantan yang sangat mudah dikenali dari hidung besar jantan dan warna bulunya yang khas merah-oranye. Mereka hidup dalam kelompok yang dipimpin satu jantan dominan. Bekantan adalah perenang handal yang sering terlihat menyeberangi sungai.',
    'Hidung besar bekantan jantan berfungsi sebagai resonator suara — makin besar hidung, makin dalam suaranya dan makin menarik bagi betina. Betina lebih memilih jantan berhidung besar.',
    '/wildpedia/public/assets/animals/Bekantan.webp',
    'Monitoring populasi di TN Tanjung Puting dan Kutai, program konservasi mangrove',
    1
),
(
    'Merak Hijau', 'Pavo muticus', 'merak-hijau',
    'Burung', 'EN',
    'Padang rumput dan hutan terbuka Jawa, Myanmar, Vietnam',
    'Biji-bijian, buah, serangga, ular kecil, tikus',
    'Kurang dari 30.000 ekor', 'Panjang total (termasuk ekor) 300 cm, berat 5 kg',
    'Merak Hijau adalah burung nasional Indonesia yang terancam punah. Bulu ekornya yang memukau (disebut "kereta") bisa mencapai 160 cm. Saat musim kawin, jantan mengembangkan ekornya dalam formasi kipas dan menari untuk menarik betina. Merak Hijau berbeda dari Merak India yang biru.',
    'Meskipun tampak sangat berat dengan ekor panjangnya, Merak Hijau bisa terbang tinggi ke pohon untuk tidur dan menghindari predator. Setiap tahun ekornya rontok dan tumbuh kembali lebih panjang.',
    '/wildpedia/public/assets/animals/Merak_Hijau.webp',
    'Penangkaran di TMII dan Kebun Binatang Surabaya, patroli di TN Baluran dan Alas Purwo',
    0
),
(
    'Owa Jawa', 'Hylobates moloch', 'owa-jawa',
    'Mamalia', 'EN',
    'Hutan primer Jawa Barat dan Jawa Tengah (0–1500 mdpl)',
    'Buah-buahan (50%), daun muda, serangga',
    '~4.000 ekor', 'Tinggi 58 cm, berat 8 kg',
    'Owa Jawa atau Silvery Gibbon adalah primata endemik Jawa yang terancam punah akibat deforestasi masif. Mereka berpasangan seumur hidup dan mempertahankan wilayah dengan nyanyian duet yang terdengar hingga 2 km. Owa Jawa bergerak dengan cara brakhiasi — berayun dari cabang ke cabang dengan tangan.',
    'Pasangan Owa Jawa menyanyikan duet pagi hari bersama-sama setiap hari — ini bukan sekadar komunikasi, tapi juga cara mempererat ikatan pasangan. Lagu betina berbeda dengan jantan dan sangat khas.',
    '/wildpedia/public/assets/animals/Owa_Jawa.webp',
    'Pusat rehabilitasi Javan Gibbon Center di Gunung Gede, reintroduksi ke hutan',
    1
),
(
    'Kucing Bakau', 'Prionailurus viverrinus', 'kucing-bakau',
    'Mamalia', 'EN',
    'Rawa, hutan bakau, muara sungai Asia Selatan dan Tenggara',
    'Ikan, katak, kepiting, udang, tikus air',
    'Kurang dari 10.000 ekor', 'Panjang 90 cm, berat 12 kg',
    'Kucing Bakau adalah kucing liar berukuran sedang yang teradaptasi untuk kehidupan semi-akuatik. Berbeda dari kucing lain, mereka senang berenang dan menyelam untuk menangkap ikan. Jari-jarinya memiliki selaput renang parsial dan bulu perutnya tebal tahan air.',
    'Kucing Bakau bisa menyelam sepenuhnya ke bawah air untuk mengejar ikan — sesuatu yang sangat langka di antara keluarga kucing. Mereka juga dikenal "memancing" dengan mengetuk permukaan air untuk memancing ikan.',
    '/wildpedia/public/assets/animals/Kucing_Bakau.jpg',
    'Monitoring di kawasan mangrove Sumatera, program pelestarian lahan basah',
    0
),
(
    'Anoa Dataran Rendah', 'Bubalus depressicornis', 'anoa-dataran-rendah',
    'Mamalia', 'EN',
    'Hutan primer Sulawesi dan Pulau Buton',
    'Rumput, pakis, alang-alang, buah',
    'Kurang dari 2.500 ekor', 'Panjang 160 cm, berat 150 kg',
    'Anoa adalah kerbau kerdil endemik Sulawesi, seringkali disebut kerbau kerdil dunia. Ada dua spesies: anoa dataran rendah dan anoa pegunungan. Hewan ini sangat pemalu dan jarang terlihat manusia. Tanduknya lurus pendek berbeda dari kerbau biasa.',
    'Anoa dianggap sebagai nenek moyang kerbau Asia yang terisolasi di Sulawesi jutaan tahun lalu dan berevolusi mengecil. Mereka bisa sangat agresif saat terancam meski tubuhnya kecil.',
    '/wildpedia/public/assets/animals/Anoa_Dataran_Rendah.jpg',
    'Patroli di TN Lore Lindu dan Bogani Nani Wartabone, penangkaran di Sulawesi',
    1
),
(
    'Lutung Jawa', 'Trachypithecus auratus', 'lutung-jawa',
    'Mamalia', 'EN',
    'Hutan Jawa, Bali, dan Lombok',
    'Daun muda (70%), bunga, biji, sedikit buah',
    'Kurang dari 15.000 ekor', 'Panjang 55 cm, berat 7 kg',
    'Lutung Jawa atau Javan Lutung adalah primata dengan bulu hitam mengilap khas. Mereka hidup dalam kelompok 10–20 individu dengan beberapa betina dan satu jantan dominan. Lutung sangat bergantung pada daun muda yang memiliki sistem pencernaan khusus dengan lambung multi-bilik.',
    'Bayi Lutung Jawa lahir berwarna oranye cerah menyala, sangat kontras dengan orang tuanya yang hitam. Warna ini berubah menjadi hitam setelah usia 3–5 bulan. Semua betina dalam kelompok ikut merawat bayi.',
    '/wildpedia/public/assets/animals/Lutung_Jawa.webp',
    'Program konservasi di TN Meru Betiri dan Baluran, pelepasliaran sitaan',
    1
),
(
    'Maleo', 'Macrocephalon maleo', 'maleo',
    'Burung', 'EN',
    'Hutan primer Sulawesi dan Pulau Buton',
    'Biji-bijian, buah, serangga, cacing',
    'Kurang dari 10.000 ekor dewasa', 'Panjang 55 cm, berat 1.7 kg',
    'Maleo adalah megapode endemik Sulawesi yang sangat unik — mereka tidak mengerami telurnya sendiri, melainkan mengubur telur raksasa (sekitar 5x lebih besar dari ayam) di pasir panas dekat pantai atau sumber panas bumi untuk diinkubasi alami.',
    'Anak Maleo saat menetas sudah bisa terbang langsung! Mereka keluar sendiri dari tanah setelah menggali selama 2 hari, dan dalam beberapa jam sudah bisa terbang. Tidak ada induk yang menunggu atau merawat.',
    '/wildpedia/public/assets/animals/Maleo.jpg',
    'Program perlindungan pantai peneluran di Sulawesi, WCS Maleo Conservation Project',
    1
),

-- === RENTAN (VU) ===
(
    'Penyu Belimbing', 'Dermochelys coriacea', 'penyu-belimbing',
    'Reptil', 'VU',
    'Pantai Jamursba Medi (Papua), perairan tropis dan subtropis',
    'Ubur-ubur, moluska laut, cumi-cumi',
    'Populasi global menurun drastis', 'Panjang 2 m, berat 700 kg',
    'Penyu Belimbing adalah reptil terbesar di dunia dan penyu terbesar di antara semua spesies penyu. Berbeda dari penyu lain, cangkangnya bukan dari tulang keras tapi dari kulit berkeratina elastis. Papua memiliki salah satu pantai peneluran penyu belimbing terpenting di dunia.',
    'Penyu Belimbing bisa menyelam hingga kedalaman 1.200 meter dan mampu bertahan di air bersuhu dingin berkat sistem peredaran darah khusus. Mereka bisa menempuh 16.000 km migrasi antara pantai makan dan pantai bertelur.',
    '/wildpedia/public/assets/animals/Penyu_Belimbing.jpg',
    'Perlindungan pantai di Jamursba Medi Papua, program adopsi sarang penyu',
    0
),
(
    'Kukang Sumatera', 'Nycticebus coucang', 'kukang-sumatera',
    'Mamalia', 'VU',
    'Hutan Sumatera, Semenanjung Malaya, Thailand Selatan',
    'Getah pohon, nektar, serangga, buah',
    'Populasi menurun signifikan', 'Panjang 37 cm, berat 0.7 kg',
    'Kukang adalah primata nokturnal berbisa satu-satunya di dunia. Mereka bergerak sangat lambat dan memiliki mata besar untuk melihat di malam hari. Popularitasnya di media sosial justru meningkatkan perburuan untuk dijual sebagai hewan peliharaan ilegal.',
    'Kukang menghasilkan racun dari kelenjar di lengannya. Saat merasa terancam, mereka mengangkat lengan dan mencampur sekret dengan air liur untuk menciptakan gigitan beracun yang bisa menyebabkan syok anafilaktik.',
    '/wildpedia/public/assets/animals/Kukang_Sumatera.jpg',
    'Pusat rehabilitasi IAR Indonesia di Bogor, kampanye kesadaran anti-peliharaan liar',
    0
),
(
    'Rangkong Badak', 'Buceros rhinoceros', 'rangkong-badak',
    'Burung', 'VU',
    'Hutan hujan Sumatera, Kalimantan, Semenanjung Malaya',
    'Buah ara, kadal, katak, serangga besar',
    'Menurun, populasi tidak diketahui pasti', 'Panjang 120 cm, berat 2.7 kg',
    'Rangkong Badak adalah salah satu burung terbesar di hutan Indonesia dengan casque (tanduk di atas paruh) berwarna merah-oranye yang mencolok. Betina menutup diri dalam rongga pohon saat mengerami telur, sementara jantan memberi makan lewat celah sempit. Mereka bisa hidup 35 tahun.',
    'Casque Rangkong Badak bukan tulang berongga seperti yang diperkirakan — melainkan padat. Ini menjadikannya target perburuan ilegal karena dipercaya memiliki khasiat obat dan dijual mahal di Tiongkok.',
    '/wildpedia/public/assets/animals/Rangkong_Badak.webp',
    'FLIGHT Protecting Indonesian Birds, penegakan hukum anti-perburuan di Kalimantan',
    0
),
(
    'Babi Rusa', 'Babyrousa babyrussa', 'babi-rusa',
    'Mamalia', 'VU',
    'Hutan hujan Sulawesi dan Pulau Maluku',
    'Akar, buah jatuh, jamur, daun, invertebrata',
    'Kurang dari 4.000 ekor', 'Panjang 100 cm, berat 100 kg',
    'Babi Rusa adalah babi liar endemik Sulawesi yang terkenal dengan taring atasnya yang tumbuh menembus kulit moncong ke atas. Secara taksonomi lebih dekat ke kuda nil daripada babi biasa. Mereka omnivora yang sangat penting dalam penyebaran biji-bijian hutan.',
    'Taring atas Babi Rusa jantan terus tumbuh sepanjang hidupnya. Jika tidak patah, taring ini bisa melengkung hingga menusuk tengkoraknya sendiri. Ini adalah satu-satunya hewan di mana gigi bisa membunuh pemiliknya.',
    '/wildpedia/public/assets/animals/Babi_Rusa.webp',
    'Penangkaran di Kebun Binatang Surabaya, perlindungan habitat di TN Tangkoko',
    1
),
(
    'Tarsius Sulawesi', 'Tarsius tarsier', 'tarsius-sulawesi',
    'Mamalia', 'VU',
    'Hutan sekunder dan primer Sulawesi',
    'Serangga, kadal kecil, kepiting, kelelawar kecil',
    'Populasi menurun', 'Panjang 16 cm, berat 135 gram',
    'Tarsius adalah primata terkecil di dunia dan satu-satunya primata karnivora eksklusif. Matanya yang sangat besar tidak bisa bergerak dalam rongganya, sehingga mereka harus memutar seluruh kepala seperti burung hantu hingga 180°. Tarsius aktif di malam hari dan bisa melompat hingga 40 kali panjang tubuhnya.',
    'Mata tarsius, jika proporsional dengan ukuran tubuh manusia, akan sebesar grapefruit. Pendengaran mereka sangat tajam dan bisa mendeteksi ultrasonik. Saat stres berat, tarsius bisa bunuh diri dengan menghantam kepala ke benda keras.',
    '/wildpedia/public/assets/animals/Tarsius_Sulawesi.webp',
    'Sanctuary Tarsius di Tangkoko Nature Reserve Sulawesi Utara',
    1
),
(
    'Kepiting Kenari', 'Birgus latro', 'kepiting-kenari',
    'Krustasea', 'VU',
    'Pulau-pulau tropis terpencil di Indo-Pasifik',
    'Kelapa, buah jatuh, kacang-kacangan, bangkai',
    'Menurun di sebagian besar pulau berpenghuni', 'Lebar 40 cm, berat 4 kg',
    'Kepiting Kenari adalah artropoda daratan terbesar di dunia. Mereka aktif di malam hari, bisa memanjat pohon kelapa dan membuka buah kelapa yang keras dengan capitnya yang sangat kuat. Kepiting ini bisa hidup lebih dari 60 tahun dan membutuhkan 5 tahun untuk mencapai dewasa.',
    'Capit Kepiting Kenari bisa menahan beban 29 kali berat tubuhnya sendiri — secara proporsional adalah kekuatan terkuat di antara semua hewan. Mereka punya penciuman luar biasa dan bisa mencium kelapa jatuh dari jarak sangat jauh.',
    '/wildpedia/public/assets/animals/Kepiting_Kenari.webp',
    'Perlindungan di Kepulauan Christmas, monitoring di Kepulauan Natuna',
    0
),

-- === TIDAK TERANCAM (LC) ===
(
    'Buaya Muara', 'Crocodylus porosus', 'buaya-muara',
    'Reptil', 'LC',
    'Muara sungai, hutan bakau, dan perairan pesisir Asia Tenggara hingga Australia',
    'Ikan, mamalia, burung, penyu, bahkan hiu',
    'Stabil (~200.000 ekor global)', 'Panjang 5 m, berat 500 kg (jantan)',
    'Buaya Muara adalah reptil terbesar yang masih hidup di dunia. Mereka predator puncak perairan pesisir yang bisa hidup di air tawar maupun asin. Meski statusnya LC secara global, populasi lokal di banyak wilayah Indonesia sudah sangat berkurang akibat perburuan.',
    'Buaya Muara memiliki kekuatan gigitan terkuat di antara semua hewan yang pernah diukur — sekitar 16.000 Newton. Namun otot untuk membuka rahang sangat lemah; bisa ditahan dengan karet gelang.',
    '/wildpedia/public/assets/animals/Buaya_Muara.jpg',
    'Pengelolaan berbasis komunitas, penangkaran di beberapa provinsi',
    0
),
(
    'Cenderawasih Kuning Kecil', 'Paradisaea minor', 'cenderawasih-kuning-kecil',
    'Burung', 'LC',
    'Hutan dataran rendah Papua dan Maluku Utara',
    'Buah-buahan, artropoda, nektar',
    'Stabil', 'Panjang 32 cm (tidak termasuk pita ekor)',
    'Cenderawasih Kuning Kecil atau Lesser Bird-of-paradise adalah salah satu dari 42 spesies cenderawasih yang ada di Papua. Jantan memiliki bulu kuning-emas memukau dan dua pita ekor panjang yang digunakan dalam tarian kawin spektakuler. Dijuluki "burung surga" oleh penjelajah Eropa abad ke-16.',
    'Tarian kawin cenderawasih jantan adalah salah satu pertunjukan alam paling rumit di dunia. Mereka berlatih gerakan selama bertahun-tahun sebelum cukup mahir untuk menarik betina. Betina memilih jantan berdasarkan kualitas tarian, bukan hanya penampilan.',
    '/wildpedia/public/assets/animals/Cenderawasih_Kuning_Kecil.webp',
    'Perlindungan habitat di TN Lorentz Papua, larangan perburuan',
    1
),
(
    'Kancil', 'Tragulus javanicus', 'kancil',
    'Mamalia', 'LC',
    'Hutan dan semak belukar Asia Tenggara, termasuk seluruh Indonesia',
    'Buah jatuh, daun, fungi, biji-bijian',
    'Stabil', 'Panjang 45 cm, berat 2 kg',
    'Kancil adalah ungulata (hewan berkuku) terkecil di dunia — lebih kecil dari kucing rumahan. Mereka bukan kelinci maupun rusa, tapi berada di kelompok tersendiri (chevrotain). Kancil terkenal dalam cerita rakyat Indonesia sebagai hewan yang sangat cerdik.',
    'Kancil jantan punya taring kecil yang mencuat dari mulut — digunakan untuk berduel memperebutkan betina. Meski kecil, mereka bisa berlari sangat cepat dalam zigzag untuk menghindari predator, sering menceburkan diri ke air.',
    '/wildpedia/public/assets/animals/Kancil.jpg',
    'Tidak memerlukan konservasi khusus, namun penting dijaga habitatnya',
    0
),
(
    'Monyet Ekor Panjang', 'Macaca fascicularis', 'monyet-ekor-panjang',
    'Mamalia', 'LC',
    'Seluruh Asia Tenggara termasuk Indonesia',
    'Buah, biji, serangga, kepiting, katak, telur burung',
    'Sangat stabil', 'Panjang 50 cm, berat 8 kg',
    'Monyet Ekor Panjang atau Macaque Kera adalah primata paling umum dijumpai di Indonesia. Mereka sangat adaptif dan bisa hidup di berbagai habitat, dari hutan primer hingga kota. Di pantai, kelompok tertentu telah mengembangkan tradisi menggunakan batu untuk memecah kerang.',
    'Monyet Ekor Panjang di beberapa pantai telah mengembangkan budaya memancing kepiting dengan menggunakan daun sebagai alat. Kebiasaan ini diajarkan turun-temurun dari induk ke anak.',
    '/wildpedia/public/assets/animals/Monyet_Ekor_Panjang.webp',
    'Tidak perlu konservasi khusus secara global',
    0
);

-- -------------------------------------------------------
-- 3. Relasi ancaman
-- -------------------------------------------------------
INSERT INTO animal_threat (animal_id, threat_id)
SELECT a.id, t.id FROM animals a, threats t WHERE
    (a.slug='harimau-sumatera'      AND t.name IN ('Perburuan liar','Deforestasi','Konflik dengan manusia','Perdagangan ilegal')) OR
    (a.slug='orangutan-sumatra'     AND t.name IN ('Deforestasi','Kebakaran hutan','Perdagangan ilegal','Alih fungsi lahan')) OR
    (a.slug='badak-jawa'            AND t.name IN ('Perburuan liar','Alih fungsi lahan','Perubahan iklim')) OR
    (a.slug='gajah-sumatera'        AND t.name IN ('Konflik dengan manusia','Deforestasi','Perburuan liar','Alih fungsi lahan')) OR
    (a.slug='pesut-mahakam'         AND t.name IN ('Tangkap sampingan','Polusi','Alih fungsi lahan')) OR
    (a.slug='kura-kura-leher-ular-roti' AND t.name IN ('Perdagangan ilegal','Pemeliharaan ilegal','Perubahan iklim')) OR
    (a.slug='komodo'                AND t.name IN ('Perubahan iklim','Perburuan liar','Alih fungsi lahan')) OR
    (a.slug='elang-jawa'            AND t.name IN ('Deforestasi','Perburuan liar','Perdagangan ilegal')) OR
    (a.slug='tapir-asia'            AND t.name IN ('Deforestasi','Perburuan liar','Alih fungsi lahan','Konflik dengan manusia')) OR
    (a.slug='hiu-paus'              AND t.name IN ('Tangkap sampingan','Perburuan liar','Polusi')) OR
    (a.slug='bekantan'              AND t.name IN ('Deforestasi','Alih fungsi lahan','Perburuan liar')) OR
    (a.slug='merak-hijau'           AND t.name IN ('Perburuan liar','Alih fungsi lahan','Perdagangan ilegal')) OR
    (a.slug='owa-jawa'              AND t.name IN ('Deforestasi','Perdagangan ilegal','Pemeliharaan ilegal')) OR
    (a.slug='kucing-bakau'          AND t.name IN ('Alih fungsi lahan','Polusi','Perdagangan ilegal')) OR
    (a.slug='anoa-dataran-rendah'   AND t.name IN ('Perburuan liar','Alih fungsi lahan','Penyakit')) OR
    (a.slug='lutung-jawa'           AND t.name IN ('Deforestasi','Perdagangan ilegal','Pemeliharaan ilegal')) OR
    (a.slug='maleo'                 AND t.name IN ('Pengambilan telur','Introduksi spesies asing','Deforestasi')) OR
    (a.slug='penyu-belimbing'       AND t.name IN ('Tangkap sampingan','Polusi','Pengambilan telur','Perubahan iklim')) OR
    (a.slug='kukang-sumatera'       AND t.name IN ('Perdagangan ilegal','Pemeliharaan ilegal','Deforestasi')) OR
    (a.slug='rangkong-badak'        AND t.name IN ('Perdagangan ilegal','Deforestasi','Perburuan liar')) OR
    (a.slug='babi-rusa'             AND t.name IN ('Perburuan liar','Alih fungsi lahan')) OR
    (a.slug='tarsius-sulawesi'      AND t.name IN ('Deforestasi','Perdagangan ilegal','Pemeliharaan ilegal')) OR
    (a.slug='kepiting-kenari'       AND t.name IN ('Perburuan liar','Alih fungsi lahan')) OR
    (a.slug='buaya-muara'           AND t.name IN ('Perburuan liar','Alih fungsi lahan','Konflik dengan manusia')) OR
    (a.slug='cenderawasih-kuning-kecil' AND t.name IN ('Perdagangan ilegal','Deforestasi')) OR
    (a.slug='kancil'                AND t.name IN ('Perburuan liar','Alih fungsi lahan')) OR
    (a.slug='monyet-ekor-panjang'   AND t.name IN ('Konflik dengan manusia','Alih fungsi lahan'));
-- -------------------------------------------------------
-- Tabel users (akun pengunjung biasa, untuk register/login & komentar)
-- Dibuat SEBELUM comments karena comments punya FK ke users
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------
-- Tabel komentar pengunjung
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS comments (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    animal_id  INT UNSIGNED NOT NULL,
    user_id    INT UNSIGNED NULL,
    nama       VARCHAR(100) NOT NULL,
    isi        TEXT NOT NULL,
    approved   TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE SET NULL,
    INDEX idx_animal   (animal_id),
    INDEX idx_approved (approved),
    INDEX idx_user     (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------
-- Tabel admin
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS admins (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(100) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin default: username = admin | password = admin123
INSERT IGNORE INTO admins (username, password)
VALUES ('admin', '$2y$10$ZQHNFlr3R3fMdyew2ovJQ.DCJXowuAkVXnoTxCfJifOvZFptJ6fX2');