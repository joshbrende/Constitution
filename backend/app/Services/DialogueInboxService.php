<?php

namespace App\Services;

use App\Models\DialogueChannel;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DialogueInboxService
{
    /**
     * Total unread dialogue messages across all channels the user can access.
     */
    public function unreadMessageCount(User $user): int
    {
        $threadIds = $this->accessibleThreadIds($user);
        if ($threadIds === []) {
            return 0;
        }

        $counts = $this->unreadCountsByThread($user->id, $threadIds);

        return array_sum($counts);
    }

    /**
     * @return list<int>
     */
    public function accessibleThreadIds(User $user): array
    {
        return DialogueChannel::query()
            ->with('threads:id,dialogue_channel_id')
            ->orderBy('name')
            ->get()
            ->filter(fn (DialogueChannel $channel) => $channel->canUserAccess($user))
            ->flatMap(fn (DialogueChannel $channel) => $channel->threads->pluck('id'))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  list<int>  $threadIds
     * @return array<int, int> thread_id => unread count
     */
    public function unreadCountsByThread(int $userId, array $threadIds): array
    {
        if ($threadIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($threadIds), '?'));
        $params = array_merge([$userId], $threadIds);

        $rows = DB::select(
            "SELECT dm.dialogue_thread_id as thread_id, COUNT(*) as cnt
             FROM dialogue_messages dm
             LEFT JOIN dialogue_thread_reads dr
               ON dr.dialogue_thread_id = dm.dialogue_thread_id AND dr.user_id = ?
             WHERE dm.dialogue_thread_id IN ({$placeholders})
               AND dm.is_deleted = 0
               AND (dr.last_read_at IS NULL OR dm.created_at > dr.last_read_at)
             GROUP BY dm.dialogue_thread_id",
            $params
        );

        $result = [];
        foreach ($rows as $row) {
            $result[(int) $row->thread_id] = (int) $row->cnt;
        }

        return $result;
    }
}
