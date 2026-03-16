// ============================================================
//  ADMIN PANEL JS - Thuận Phát Garden
// ============================================================

document.addEventListener('DOMContentLoaded', function() {

    // ===== SIDEBAR TOGGLE (Mobile) =====
    const toggleBtn = document.querySelector('.btn-toggle-sidebar');
    const sidebar = document.querySelector('.admin-sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay?.classList.toggle('active');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        });
    }

    // ===== DELETE CONFIRMATION =====
    window.confirmDelete = function(productId, productName) {
        const modal = document.getElementById('deleteModal');
        const nameSpan = document.getElementById('deleteProductName');
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        
        if (modal && nameSpan && confirmBtn) {
            nameSpan.textContent = productName;
            modal.classList.add('active');
            
            confirmBtn.onclick = function() {
                fetch('api/delete_product.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: productId })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra!');
                    }
                    modal.classList.remove('active');
                })
                .catch(() => {
                    alert('Lỗi kết nối!');
                    modal.classList.remove('active');
                });
            };
        }
    };

    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.remove('active');
    };

    // ===== UPDATE ORDER STATUS =====
    window.updateOrderStatus = function(orderId, newStatus) {
        fetch('api/update_order_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: orderId, status: newStatus })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Show brief success feedback
                const select = document.querySelector(`select[data-order-id="${orderId}"]`);
                if (select) {
                    select.style.borderColor = '#22c55e';
                    setTimeout(() => {
                        select.style.borderColor = '';
                    }, 1500);
                }
            } else {
                alert(data.message || 'Có lỗi xảy ra!');
            }
        })
        .catch(() => {
            alert('Lỗi kết nối!');
        });
    };

    // ===== IMAGE PREVIEW =====
    const imgInput = document.getElementById('hinh_chinh');
    const imgPreview = document.getElementById('imgPreview');
    
    if (imgInput && imgPreview) {
        imgInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgPreview.src = e.target.result;
                    imgPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // ===== MULTIPLE IMAGE PREVIEW =====
    const galleryInput = document.getElementById('thu_vien_anh');
    const galleryPreview = document.getElementById('galleryPreview');
    
    if (galleryInput && galleryPreview) {
        galleryInput.addEventListener('change', function() {
            galleryPreview.innerHTML = ''; // Clear previous previews
            Array.from(this.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const item = document.createElement('div');
                    item.className = 'gallery-item';
                    item.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <span class="preview-badge">Mới</span>
                    `;
                    galleryPreview.appendChild(item);
                };
                reader.readAsDataURL(file);
            });
        });
    }

    // ===== DELETE PRODUCT IMAGE (AJAX) =====
    window.deleteProductImage = function(imageId, btnElement) {
        if (!confirm('Bạn có chắc muốn xóa ảnh này khỏi thư viện?')) return;
        
        fetch('api/delete_product_image.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: imageId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const item = btnElement.closest('.gallery-item');
                if (item) {
                    item.style.opacity = '0';
                    setTimeout(() => item.remove(), 300);
                }
            } else {
                alert(data.message || 'Có lỗi xảy ra!');
            }
        })
        .catch(() => {
            alert('Lỗi kết nối!');
        });
    };

    // ===== FORMAT CURRENCY INPUT =====
    document.querySelectorAll('.currency-input').forEach(input => {
        input.addEventListener('blur', function() {
            let val = this.value.replace(/[^0-9]/g, '');
            if (val) {
                this.value = parseInt(val).toLocaleString('vi-VN');
            }
        });
        input.addEventListener('focus', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });
});
