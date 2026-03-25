document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('cancel-order-modal');
    if (!modal) {
        return;
    }

    const messageEl = modal.querySelector('[data-cancel-order-message]');
    const confirmBtn = modal.querySelector('[data-confirm-cancel-order]');
    const closeSelectors = '[data-cancel-order-close]';
    let currentOrderId = 0;
    let currentOrderLabel = '';

    function closeModal() {
        modal.classList.remove('is-open');
        document.body.classList.remove('modal-open');
        currentOrderId = 0;
        currentOrderLabel = '';

        if (confirmBtn) {
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'CÓ';
        }
    }

    function openModal(orderId, orderLabel) {
        currentOrderId = Number(orderId) || 0;
        currentOrderLabel = orderLabel || ('#' + currentOrderId);

        if (!currentOrderId) {
            return;
        }

        if (messageEl) {
            messageEl.textContent = 'Bạn có muốn hủy đơn hàng ' + currentOrderLabel + ' không?';
        }

        modal.classList.add('is-open');
        document.body.classList.add('modal-open');
    }

    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('.js-open-cancel-order');
        if (trigger) {
            event.preventDefault();
            openModal(trigger.dataset.orderId, trigger.dataset.orderLabel);
            return;
        }

        const closeBtn = event.target.closest(closeSelectors);
        if (closeBtn) {
            event.preventDefault();
            closeModal();
            return;
        }

        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });

    if (!confirmBtn) {
        return;
    }

    confirmBtn.addEventListener('click', function () {
        if (!currentOrderId) {
            closeModal();
            return;
        }

        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Đang hủy...';

        fetch('../api/cancel_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: currentOrderId })
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (!data.success) {
                    throw new Error(data.message || 'Không thể hủy đơn hàng.');
                }

                closeModal();
                alert(data.message || ('Đơn hàng ' + currentOrderLabel + ' đã được hủy.'));
                window.location.reload();
            })
            .catch(function (error) {
                alert(error.message || 'Có lỗi xảy ra khi hủy đơn hàng.');
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'CÓ';
            });
    });
});
