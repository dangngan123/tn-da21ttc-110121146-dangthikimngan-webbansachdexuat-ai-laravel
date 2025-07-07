<div>
    <div class="card">
        <div class="card-header">
            <div class="totall-product" style="display: flex; gap: 20px;">
                <div style="width: 600px; background-color: #fff;">
                    <input
                        type="text"
                        class="form-control"
                        placeholder="Tìm kiếm theo tên hoặc email..."
                        wire:model.live.debounce.500ms="search">
                </div>
                <div style="width: 250px; background-color: #fff;">
                    <select class="form-control" wire:model.live="filter_status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="0">Chưa xử lý</option>
                        <option value="1">Đã xử lý</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 5%;">STT</th>
                            <th style="width: 15%;">Tên khách hàng</th>
                            <th style="width: 20%;">Email</th>
                            <!-- <th style="width: 10%;">Phone</th> -->
                            <th style="width: 15%;">Chủ đề</th>
                            <th style="width: 20%;">Tin nhắn</th>
                            <th style="width: 10%;">Ngày tạo</th>
                            <th style="width: 5%;">Trạng Thái</th>
                            <th style="width: 10%;">Thao tác</th> <!-- Tăng độ rộng cột Thao tác -->
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contacts as $index => $contact)
                        <tr wire:key="contact-{{ $contact->id }}" class="{{ $contact->status == 1 ? 'bg-light' : '' }}">
                            <td>{{ $index + $contacts->firstItem() }}</td>
                            <td>{{ $contact->name }}</td>
                            <td>{{ $contact->email }}</td>
                            <!-- <td>{{ $contact->telephone }}</td> -->
                            <td>{{ $contact->subject }}</td>
                            <td>
                                {{ Str::limit($contact->message, 50, '...') }}
                                <a href="#" wire:click="openDetailModal({{ $contact->id }})" class="text-primary">Xem thêm</a>
                            </td>
                            <td>{{ $contact->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge bg-{{ $contact->status == 0 ? 'warning' : 'success' }}">
                                    {{ $contact->status == 0 ? 'Chưa xử lý' : 'Đã xử lý' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($contact->status == 0)
                                <button
                                    wire:click="openReplyModal({{ $contact->id }})"
                                    class="btn btn-primary btn-sm d-inline-flex align-items-center"
                                    style="padding: 2px 8px; font-size: 12px;">
                                    <i class="fas fa-reply me-1"></i>
                                </button>
                                @else
                                <button class="btn btn-secondary btn-sm d-inline-flex align-items-center"
                                        style="padding: 2px 8px; font-size: 12px;"
                                        disabled>
                                    <i class="fas fa-check me-1"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-danger">Không có liên hệ nào</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $contacts->links() }}
            </div>
        </div>
    </div>

    <!-- Reply Modal -->
    @if($showReplyModal && $contacts->find($replyContactId))
    <div class="modal show" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Trả lời liên hệ</h5>
                    <button type="button" class="btn-close" wire:click="closeReplyModal"></button>
                </div>
                <form wire:submit.prevent="sendEmail({{ $replyContactId }})">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nội dung trả lời:</label>
                            <textarea
                                wire:model.live="replyMessage"
                                class="form-control"
                                rows="5"
                                placeholder="Nhập nội dung trả lời..."
                                required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeReplyModal">Đóng</button>
                        <button
                            type="submit"
                            class="btn btn-primary"
                            wire:loading.attr="disabled"
                            @if(!$replyMessage) disabled @endif>
                            <span wire:loading.remove wire:target="sendEmail">
                                Gửi
                            </span>
                            <span wire:loading wire:target="sendEmail">
                                <i class="spinner-border spinner-border-sm"></i> Đang gửi...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Detail Modal -->
    @if($contacts->find($selectedContactId))
    <div class="modal show" style="display: block; background: rgba(0,0,0,0.5);" wire:keydown.escape="closeDetailModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết liên hệ</h5>
                    <button type="button" class="btn-close" wire:click="closeDetailModal"></button>
                </div>
                <div class="modal-body">
                    @if($contact = $contacts->find($selectedContactId))
                    <p><strong>Tên khách hàng:</strong> {{ $contact->name }}</p>
                    <p><strong>Email:</strong> {{ $contact->email }}</p>
                    <p><strong>Số điện thoại:</strong> {{ $contact->telephone }}</p>
                    <p><strong>Chủ đề:</strong> {{ $contact->subject }}</p>
                    <p><strong>Tin nhắn:</strong> {{ $contact->message }}</p>
                    <p><strong>Ngày tạo:</strong> {{ $contact->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Trạng thái:</strong> {{ $contact->status == 0 ? 'Chưa xử lý' : 'Đã xử lý' }}</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeDetailModal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('openDetailModal', (contactId) => {
                Livewire.emit('setSelectedContactId', contactId);
            });

            Livewire.on('closeDetailModal', () => {
                Livewire.emit('setSelectedContactId', null);
            });
        });
    </script>

    <style>
        .table th,
        .table td {
            vertical-align: middle;
            word-wrap: break-word;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        /* Custom style for buttons */
        

        .btn-primary.btn-sm i {
            margin-right: 4px;
        }

        .btn-secondary.btn-sm i {
            margin-right: 4px;
        }
    </style>
</div>