document.addEventListener('DOMContentLoaded', function() {
    
    // Sayfadaki tüm favori butonlarını seç
    const favoriteButtons = document.querySelectorAll('.favorite-btn');

    favoriteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Butonun varsayılan davranışını engelle

            const productId = this.dataset.productId;
            const siteUrl = document.body.dataset.siteUrl; // Site URL'ini body'den alacağız

            fetch(siteUrl + '/toggle_favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ product_id: productId }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // İşlem başarılıysa, butonun stilini değiştir
                    if (data.action === 'added') {
                        this.classList.add('active');
                    } else {
                        this.classList.remove('active');
                    }
                } else {
                    // Hata varsa, kullanıcıyı uyar (örn: login değilse)
                    alert(data.message || 'Bir hata oluştu.');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
