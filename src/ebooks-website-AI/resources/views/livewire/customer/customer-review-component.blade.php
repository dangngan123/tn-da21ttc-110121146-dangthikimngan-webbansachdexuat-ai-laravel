<div>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            cursor: pointer;
            font-size: 1.5rem;
            color: #ddd;
            padding: 0 2px;
        }

        .star-rating label:hover,
        .star-rating label:hover~label,
        .star-rating input:checked~label {
            color: #ffc107;
        }

        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .image-preview img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }

        .image-preview .remove {
            cursor: pointer;
            color: red;
            font-size: 20px;
            margin-left: -10px;
        }

        .image-counter {
            margin-top: 10px;
            color: #666;
        }

        .required-indicator {
            color: red;
            margin-left: 4px;
        }

        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .form-group {
            margin-bottom: 20px;
        }
    </style>

    <div class="container" style="padding: 30px 0;">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Đánh giá sản phẩm</h4>
                    </div>
                    <div class="card-body">
                        <div class="product">
                            <img src="{{ asset('admin/product/' . $orderItem->product->image) }}" alt="{{ $orderItem->product->name }}" width="100">
                            <p><strong>{{ $orderItem->product->name }}</strong></p>
                        </div>
                        <form wire:submit.prevent="addReview">
                            <div class="form-group">
                                <label for="rating">Đánh giá:<span class="required-indicator">*</span></label>
                                <div class="star-rating">
                                    <input type="radio" id="star-5" name="rating" value="5" wire:model.live="rating" />
                                    <label for="star-5" title="5 sao">★</label>
                                    <input type="radio" id="star-4" name="rating" value="4" wire:model.live="rating" />
                                    <label for="star-4" title="4 sao">★</label>
                                    <input type="radio" id="star-3" name="rating" value="3" wire:model.live="rating" />
                                    <label for="star-3" title="3 sao">★</label>
                                    <input type="radio" id="star-2" name="rating" value="2" wire:model.live="rating" />
                                    <label for="star-2" title="2 sao">★</label>
                                    <input type="radio" id="star-1" name="rating" value="1" wire:model.live="rating" />
                                    <label for="star-1" title="1 sao">★</label>
                                </div>
                                @error('rating') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="comment">Nhận xét:<span class="required-indicator">*</span></label>
                                <textarea wire:model.live="comment" id="comment" rows="5" class="form-control"></textarea>
                                @error('comment') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="image">Ảnh (tối đa 5 ảnh):</label>
                                <input type="file" wire:model="new_image" id="image" accept="image/*" class="form-control">
                                <div class="image-counter">
                                    Đã chọn: {{ count($new_images) }}/5 ảnh
                                </div>
                                @error('new_image') <span class="text-danger">{{ $message }}</span> @enderror

                                <div class="image-preview">
                                    @if($new_images)
                                        @foreach($new_images as $key => $image)
                                            @if(method_exists($image, 'temporaryUrl'))
                                            <div class="image-item">
                                                <img src="{{ $image->temporaryUrl() }}" class="img-thumbnail">
                                                <span class="remove" wire:click="removeImage({{ $key }})">×</span>
                                            </div>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>

                                @if($review && $review->images)
                                <div class="image-preview mt-3">
                                    <p>Ảnh đã tải lên trước đó:</p>
                                    @foreach(explode(',', $review->images) as $image)
                                    <img src="{{ asset('admin/review/' . $image) }}" class="img-thumbnail" style="width: 100px;">
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            <button type="submit"
                                    class="btn btn-primary"
                                    wire:loading.attr="disabled"
                                    @if(count($new_images) >= 5) disabled @endif>
                                <span wire:loading.remove>Gửi đánh giá</span>
                                <span wire:loading>Đang xử lý...</span>
                            </button>
                        </form>

                        @if(session()->has('message'))
                        <div class="alert alert-success mt-3">
                            {{ session('message') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>