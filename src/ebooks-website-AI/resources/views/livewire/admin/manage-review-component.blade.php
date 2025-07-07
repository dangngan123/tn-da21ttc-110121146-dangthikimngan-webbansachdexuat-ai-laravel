<div>
    <style>
        .sort-by-product-wrap {
            width: 200px;
            display: flex;
            align-items: center;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .sort-by-dropdown-wrap {
            flex: 1;
            overflow: hidden;
        }

        .sort-by-dropdown-wrap span {
            display: inline-block;
            white-space: nowrap;
            text-overflow: ellipsis;
            width: 100%;
            padding-right: 20px;
        }

        .sort-by {
            position: relative;
            padding-left: 30px;
        }

        .fa-filter {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #007bff;
        }

        .sort-by-dropdown {
            width: 200px;
        }
    </style>
    <div class="card">
        <div class="card-header">
            <div class="shop-product-fillter mb-0">
                <div class="sidebar-widget widget_search bg-1">
                    <div class="search-form">
                        <form action="#">
                            <input type="text" placeholder="Tìm kiếm…" wire:model.live="search" style="width: 250px;">
                        </form>
                    </div>
                </div>
                <div class="sort-by-product-area mr-10">
                    <div class="sort-by-cover">
                        <div class="sort-by-product-wrap bg-3">
                            <div class="sort-by">
                                <i class="fa-solid fa-filter" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 18px; color: #007bff;"></i>
                            </div>
                            <div class="sort-by-dropdown-wrap">
                                <span>
                                    @if(!$statusFilter)
                                    Tất cả trạng thái
                                    @elseif($statusFilter == 'pending')
                                    Đang xử lý
                                    @elseif($statusFilter == 'approved')
                                    Đã xác nhận
                                    @elseif($statusFilter == 'rejected')
                                    Đã bị hủy
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="sort-by-dropdown">
                            <ul>
                                <li><a href="#" wire:click.prevent="$set('statusFilter', '')" class="{{ !$statusFilter ? 'active' : '' }}">Tất cả trạng thái</a></li>
                                <li><a href="#" wire:click.prevent="$set('statusFilter', 'pending')" class="{{ $statusFilter == 'pending' ? 'active' : '' }}">Đang xử lý</a></li>
                                <li><a href="#" wire:click.prevent="$set('statusFilter', 'approved')" class="{{ $statusFilter == 'approved' ? 'active' : '' }}">Đã xác nhận</a></li>
                                <li><a href="#" wire:click.prevent="$set('statusFilter', 'rejected')" class="{{ $statusFilter == 'rejected' ? 'active' : '' }}">Đã bị hủy</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="sort-by-product-area">
                    <div class="sort-by-cover mr-10">
                        <div class="sort-by-product-wrap bg-3">
                            <div class="sort-by">
                                <span><i class="fi-rs-apps"></i>Đã chọn:</span>
                            </div>
                            <div class="sort-by-dropdown-wrap">
                                <span> {{count($selectedItems)}} <i class="fi-rs-angle-small-down"></i></span>
                            </div>
                        </div>
                        <div class="sort-by-dropdown">
                            <ul class="menu">
                                <li><a href="#" wire:click.prevent="selecteDelete"><i class="fi-rs-trash"></i> Xóa</a></li>
                                <li><a href="#" wire:click.prevent="selecteActive"><i class="fi-rs-thumbs-up mr-5"></i>Bật</a></li>
                                <li><a href="#" wire:click.prevent="selecteInactive"><i class="fi-rs-thumbs-down mr-5"></i>Tắt</a></li>
                                <li><a href="#" wire:click.prevent="export"><i class="fi-rs-download mr-5"></i>Export</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="sort-by-cover">
                        <div class="sort-by-product-wrap bg-3">
                            <div class="sort-by">
                                <span><i class="fi-rs-apps-sort"></i>Hiển thị:</span>
                            </div>
                            <div class="sort-by-dropdown-wrap">
                                <span> {{$pagesize}} <i class="fi-rs-angle-small-down"></i></span>
                            </div>
                        </div>
                        <div class="sort-by-dropdown">
                            <ul>
                                <li><a class="{{ $pagesize == 12 ? 'active' : '' }}" href="#" wire:click.prevent="changepageSize(12)">12</a></li>
                                <li><a class="{{ $pagesize == 24 ? 'active' : '' }}" href="#" wire:click.prevent="changepageSize(24)">24</a></li>
                                <li><a class="{{ $pagesize == 36 ? 'active' : '' }}" href="#" wire:click.prevent="changepageSize(36)">36</a></li>
                                <li><a class="{{ $pagesize == 48 ? 'active' : '' }}" href="#" wire:click.prevent="changepageSize(48)">48</a></li>
                                <li><a class="{{ $pagesize == 50 ? 'active' : '' }}" href="#" wire:click.prevent="changepageSize(50)">50</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr class="text-center">
                            <th><input type="checkbox" wire:model.live="selectAll" class="small-checkbox"></th>
                            <th>STT</th>
                            <th>Mã khách hàng</th>
                            <th>Tên sản phẩm</th>
                            <th>Đánh giá</th>
                            <th>Bình luận</th>
                            <th>Ngày đánh giá</th>
                            <th>Trạng thái</th>
                            <th>Phản hồi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $index => $review)
                        <tr class="{{$this->isColor($review->id)}}">
                            <td class="small-checkbox">
                                <input type="checkbox" wire:model.live="selectedItems" value="{{ $review->id }}">
                            </td>
                            <td class="text-center">{{$index + $reviews->firstItem()}}</td>
                            <td class="text-center">#{{$review->user->id}}</td>
                            <td class="text-center">{{ $review->product->name ?? 'Không tìm thấy sản phẩm' }}</td>
                            <td class="text-center">{{$review->rating}}</td>
                            <td class="text-center">{{$review->comment}}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button
                                        class="btn btn-secondary dropdown-toggle"
                                        type="button"
                                        id="dropdownMenuButton-{{ $review->id }}"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                        {{ in_array($review->status, ['rejected', 'approved']) ? 'disabled' : '' }}>
                                        @switch($review->status)
                                        @case('pending')
                                        Đang xử lý
                                        @break
                                        @case('approved')
                                        Đã xác nhận
                                        @break
                                        @case('rejected')
                                        Đã bị hủy
                                        @break
                                        @endswitch
                                    </button>
                                    @if (!in_array($review->status, ['rejected', 'approved']))
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-{{ $review->id }}">
                                        <li>
                                            <a
                                                class="dropdown-item"
                                                href="#"
                                                wire:click.prevent="updateOrderStatus({{ $review->id }}, 'approved')">
                                                Đã xác nhận
                                            </a>
                                        </li>
                                        <li>
                                            <a
                                                class="dropdown-item"
                                                href="#"
                                                wire:click.prevent="updateOrderStatus({{ $review->id }}, 'rejected')">
                                                Đã bị hủy
                                            </a>
                                        </li>
                                    </ul>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <button
                                    class="btn-small btn-sm btn-danger"
                                    wire:click.prevent="openReplyModal({{ $review->id }})">
                                    {{ $review->admin_reply ? 'Chỉnh sửa' : 'Phản hồi' }}
                                </button>
                                @if($review->admin_reply)
                                <div class="admin-reply mt-2 text-muted">
                                    <strong>Phản hồi:</strong> {{ $review->admin_reply }}<br>
                                    <small>{{ \Carbon\Carbon::parse($review->admin_reply_at)->format('d/m/Y H:i') }}</small>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-danger">Không có đánh giá nào</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $reviews->links() }}
            </div>
        </div>
    </div>

    <!-- Modal trả lời -->
    <div wire:ignore class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="replyModalLabel">
                        {{ $this->currentReviewId && ($review = \App\Models\Review::find($this->currentReviewId)) ? ($review->admin_reply ? 'Chỉnh sửa phản hồi' : 'Thêm phản hồi') : 'Phản hồi đánh giá' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="replyReview">
                    <div class="modal-body">
                        @if($this->currentReviewId && ($review = \App\Models\Review::find($this->currentReviewId)))
                        <div class="review-info mb-3">
                            <p><strong>Sản phẩm:</strong> {{ $review->product->name ?? 'Không tìm thấy sản phẩm' }}</p>
                            <p><strong>Đánh giá:</strong> {{ $review->rating }} sao</p>
                            <p><strong>Bình luận:</strong> {{ $review->comment }}</p>
                            <p><strong>Ngày đánh giá:</strong> {{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                        <div class="form-group">
                            <label for="replyTextArea">Phản hồi của bạn</label>
                            <textarea
                                class="form-control"
                                wire:model="currentReplyText"
                                rows="5"
                                placeholder="Nhập phản hồi của bạn..."
                                id="replyTextArea"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        @if($this->currentReviewId && ($review = \App\Models\Review::find($this->currentReviewId)) && $review->admin_reply)
                        <button type="button" class="btn btn-danger" wire:click.prevent="deleteReply" data-bs-dismiss="modal">Xóa phản hồi</button>
                        @endif
                        <button type="submit" class="btn btn-primary">Gửi phản hồi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('openReplyModal', (reviewId, replyText) => {
            document.getElementById('replyTextArea').value = replyText || '';
            const modal = new bootstrap.Modal(document.getElementById('replyModal'));
            modal.show();
        });

        document.getElementById('replyModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('replyTextArea').value = '';
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style = '';
        });
    });
</script>