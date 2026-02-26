// Fungsi untuk menampilkan waktu real-time
function updateWaktu() {
    const sekarang = new Date();
    const jam = String(sekarang.getHours()).padStart(2, '0');
    const menit = String(sekarang.getMinutes()).padStart(2, '0');
    const detik = String(sekarang.getSeconds()).padStart(2, '0');
    const hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    const namaHari = hari[sekarang.getDay()];
    const tanggal = sekarang.getDate();
    const namaBulan = bulan[sekarang.getMonth()];
    const tahun = sekarang.getFullYear();
    
    const formatWaktu = `${jam}:${menit}:${detik} | ${namaHari}, ${tanggal} ${namaBulan} ${tahun}`;
    
    document.getElementById('jam').textContent = formatWaktu;
}

// Update waktu setiap detik
updateWaktu();
setInterval(updateWaktu, 1000);

// Animasi scroll halus untuk menu
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Handle contact form submission
const contactForm = document.getElementById('contactForm');
if (contactForm) {
    contactForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
        
        try {
            const response = await fetch('api/handle_kontak.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert('✓ ' + data.message);
                this.reset();
            } else {
                const errorMsg = data.errors ? data.errors.join('\n') : data.message;
                alert('✗ ' + errorMsg);
            }
        } catch (error) {
            alert('✗ Terjadi kesalahan: ' + error.message);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    });
}