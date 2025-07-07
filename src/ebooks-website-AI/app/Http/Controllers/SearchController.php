<?php

namespace App\Http\Controllers;

use App\Models\SearchHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function clearRecentSearches(Request $request)
    {
        Log::info('clearRecentSearches called', [
            'user_id' => Auth::id() ?? 'Guest',
            'recent_searches_before' => $this->getRecentSearches(),
        ]);

        if (Auth::check()) {
            $deletedCount = SearchHistory::where('user_id', Auth::id())->delete();
            Log::info('Search history deleted from database', [
                'user_id' => Auth::id(),
                'deleted_count' => $deletedCount,
            ]);
        } else {
            $recentBefore = Session::get('recent_searches', []);
            Session::forget('recent_searches');
            Log::info('Search history deleted from session', [
                'recent_searches_before' => $recentBefore,
                'recent_searches_after' => Session::get('recent_searches', []),
            ]);
        }

        return redirect()->back()->with('message', 'Lịch sử tìm kiếm đã được xóa.');
    }

    public function removeRecentSearch(Request $request, $keyword)
    {
        $trimmed = trim(urldecode($keyword));

        Log::info('removeRecentSearch called', [
            'user_id' => Auth::id() ?? 'Guest',
            'keyword' => $trimmed,
            'recent_searches_before' => $this->getRecentSearches(),
        ]);

        if (Auth::check()) {
            $deletedCount = SearchHistory::where('user_id', Auth::id())
                ->where('keyword', $trimmed)
                ->delete();
            Log::info('Recent search deleted from database', [
                'user_id' => Auth::id(),
                'keyword' => $trimmed,
                'deleted_count' => $deletedCount,
            ]);
        } else {
            $recentBefore = Session::get('recent_searches', []);
            $recent = array_filter($recentBefore, fn($item) => $item !== $trimmed);
            Session::put('recent_searches', array_values($recent));
            Log::info('Recent search deleted from session', [
                'keyword' => $trimmed,
                'recent_searches_before' => $recentBefore,
                'recent_searches_after' => Session::get('recent_searches', []),
            ]);
        }

        return redirect()->back()->with('message', 'Đã xóa từ khóa tìm kiếm.');
    }

    private function getRecentSearches()
    {
        if (Auth::check()) {
            return SearchHistory::where('user_id', Auth::id())
                ->orderByDesc('searched_at')
                ->limit(5)
                ->pluck('keyword')
                ->toArray();
        } else {
            return Session::get('recent_searches', []);
        }
    }
}
