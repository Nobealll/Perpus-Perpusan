<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = 'Beranda';
require __DIR__ . '/components/header.php';
require __DIR__ . '/components/navbar.php';
?>
<main class="landing wrap">
    <section class="hero-grid">
        <div class="hero-copy">
            <span class="eyebrow">RUANG BACA KAMPUS</span>
            <h1>Buku yang tepat bisa membuka <span>babak baru.</span></h1>
            <p>Semua kebutuhan membaca dan meminjam buku kini lebih mudah. Cari buku favoritmu, pinjam dengan praktis, dan pantau riwayat bacaanmu kapan saja.</p>
            <div class="button-row">
                <a class="button" href="<?= current_user() ? 'dashboard.php' : 'login.php' ?>">Mulai membaca <span aria-hidden="true">→</span></a>
                <a class="button button-light" href="katalog.php">Lihat katalog</a>
            </div>
            <div class="hero-note"><span class="note-dot"></span> Layanan untuk mahasiswa dan petugas perpus perpusan</div>
        </div>
        <div class="hero-art" aria-label="Ilustrasi buku terbuka">
            <div class="sun-shape"></div>
            <div class="book-stack">
                <div class="book book-yellow">IDE BESAR</div>
                <div class="book book-blue">CATATAN KECIL</div>
                <div class="book book-red">CERITA KITA</div>
            </div>
            <span class="art-spark spark-one">✳</span><span class="art-spark spark-two">✦</span>
            <div class="art-caption">Buka buku.<br>Temukan dunia.</div>
        </div>
    </section>
    <section class="service-strip">
        <article><span class="service-icon">01</span>
            <div>
                <h2>Cari koleksi</h2>
                <p>Temukan buku dari berbagai kategori.</p>
            </div>
        </article>
        <article><span class="service-icon icon-blue">02</span>
            <div>
                <h2>Pinjam praktis</h2>
                <p>Ajukan peminjaman dengan beberapa langkah.</p>
            </div>
        </article>
        <article><span class="service-icon icon-red">03</span>
            <div>
                <h2>Pantau riwayat</h2>
                <p>Lihat status dan tanggal pengembalian.</p>
            </div>
        </article>
    </section>
</main>
<?php require __DIR__ . '/components/footer.php'; ?>
