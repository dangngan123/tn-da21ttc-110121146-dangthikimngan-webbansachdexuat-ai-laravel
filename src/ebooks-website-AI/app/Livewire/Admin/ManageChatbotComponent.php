<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ChatInteraction;
use Illuminate\Support\Facades\DB;

class ManageChatbotComponent extends Component
{
    public $stats = [];

    public function mount()
    {
        $this->calculateStats();
    }

    private function calculateStats()
    {
        // 1. Tổng số người dùng chatbot
        $this->stats['total_chatbot_users'] = ChatInteraction::distinct('session_key')->count('session_key');

        // 2. Tương tác từ khách và người dùng đăng nhập (chỉ dựa trên user_id)
        $interactions = ChatInteraction::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN user_id IS NULL THEN 1 ELSE 0 END) as guest_interactions,
            SUM(CASE WHEN user_id IS NOT NULL THEN 1 ELSE 0 END) as user_interactions
        ')->first();
        $this->stats['guest_interactions'] = $interactions->guest_interactions;
        $this->stats['user_interactions'] = $interactions->user_interactions;

        // 3. Người dùng hoạt động trong 1 phút
        $this->stats['active_users_now'] = ChatInteraction::where('created_at', '>=', now()->subMinutes(1))
            ->distinct('session_key')
            ->count('session_key');

        // 4. Tương tác trong 1 giờ qua
        $this->stats['interactions_last_hour'] = ChatInteraction::where('created_at', '>=', now()->subHour())
            ->count();

        // 5. Độ dài trung bình câu hỏi và câu trả lời
        $lengths = ChatInteraction::selectRaw('
            AVG(LENGTH(question)) as avg_question_length,
            AVG(LENGTH(answer)) as avg_answer_length
        ')->first();
        $this->stats['avg_question_length'] = round($lengths->avg_question_length ?? 0);
        $this->stats['avg_answer_length'] = round($lengths->avg_answer_length ?? 0);

        // 6. Tương tác trung bình mỗi người dùng
        $totalInteractions = $interactions->total;
        $this->stats['avg_interactions_per_user'] = $this->stats['total_chatbot_users'] > 0
            ? round($totalInteractions / $this->stats['total_chatbot_users'], 1)
            : 0;

        // 7. Thống kê theo loại dịch vụ hỗ trợ
        $this->stats['support_options'] = ChatInteraction::select('support_option')
            ->groupBy('support_option')
            ->pluck('support_option')
            ->filter()
            ->mapWithKeys(function ($option) {
                return [$option => ChatInteraction::where('support_option', $option)->count()];
            })->toArray();

        // 8. Xu hướng tương tác 24 giờ qua (theo giờ)
        $this->stats['interaction_trend'] = ChatInteraction::selectRaw('
            DATE_FORMAT(created_at, "%H:00") as hour,
            COUNT(*) as count
        ')
            ->where('created_at', '>=', now()->subHours(24))
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        // Điền các giờ thiếu trong 24 giờ qua
        $trend = [];
        for ($i = 23; $i >= 0; $i--) {
            $hour = now()->subHours($i)->format('H:00');
            $trend[$hour] = $this->stats['interaction_trend'][$hour] ?? 0;
        }
        $this->stats['interaction_trend'] = $trend;
    }

    public function render()
    {
        return view('livewire.admin.manage-chatbot-component', [
            'stats' => $this->stats,
        ]);
    }
}