<?php

namespace App\Exports;

use App\Models\ChatInteraction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ChatInteractionsExport implements FromCollection, WithHeadings
{
    protected $selectedItems;

    public function __construct($selectedItems = [])
    {
        $this->selectedItems = $selectedItems;
    }

    public function collection()
    {
        $query = ChatInteraction::query();

        if (!empty($this->selectedItems)) {
            $query->whereIn('id', $this->selectedItems);
        }

        return $query->get()->map(function ($interaction) {
            $messages = json_decode($interaction->messages, true) ?? [];
            $messageHistory = '';
            foreach ($messages as $msg) {
                if ($msg['user']) {
                    $messageHistory .= "User ({$msg['timestamp']}): {$msg['user']}\n";
                }
                $repliedBy = $msg['replied_by'] ?? 'Chatbot';
                $messageHistory .= "$repliedBy ({$msg['timestamp']}): {$msg['bot']}\n";
            }

            return [
                'name' => $interaction->name,
                'email' => $interaction->email,
                'phone' => $interaction->phone,
                'support_option' => $interaction->support_option,
                'question' => $interaction->question,
                'answer' => $interaction->answer,
                'status' => $interaction->status ?? 'unreplied',
                'messages' => $messageHistory,
                'created_at' => $interaction->created_at->toDateTimeString(),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Tên',
            'Email',
            'Số điện thoại',
            'Dịch vụ',
            'Câu hỏi',
            'Câu trả lời',
            'Trạng thái',
            'Lịch sử tin nhắn',
            'Thời gian',
        ];
    }
}