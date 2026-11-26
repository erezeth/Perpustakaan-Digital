/* Interaksi Litera: flash, dialog hapus, ringkasan peminjaman. */
(function () {
    'use strict';

    const flash = document.querySelector('.flash');
    if (flash) {
        setTimeout(() => flash.remove(), 5000);
    }

    // Dialog konfirmasi hapus mengirim form POST sungguhan (dengan CSRF dari layout).
    const modal = document.getElementById('modalHapus');
    const formHapus = document.getElementById('formHapus');
    if (modal && formHapus) {
        const teks = document.getElementById('modalHapusTeks');
        const tombolHapus = document.getElementById('tombolHapus');
        let aksi = '';
        const tutup = () => { modal.hidden = true; };

        document.querySelectorAll('[data-hapus]').forEach((tombol) => {
            tombol.addEventListener('click', () => {
                aksi = tombol.dataset.aksi || '';
                teks.textContent = '“' + (tombol.dataset.hapus || 'data ini') + '” akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.';
                modal.hidden = false;
                tombolHapus.focus();
            });
        });

        modal.querySelectorAll('[data-tutup]').forEach((tombol) => {
            tombol.addEventListener('click', tutup);
        });

        tombolHapus.addEventListener('click', () => {
            if (!aksi) return;
            formHapus.action = aksi;
            formHapus.submit();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.hidden) tutup();
        });
    }

    // Ringkasan form peminjaman: jumlah buku + estimasi tanggal kembali.
    const formPinjam = document.getElementById('formPinjam');
    if (formPinjam) {
        const hari = parseInt(formPinjam.dataset.hari || '7', 10);
        const cek = formPinjam.querySelectorAll('input[name="buku[]"]');
        const jumlah = document.getElementById('jumlahDipilih');
        const ringkas = document.getElementById('jumlahRingkas');
        const tgl = document.getElementById('tglPinjam');
        const estimasi = document.getElementById('estimasiKembali');
        const tglTeks = document.getElementById('tanggalPinjam');
        const bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const format = (d) => d.getDate() + ' ' + bulan[d.getMonth()] + ' ' + d.getFullYear();

        const terapkan = () => {
            const n = Array.from(cek).filter((c) => c.checked).length;
            if (jumlah) jumlah.textContent = n + ' buku';
            if (ringkas) ringkas.textContent = n + ' buku';
            if (tgl && estimasi && tgl.value) {
                const mulai = new Date(tgl.value + 'T00:00:00');
                if (!isNaN(mulai)) {
                    const tempo = new Date(mulai);
                    tempo.setDate(tempo.getDate() + hari);
                    if (tglTeks) tglTeks.textContent = format(mulai);
                    estimasi.textContent = format(tempo);
                }
            }
        };

        cek.forEach((c) => c.addEventListener('change', terapkan));
        if (tgl) tgl.addEventListener('change', terapkan);
        terapkan();
    }
})();
