<div>
    <main class="main">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href="{{ route('home') }}" rel="nofollow" style="color: #F15412;">Trang chủ</a>
                    <span></span> Liên hệ
                </div>
            </div>
        </div>

        <div class="container py-5">
            <div class="row">
                <!-- Thông tin liên hệ -->
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="contact-info-card">
                        <div class="contact-info-header">
                            <h3>Thông tin liên hệ</h3>
                            <p>Chúng tôi luôn sẵn sàng hỗ trợ bạn</p>
                        </div>

                        <div class="contact-info-item">
                            <div class="icon-box">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="info-content">
                                <h5>Địa chỉ</h5>
                                <p>126 Nguyễn Thiện Thành, Phường 5, Trà Vinh</p>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <div class="icon-box">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="info-content">
                                <h5>Điện thoại</h5>
                                <p>+84 795 405 536</p>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <div class="icon-box">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="info-content">
                                <h5>Email</h5>
                                <p>iamkimngan197@gmail.com</p>
                            </div>
                        </div>



                        <div class="social-links mt-4">
                            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Form liên hệ -->
                <div class="col-lg-8">
                    <div class="contact-box">
                        <div wire:loading class="loading-overlay">
                            <div class="spinner"></div>
                        </div>

                        @if(session()->has('success'))
                        <div class="alert alert-success animate__animated animate__fadeIn">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                        @endif

                        @if(session()->has('error'))
                        <div class="alert alert-danger animate__animated animate__fadeIn">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        </div>
                        @endif

                        <div class="contact-header">
                            <h2>Liên hệ với chúng tôi</h2>
                            <p>Chúng tôi rất mong nhận được phản hồi từ bạn</p>
                        </div>

                        <form wire:submit.prevent="submit" class="contact-form">
                            <div class="form-grid">
                                <!-- Trường tên -->
                                <div class="form-group">
                                    <label><i class="fas fa-user me-2"></i>Họ và tên *</label>
                                    <input type="text" wire:model.defer="name"
                                        placeholder="Nhập họ tên của bạn"
                                        class="@error('name') is-invalid @enderror"
                                        @if($isAuthenticated) readonly @endif>
                                    @error('name')<small class="error-text">{{ $message }}</small>@enderror
                                </div>

                                <!-- Trường email -->
                                <div class="form-group">
                                    <label><i class="fas fa-envelope me-2"></i>Email *</label>
                                    <input type="email" wire:model.defer="email"
                                        placeholder="Nhập email của bạn"
                                        class="@error('email') is-invalid @enderror"
                                        @if($isAuthenticated) readonly @endif>
                                    @error('email')<small class="error-text">{{ $message }}</small>@enderror
                                </div>

                                <!-- Trường điện thoại -->
                                <div class="form-group">
                                    <label><i class="fas fa-phone me-2"></i>Số điện thoại *</label>
                                    <input type="tel" wire:model.defer="telephone"
                                        placeholder="Nhập số điện thoại của bạn"
                                        pattern="[0-9]{10,11}"
                                        class="@error('telephone') is-invalid @enderror"
                                        @if($isAuthenticated && !empty($telephone)) readonly @endif>
                                    @error('telephone')<small class="error-text">{{ $message }}</small>@enderror
                                </div>

                                <!-- Trường chủ đề -->
                                <div class="form-group">
                                    <label><i class="fas fa-tag me-2"></i>Chủ đề *</label>
                                    <input type="text" wire:model.defer="subject"
                                        placeholder="Góp ý, hỗ trợ..."
                                        class="@error('subject') is-invalid @enderror">
                                    @error('subject')<small class="error-text">{{ $message }}</small>@enderror
                                </div>
                            </div>

                            <!-- Trường nội dung -->
                            <div class="form-group full">
                                <label><i class="fas fa-comment me-2"></i>Nội dung tin nhắn *</label>
                                <textarea rows="5" wire:model.defer="message"
                                    placeholder="Nhập nội dung chi tiết..."
                                    class="@error('message') is-invalid @enderror"></textarea>
                                @error('message')<small class="error-text">{{ $message }}</small>@enderror
                            </div>

                            <!-- Hiển thị thông báo yêu cầu đăng nhập cho khách -->
                            @if(!$isAuthenticated)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>Đăng nhập để lưu lịch sử liên hệ của bạn!
                            </div>
                            @endif

                            <button type="submit" wire:loading.attr="disabled" class="btn-submit">
                                <span wire:loading.remove><i class="fas fa-paper-plane me-2"></i>Gửi tin nhắn</span>
                                <span wire:loading><i class="fas fa-spinner fa-spin me-2"></i>Đang gửi...</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Bản đồ -->
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="map-container">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3929.9553734777!2d106.33999807465055!3d9.923755674334188!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31a0175ea296facb%3A0x55abe9d585644a2e!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBUcsOgIFZpbmg!5e0!3m2!1svi!2s!4v1718097845830!5m2!1svi!2s"
                                width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                /* Chung */
                .main {
                    background-color: #f8f9fa;
                    font-family: 'Nunito', sans-serif;
                }

                /* Breadcrumb */
                .page-header {
                    background: #ffffff;
                    border-bottom: 1px solid #ddd;
                    padding: 1rem 0;
                    margin-bottom: 2rem;
                }

                .breadcrumb {
                    font-size: 0.95rem;
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    color: #555;
                }

                .breadcrumb a {
                    color: #F15412;
                    text-decoration: none;
                    font-weight: 600;
                    transition: color 0.3s;
                }

                .breadcrumb a:hover {
                    color: #d13700;
                }

                .breadcrumb span {
                    display: inline-block;
                    width: 6px;
                    height: 6px;
                    border-radius: 50%;

                    margin: 0 5px;
                }

                /* Thông tin liên hệ */
                .contact-info-card {
                    background: white;
                    border-radius: 12px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
                    padding: 2rem;
                    height: 100%;
                    transition: transform 0.3s;
                }

                .contact-info-card:hover {
                    transform: translateY(-5px);
                }

                .contact-info-header {
                    margin-bottom: 2rem;
                    border-bottom: 1px solid #eee;
                    padding-bottom: 1rem;
                }

                .contact-info-header h3 {
                    color: #333;
                    font-size: 1.5rem;
                    margin-bottom: 0.5rem;
                    font-weight: 700;
                }

                .contact-info-header p {
                    color: #666;
                    margin-bottom: 0;
                }

                .contact-info-item {
                    display: flex;
                    margin-bottom: 1.5rem;
                    align-items: flex-start;
                }

                .icon-box {
                    width: 40px;
                    height: 40px;
                    background: #F15412;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-right: 1rem;
                    color: white;
                    flex-shrink: 0;
                }

                .info-content h5 {
                    margin: 0 0 0.25rem;
                    font-size: 1rem;
                    font-weight: 600;
                    color: #333;
                }

                .info-content p {
                    margin: 0;
                    color: #666;
                    line-height: 1.5;
                }

                .social-links {
                    display: flex;
                    gap: 0.75rem;
                }

                .social-link {
                    width: 36px;
                    height: 36px;
                    border-radius: 50%;
                    background: #f5f5f5;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #555;
                    transition: all 0.3s;
                }

                .social-link:hover {
                    background: #F15412;
                    color: white;
                    transform: translateY(-3px);
                }

                /* Form liên hệ */
                .contact-box {
                    background: white;
                    padding: 2rem;
                    border-radius: 12px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
                    position: relative;
                    height: 100%;
                }

                .contact-header {
                    text-align: center;
                    margin-bottom: 2rem;
                    border-bottom: 1px solid #eee;
                    padding-bottom: 1rem;
                }

                .contact-header h2 {
                    margin: 0 0 0.5rem;
                    font-size: 1.8rem;
                    color: #333;
                    font-weight: 700;
                }

                .contact-header p {
                    color: #666;
                    margin-bottom: 0;
                }

                .form-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 1.25rem;
                }

                .form-group {
                    display: flex;
                    flex-direction: column;
                    margin-bottom: 1.25rem;
                }

                .form-group.full {
                    grid-column: span 2;
                }

                label {
                    margin-bottom: 0.5rem;
                    font-weight: 600;
                    color: #222;
                    display: flex;
                    align-items: center;
                }

                label i {
                    color: #F15412;
                }

                input,
                textarea {
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    padding: 0.75rem 1rem;
                    font-size: 1rem;
                    transition: all 0.3s ease;
                    background-color: #f9f9f9;
                }

                input:focus,
                textarea:focus {
                    outline: none;
                    border-color: #F15412;
                    box-shadow: 0 0 0 3px rgba(241, 84, 18, 0.1);
                    background-color: #fff;
                }

                .is-invalid {
                    border-color: #dc3545;
                }

                .error-text {
                    color: #dc3545;
                    font-size: 0.85rem;
                    margin-top: 0.25rem;
                    display: flex;
                    align-items: center;
                }

                .btn-submit {
                    margin-top: 1rem;
                    width: 100%;
                    background: #F15412;
                    color: white;
                    padding: 0.85rem;
                    border: none;
                    font-size: 1.1rem;
                    border-radius: 8px;
                    cursor: pointer;
                    transition: all 0.3s;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: 600;
                }

                .btn-submit:hover {
                    background: #d13700;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(241, 84, 18, 0.2);
                }

                .btn-submit:active {
                    transform: translateY(0);
                }

                .alert {
                    margin-bottom: 1.5rem;
                    padding: 1rem;
                    border-radius: 8px;
                    display: flex;
                    align-items: center;
                }

                .alert-success {
                    background: #e6ffed;
                    color: #155724;
                    border: 1px solid #c3e6cb;
                }

                .alert-danger {
                    background: #fff0f0;
                    color: #721c24;
                    border: 1px solid #f5c6cb;
                }

                .loading-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(255, 255, 255, 0.8);
                    z-index: 1000;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 12px;
                }

                .spinner {
                    width: 40px;
                    height: 40px;
                    border: 4px solid rgba(241, 84, 18, 0.2);
                    border-top: 4px solid #F15412;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                }

                /* Bản đồ */
                .map-container {
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
                }

                @keyframes spin {
                    to {
                        transform: rotate(360deg);
                    }
                }

                /* Responsive */
                @media (max-width: 992px) {
                    .contact-info-card {
                        margin-bottom: 2rem;
                    }
                }

                @media (max-width: 768px) {
                    .form-grid {
                        grid-template-columns: 1fr;
                    }

                    .form-group.full {
                        grid-column: span 1;
                    }

                    .contact-box,
                    .contact-info-card {
                        padding: 1.5rem;
                    }
                }
            </style>

            <script>
                document.addEventListener('livewire:initialized', () => {
                    Livewire.on('contactSubmitted', () => {
                        setTimeout(() => {
                            window.location.href = '{{ route("home") }}';
                        }, 2000);
                    });
                });
            </script>
    </main>
</div>