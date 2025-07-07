<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Livewire\Customer\CustomerDashboardComponent;
use App\Livewire\HomeComponent;
use App\Livewire\ShopComponent;
use App\Livewire\CartComponent;
use App\Livewire\CheckoutComponent;
use App\Livewire\DetailsComponent;
use App\Livewire\CategoryComponent;
use App\Livewire\SearchComponent;
use App\Livewire\WishlistComponent;
use App\Livewire\ThankyouComponent;
use App\Livewire\Admin\AdminDashboardComponent;
use App\Livewire\Admin\ManageOrderDetailsComponent;
use App\Livewire\Admin\ManageOrderComponent;
use App\Livewire\Customer\CustomerOrderComponent;
use App\Livewire\Customer\CustomerReviewComponent;
use App\Livewire\ContactComponent;
use App\Livewire\AboutComponent;
use App\Livewire\Customer\CustomerOrderDetailsComponent;
use App\Http\Controllers\Auth\GoogleController;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Review;
// Ensure this line is present and the class exists
use App\Http\Controllers\LocaleController;
use App\Livewire\Admin\ManageChatbotComponent;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Livewire\VoucherIconComponent;
use App\Livewire\PoliciesComponent;







//Admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', AdminDashboardComponent::class)->name('admin.dashboard');
    Route::get('/orders', ManageOrderComponent::class)->name('admin.orders');
    Route::get('/orderdetails/{order_id}', ManageOrderDetailsComponent::class)->name('admin.orderdetails');
    
});





//Customer 
Route::middleware(['auth'])->group(function () {
    Route::get('/customer/dashboard', CustomerDashboardComponent::class)->name('customer.dashboard');
    Route::get('/order', CustomerOrderComponent::class)->name('customer.orders');
    Route::get('/review/{order_item_id}', CustomerReviewComponent::class)->name('customer.review');
    Route::get('/ordersdetails/{order_id}', CustomerOrderDetailsComponent::class)->name('customer.orderdetails');
});

Route::get('/policy', PoliciesComponent::class)->name('policy');

Route::get('/', HomeComponent::class)->name('home');

Route::get('/shop', ShopComponent::class)->name('shop');

Route::get('/cart', CartComponent::class)->name('cart');


Route::get('/checkout', CheckoutComponent::class)->name('checkout');

Route::get('/details/{slug}', DetailsComponent::class)->name('details'); 



Route::get('product-category/{slug}', CategoryComponent::class)->name('product.category');

Route::get('/search-product', SearchComponent::class)->name('search');

Route::get('/wishlist', WishlistComponent::class)->name('wishlist');

Route::get('/voucher', VoucherIconComponent::class)->name('voucher');

Route::get('/thankyou', ThankyouComponent::class)->name('thankyou');

Route::get('/contact', ContactComponent::class)->name('contact');

Route::get('/about', AboutComponent::class)->name('about');




Route::post('review/{review}/reply', [DetailsComponent::class, 'reply'])->name('review.reply');


Route::get('/notifications', [AdminDashboardComponent::class, 'index'])->name('notifications.index');
Route::post('/notifications/{id}/read', [AdminDashboardComponent::class, 'markAsRead'])->name('notifications.read');
Route::post('/notifications/mark-all-read', [AdminDashboardComponent::class, 'markAllAsRead'])->name('notifications.markAllRead');







// Route::get('auth/google', function () {
//     return Socialite::driver('google')->redirect();
// })->name('google.login');
// Route::get('google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Route::get('redirect/google', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
// Route::get('callback/google', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

Route::get('redirect/google', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

Route::get('/verification/otp', [RegisteredUserController::class, 'showOtpForm'])->name('verification.otp');
Route::post('/verification/otp', [RegisteredUserController::class, 'verifyOtp'])->name('verification.verify-otp');
Route::get('/verification/notice', function () {
    return view('auth.verify-email');
})->name('verification.notice');




Route::middleware(['admin'])->group(function () {
    Route::get('/cart', CartComponent::class)->name('cart');
    Route::get('/checkout', CheckoutComponent::class)->name('checkout');
});

Route::get('locale/{lang}', [LocaleController::class, 'setLocale']);


use App\Http\Controllers\SearchController;

Route::get('/search/clear', [SearchController::class, 'clearRecentSearches'])->name('search.clear');
Route::get('/search/remove/{keyword}', [SearchController::class, 'removeRecentSearch'])->name('search.remove');







// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::view('/chat-interactions', 'admin.chat-interactions')->name('admin.chat-interactions');
    Route::get('/chat-interactions/{id}', [ManageChatbotComponent::class, 'show'])->name('admin.chat-interactions.show');
});

use App\Http\Controllers\AdminSaletimerController;

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/saletimer', [AdminSaletimerController::class, 'index'])->name('admin.saletimer.index');
    Route::post('/saletimer/update', [AdminSaletimerController::class, 'update'])->name('admin.saletimer.update');
});

Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');





require __DIR__ . '/auth.php';
