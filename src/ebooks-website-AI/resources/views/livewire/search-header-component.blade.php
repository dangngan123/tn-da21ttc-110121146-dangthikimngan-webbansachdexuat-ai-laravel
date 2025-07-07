<style>
    input::placeholder {
        font-style: italic;
        color: #888;
    }

    .search-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #ccc;
        border-radius: 12px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        max-height: 300px;
        overflow-y: auto;
        width: 400px;
    }

    .search-suggestions li {
        padding: 10px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
        border-radius: 8px;
        margin: 2px 5px;
    }

    .search-suggestions li:hover {
        background: #f0f0f0;
    }

    .search-suggestions li:last-child {
        border-bottom: none;
    }

    .search-suggestions .history-header {
        font-weight: bold;
        padding: 10px;
        background: rgb(255, 255, 255);
        border-radius: 8px;
        margin: 2px 5px;
    }

    .search-suggestions .suggestion-item {
        font-size: 14px;
        display: flex;
        align-items: center;
    }

    .search-suggestions .suggestion-item img {
        width: 40px;
        height: 40px;
        margin-right: 10px;
        border-radius: 8px;
    }

    .search-suggestions .suggestion-item span {
        display: block;
        font-size: 12px;
        color: #666;
    }

    .suggestion-item a {
        text-decoration: none;
        color: inherit;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .suggestion-item a:hover {
        color: #007bff;
    }

    .suggestion-item.no-link {
        cursor: default;
    }
</style>

<div>
    <div class="totall-product">
        <form wire:submit.prevent action="{{ route('search') }}" class="d-flex align-items-center position-relative" style="width: 400px;">
            <input
                id="searchInput"
                type="text"
                name="search"
                placeholder="Sách giá rẻ tìm kiếm nhanh..."
                value="{{ $search ?? '' }}"
                wire:model.debounce.300ms="search"
                style="border-radius: 100px; width: 100%; border: 1px solid #C12530; padding-right: 40px;"
                autocomplete="off">

            <!-- Icon tìm kiếm -->
            <button type="submit" class="position-absolute end-0 me-3 bg-transparent border-0 p-0"
                style="background-color: transparent !important; box-shadow: none !important;">
                <i class="fa fa-search text-danger" style="pointer-events: none;"></i>
            </button>

            <!-- Gợi ý tìm kiếm -->
            <ul class="search-suggestions" id="searchSuggestions" style="display: none;">
                <!-- Gợi ý sách dựa trên từ khóa -->
                @if (!empty($searchSuggestions))
                    <li class="history-header">Kết quả gợi ý</li>
                    @foreach ($searchSuggestions as $suggestion)
                    <li class="suggestion-item {{ empty($suggestion['slug']) ? 'no-link' : '' }}">
                        @if (!empty($suggestion['slug']))
                        <a href="{{ route('details', ['slug' => $suggestion['slug']]) }}">
                            <img src="{{ asset('admin/product/' . ($suggestion['image'] ?? 'default-image.jpg')) }}" alt="{{ $suggestion['name'] }}">
                            <div>
                                {{ $suggestion['name'] }}
                                <span>Tác giả: {{ $suggestion['author'] }} | Danh mục: {{ $suggestion['category'] }} | Năm: {{ $suggestion['year'] }}</span>
                            </div>
                        </a>
                        @else
                        <div>
                            <img src="{{ asset('admin/product/' . ($suggestion['image'] ?? 'default-image.jpg')) }}" alt="{{ $suggestion['name'] }}">
                            <div>
                                {{ $suggestion['name'] }}
                                <span>Tác giả: {{ $suggestion['author'] }} | Danh mục: {{ $suggestion['category'] }} </span>
                            </div>
                        </div>
                        @endif
                    </li>
                    @endforeach
                @endif

                <!-- Sản phẩm phổ biến (khi ô tìm kiếm trống) -->
                @if (empty($search) && !empty($popularProducts))
                    <li class="history-header">Sản phẩm phổ biến</li>
                    @foreach ($popularProducts as $product)
                    <li class="suggestion-item {{ empty($product['slug']) ? 'no-link' : '' }}">
                        @if (!empty($product['slug']))
                        <a href="{{ route('details', ['slug' => $product['slug']]) }}">
                            <img src="{{ asset('admin/product/' . ($product['image'] ?? 'default-image.jpg')) }}" alt="{{ $product['name'] }}">
                            <div>
                                {{ $product['name'] }}
                                <span>Tác giả: {{ $product['author'] }} | Danh mục: {{ $product['category'] }} </span>
                            </div>
                        </a>
                        @else
                        <div>
                            <img src="{{ asset('admin/product/' . ($product['image'] ?? 'default-image.jpg')) }}" alt="{{ $product['name'] }}">
                            <div>
                                {{ $product['name'] }}
                                <span>Tác giả: {{ $product['author'] }} | Danh mục: {{ $product['category'] }} </span>
                            </div>
                        </div>
                        @endif
                    </li>
                    @endforeach
                @endif

                <!-- Thông báo không tìm thấy -->
                @if (!empty($noResultsMessage))
                    <li class="history-header">{{ $noResultsMessage }}</li>
                @endif
            </ul>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('searchInput');
    const suggestions = document.getElementById('searchSuggestions');
    const chatbotButton = document.querySelector('[x-on\\:click*="open = true"]');
    const chatbotContainer = document.querySelector('[x-show="open"]');

    if (!input || !suggestions) return;

    const debounce = (func, wait) => {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    };

    // Hiển thị gợi ý khi focus
    input.addEventListener('focus', () => {
        if (input.value.trim().length >= 0 || suggestions.children.length > 0) {
            suggestions.style.display = 'block';
        }
    });

    // Hiển thị gợi ý khi nhập
    input.addEventListener('input', debounce(() => {
        if (input.value.trim().length >= 0 || suggestions.children.length > 0) {
            suggestions.style.display = 'block';
        } else {
            suggestions.style.display = 'none';
        }
    }, 300));

    // Ẩn gợi ý khi blur
    input.addEventListener('blur', (e) => {
        if (!suggestions.contains(e.relatedTarget)) {
            setTimeout(() => {
                suggestions.style.display = 'none';
            }, 200);
        }
    });

    // Ngăn blur khi click vào gợi ý
    suggestions.addEventListener('mousedown', (e) => {
        e.preventDefault();
    });

    // Ẩn gợi ý khi click ngoài
    document.addEventListener('click', (e) => {
        if (!input.contains(e.target) && !suggestions.contains(e.target) && !chatbotButton.contains(e.target) && (!chatbotContainer || !chatbotContainer.contains(e.target))) {
            suggestions.style.display = 'none';
            input.blur();
        }
    });

    // Xử lý sự kiện từ Livewire
    Livewire.on('search.updateSuggestions', () => {
        if (document.activeElement === input && (input.value.trim().length >= 0 || suggestions.children.length > 0)) {
            suggestions.style.display = 'block';
        }
    });

    // Ngăn hiển thị gợi ý khi click vào chatbot
    if (chatbotButton) {
        chatbotButton.addEventListener('click', (e) => {
            e.stopPropagation();
            suggestions.style.display = 'none';
        });
    }
});
</script>
