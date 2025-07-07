<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="jumbotron text-center shadow-lg p-5 bg-light rounded">
                <h2 class="mb-4 text-success">ĐƠN HÀNG CỦA BẠN ĐÃ ĐƯỢC TIẾP NHẬN</h2>
                <h3 class="mb-3 text-secondary">Cảm ơn bạn đã đặt hàng, chúng tôi đang xử lý đơn hàng</h3>
                <p class="mb-2"><strong>Mã đơn hàng của bạn là:</strong> <span class="text-primary">{{$order->order_code}}</span></p>
                <p class="mb-4">Bạn sẽ nhận được email xác nhận đơn hàng kèm theo chi tiết đơn hàng và đường dẫn để theo dõi quá trình xử lý.</p>
                <a href="/" class="btn btn-warning btn-lg">TIẾP TỤC MUA SẮM</a>
                <a href="{{route('customer.orders',$order->_id)}}" class="btn btn-warning btn-lg">ĐƠN HÀNG CỦA BẠN</a>
            </div>
        </div>
    </div>
</div>