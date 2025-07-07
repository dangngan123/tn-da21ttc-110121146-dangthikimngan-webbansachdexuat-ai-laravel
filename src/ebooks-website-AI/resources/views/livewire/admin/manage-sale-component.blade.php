<div>
    <style>
        /* CSS tối thiểu cho shop-product-fillter và liên quan */
        .shop-product-fillter {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 0;
        }

        .sort-by-product-area {
            display: flex;
            gap: 10px;
        }

        .sort-by-product-wrap {
            background: #fff;
            border-radius: 20px;
            padding: 8px 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #333;
            cursor: pointer;
        }

        .form-control:focus {
            border-color: #ff4d4d;
            box-shadow: none;
        }

        .sort-by-product-wrap i {
            color: #ff4d4d;
        }

        .sort-by-dropdown {
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 5px;
        }

        .sort-by-dropdown ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sort-by-dropdown ul li a {
            display: block;
            padding: 8px 15px;
            color: #333;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .sort-by-dropdown ul li a:hover,
        .sort-by-dropdown ul li a.active {
            background: #ff4d4d;
            color: #fff;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #ff4d4d 0%, #ff6b6b 100%);
            color: #fff;
            padding: 15px 20px;
            border-bottom: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-body {
            padding: 20px;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .table thead th {
            background: #f5f7fa;
            color: #333;
            font-weight: 600;
            padding: 15px;
            border: none;
            font-size: 14px;
        }

        .table tbody tr {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .table tbody td {
            padding: 15px;
            border: none;
            vertical-align: middle;
            font-size: 14px;
            color: #555;
        }

        .badge.bg-success {
            background: #4b8f63 !important;
        }

        .badge.bg-danger {
            background: #ff4d4d !important;
        }

        .dropdown .btn {
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 5px;
            background: #f5f7fa;
            color: #333;
            border: none;
        }

        .dropdown .btn:hover {
            background: #ff4d4d;
            color: #fff;
        }

        .edit-btn {
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 5px;
            background: #007bff;
            color: #fff;
            border: none;
        }

        .edit-btn:hover {
            background: #0056b3;
        }

        .modal-content {
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background: #ff4d4d;
            color: #fff;
            border-bottom: none;
            border-radius: 10px 10px 0 0;
            padding: 15px 20px;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer .btn-primary {
            background: #ff4d4d;
            border: none;
        }

        .form-control {
            border-radius: 5px;
            font-size: 14px;
            padding: 10px;
        }

        @media screen and (max-width: 768px) {
            .shop-product-fillter {
                flex-direction: column;
                gap: 10px;
            }

            .sort-by-product-area {
                flex-direction: column;
                width: 100%;
            }

            .sort-by-product-wrap {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>

    <div class="card">
        <div class="card-header">
            Quản lý Flash Sale
            <div class="shop-product-fillter mb-0">
                <!-- Có thể thêm bộ lọc hoặc sắp xếp nếu cần -->
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr class="text-center">
                            <th>STT</th>
                            <th>ID Flash Sale</th>
                            <th>Thời gian bắt đầu</th>
                            <th>Thời gian kết thúc</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($saletimers as $index => $saletimer)
                        <tr wire:key="sale-{{ $saletimer->id }}" class="{{ $highlightedId == $saletimer->id ? 'table-success' : '' }}">
                            <td class="text-center">{{ $index + $saletimers->firstItem() }}</td>
                            <td class="text-center">#{{ $saletimer->id }}</td>
                            <td class="text-center">{{ $saletimer->start_date ? $saletimer->start_date->format('d/m/Y H:i') : 'Chưa đặt' }}</td>
                            <td class="text-center">{{ $saletimer->sale_timer ? $saletimer->sale_timer->format('d/m/Y H:i') : 'Chưa đặt' }}</td>
                            <td class="text-center">
                                <span class="badge {{ $saletimer->status ? 'bg-success' : 'bg-danger' }}">
                                    {{ $saletimer->status ? 'Bật' : 'Tắt' }}
                                </span>
                            </td>
                            <td class="text-center">{{ $saletimer->created_at ? $saletimer->created_at->format('d/m/Y H:i') : 'Chưa đặt' }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <div class="dropdown">
                                        <button
                                            class="btn btn-secondary dropdown-toggle"
                                            type="button"
                                            id="dropdownMenuButton_{{ $saletimer->id }}"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            {{ $saletimer->status ? 'Bật' : 'Tắt' }}
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton_{{ $saletimer->id }}">
                                            <li>
                                                <a
                                                    class="dropdown-item"
                                                    href="#"
                                                    wire:click.prevent="updateSaleStatus({{ $saletimer->id }}, 1)">
                                                    Bật
                                                </a>
                                            </li>
                                            <li>
                                                <a
                                                    class="dropdown-item"
                                                    href="#"
                                                    wire:click.prevent="updateSaleStatus({{ $saletimer->id }}, 0)">
                                                    Tắt
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <button
                                        class="edit-btn"
                                        wire:click.prevent="startEditing({{ $saletimer->id }})">
                                        Chỉnh sửa
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-danger">Không có Flash Sale nào</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $saletimers->links() }}
            </div>
        </div>
    </div>

    <!-- Modal chỉnh sửa -->
    <div wire:ignore.self class="modal fade" id="editSaleModal" tabindex="-1" aria-labelledby="editSaleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSaleModalLabel">Chỉnh sửa Flash Sale #{{ $editingId ?? '' }}</h5>
                    <button type="button" class="btn-close" wire:click="cancelEditing" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Thời gian bắt đầu</label>
                        <input type="datetime-local" class="form-control" id="start_date" wire:model="start_date">
                        @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="sale_timer" class="form-label">Thời gian kết thúc</label>
                        <input type="datetime-local" class="form-control" id="sale_timer" wire:model="sale_timer">
                        @error('sale_timer') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-control" id="status" wire:model="status">
                            <option value="1">Bật</option>
                            <option value="0">Tắt</option>
                        </select>
                        @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="cancelEditing">Hủy</button>
                    <button type="button" class="btn btn-primary" wire:click="updateSale">Lưu</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('openEditModal', () => {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = new bootstrap.Modal(document.getElementById('editSaleModal'));
                    modal.show();
                } else {
                    console.error('Bootstrap Modal không được tải. Vui lòng kiểm tra việc bao gồm Bootstrap JS.');
                }
            });

            // Đóng modal khi hủy hoặc lưu
            Livewire.on('closeEditModal', () => {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modalElement = document.getElementById('editSaleModal');
                    const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                    modal.hide();
                    // Xóa backdrop thủ công nếu cần
                    document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = ''; // Khôi phục cuộn trang
                }
            });
        });
    </script>
</div>