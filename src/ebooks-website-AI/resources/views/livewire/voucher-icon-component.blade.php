
<div style="max-width: 1200px; margin: 0 auto;">
    <style>
        .voucher-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
            justify-content: center;
        }

        .voucher-item {
            flex: 0 0 calc(33.333% - 20px);
            /* 3 mục trên mỗi hàng, trừ khoảng cách gap */
            max-width: calc(33.333% - 20px);
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1.5px dashed #007bff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-sizing: border-box;
            transition: transform 0.2s;
        }

        .voucher-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .voucher-code {
            font-weight: bold;
            color: #007bff;
            font-size: 18px;
            margin-bottom: 8px;
        }

        .voucher-details {
            font-size: 14px;
            color: #555;
            line-height: 1.5;
            flex-grow: 1;
        }

        .voucher-description {
            font-size: 13px;
            color: #666;
            margin-top: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2; /* Giới hạn 2 dòng */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .copy-btn {
            background-color: #007bff;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            margin-top: 10px;
            align-self: flex-end;
        }

        .copy-btn.copied {
            background-color: #28a745;
        }

        .flash-message {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #28a745;
            color: white;
            padding: 12px 16px;
            border-radius: 6px;
            z-index: 9999;
            font-size: 14px;
            animation: fadeInOut 3s forwards;
        }

        @keyframes fadeInOut {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }

            10% {
                opacity: 1;
                transform: translateY(0);
            }

            90% {
                opacity: 1;
                transform: translateY(0);
            }

            100% {
                opacity: 0;
                transform: translateY(-20px);
            }
        }

        @media (max-width: 768px) {
            .voucher-item {
                flex: 0 0 calc(50% - 20px);
                /* 2 mục trên mỗi hàng cho màn hình nhỏ */
                max-width: calc(50% - 20px);
            }
        }

        @media (max-width: 480px) {
            .voucher-item {
                flex: 0 0 100%;
                /* 1 mục trên mỗi hàng cho màn hình rất nhỏ */
                max-width: 100%;
            }
        }
    </style>
 <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <span></span> Ví Voucher
                </div>
            </div>
        </div>
    <div class="voucher-list">
        
        @forelse($coupons as $coupon)
        @if(filled($coupon->coupon_code))
        <div class="voucher-item">
            <div>
                <div class="voucher-code">{{ $coupon->coupon_code }}</div>
                <div class="voucher-details">
                    {{ $coupon->coupon_type == 'percent' ? number_format($coupon->coupon_value, 0, ',', '.') . '% off' : 'Giảm ' . number_format($coupon->coupon_value, 0, ',', '.') . ' VNĐ' }}
                    @if($coupon->cart_value > 0)
                    <br>(Đơn từ {{ number_format($coupon->cart_value, 0, ',', '.') }} VNĐ)
                    @endif
                    <br>Hạn: {{ \Carbon\Carbon::parse($coupon->end_date)->format('d/m/Y') }}
                    @if($coupon->user_id)
                    <br>Dành riêng cho bạn
                    @endif
                    @if($coupon->description)
                    <div class="voucher-description">{{ $coupon->description }}</div>
                    @endif
                </div>
            </div>
            <button class="copy-btn" onclick="copyCode('{{ $coupon->coupon_code }}')">Sao chép</button>
        </div>
        @endif
        @empty
        <div class="no-vouchers" style="text-align: center; width: 100%; padding: 20px; color: #555;">
            Không có mã giảm giá khả dụng.
        </div>
        @endforelse
    </div>
    <br>

    <script>
        function copyCode(code) {
            code = String(code);
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(code)
                    .then(() => Livewire.dispatch('codeCopied', code))
                    .catch(() => fallbackCopy(code));
            } else {
                fallbackCopy(code);
            }
        }

        function fallbackCopy(code) {
            const textArea = document.createElement('textarea');
            textArea.value = code;
            document.body.appendChild(textArea);
            textArea.select();
            try {
                document.execCommand('copy');
                Livewire.dispatch('codeCopied', code);
            } catch (err) {
                console.error('Copy thất bại:', err);
            }
            document.body.removeChild(textArea);
        }

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('codeCopied', (code) => {
                const buttons = document.querySelectorAll('.copy-btn');
                buttons.forEach(button => {
                    if (button.textContent === 'Sao chép' && button.getAttribute('onclick').includes(code)) {
                        button.textContent = 'Đã sao chép';
                        button.classList.add('copied');
                        setTimeout(() => {
                            button.textContent = 'Sao chép';
                            button.classList.remove('copied');
                        }, 2000);
                    }
                });

                const existing = document.querySelector('.flash-message');
                if (existing) existing.remove();

                const msg = document.createElement('div');
                msg.className = 'flash-message';
                msg.innerText = `Đã sao chép mã: ${code}`;
                document.body.appendChild(msg);
                setTimeout(() => msg.remove(), 3000);
            });
        });
    </script>
</div>