<?php

namespace App\Services;

use App\Models\DialogueChannel;
use App\Models\DialogueMessage;
use App\Models\DialogueThreadRead;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DialogueChannelService
{
    /**
     * Get channels with unread counts and official-reply flags, using batched queries.
     */
    public function channelsForUser(?User $user): array
    {
        $channels = DialogueChannel::with(['defaultZanupfSection', 'defaultZimbabweSection', 'threads'])
            ->orderBy('name')
            ->get();

        if (! $user) {
            return $this->mapChannelsWithoutUser($channels);
        }

        $threadIds = $channels->flatMap(fn ($c) => $c->threads->pluck('id'))->unique()->values()->all();
        if (empty($threadIds)) {
            return $this->mapChannelsWithCounts($channels, [], []);
        }

        $unreadByThread = $this->unreadCountsByThread($user->id, $threadIds);
        $hasOfficialByChannel = $this->hasOfficialReplyByChannel($user->id, $channels->pluck('id')->all());

        return $this->mapChannelsWithCounts($channels, $unreadByThread, $hasOfficialByChannel);
    }

    /**
     * Batch unread counts: thread_id => count.
     */
    private function unreadCountsByThread(int $userId, array $threadIds): array
    {
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

    /**
     * Batch check has_official_reply: channel_id => bool.
     * Official = latest message from user named 'System' in channel; true if user hasn't read it.
     */
    private function hasOfficialReplyByChannel(int $userId, array $channelIds): array
    {
        if (empty($channelIds)) {
            return [];
        }

        $placeholders = implode(',', array_map(fn () => '?', $channelIds));
        $latestRows = DB::select(
            "SELECT t.dialogue_channel_id as channel_id, m.dialogue_thread_id as thread_id, m.created_at
             FROM dialogue_messages m
             JOIN dialogue_threads t ON t.id = m.dialogue_thread_id
             JOIN users u ON u.id = m.user_id AND u.name = 'System'
             WHERE t.dialogue_channel_id IN ({$placeholders})
               AND m.is_deleted = 0
             ORDER BY m.created_at DESC",
            $channelIds
        );

        $channelToLatest = [];
        foreach ($latestRows as $row) {
            $cid = (int) $row->channel_id;
            if (! isset($channelToLatest[$cid])) {
                $channelToLatest[$cid] = [
                    'thread_id' => (int) $row->thread_id,
                    'created_at' => $row->created_at,
                ];
            }
        }

        $threadIdsNeeded = array_values(array_unique(array_column($channelToLatest, 'thread_id')));
        $reads = collect();
        if (! empty($threadIdsNeeded)) {
            $reads = DialogueThreadRead::whereIn('dialogue_thread_id', $threadIdsNeeded)
                ->where('user_id', $userId)
                ->get()
                ->keyBy('dialogue_thread_id');
        }

        $result = [];
        foreach ($channelIds as $cid) {
            $latest = $channelToLatest[$cid] ?? null;
            if (! $latest) {
                $result[$cid] = false;
                continue;
            }
            $read = $reads->get($latest['thread_id']);
            $lastReadAt = $read?->last_read_at;
            $createdAt = $latest['created_at'];
            if (is_string($createdAt)) {
                $createdAt = \Carbon\Carbon::parse($createdAt);
            }
            $result[$cid] = ! $lastReadAt || $createdAt->gt($lastReadAt);
        }

        return $result;
    }

    private function mapChannelsWithoutUser($channels): array
    {
        return $channels->map(function (DialogueChannel $c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
                'description' => $c->description,
                'is_public' => (bool) $c->is_public,
                'unread_count' => 0,
                'has_official_reply' => false,
                'constitution_links' => [
                    'zanupf' => $c->defaultZanupfSection ? [
                        'section_id' => $c->defaultZanupfSection->id,
                        'title' => $c->defaultZanupfSection->title,
                    ] : null,
                    'zimbabwe' => $c->defaultZimbabweSection ? [
                        'section_id' => $c->defaultZimbabweSection->id,
                        'title' => $c->defaultZimbabweSection->title,
                    ] : null,
                ],
            ];
        })->all();
    }

    private function mapChannelsWithCounts($channels, array $unreadByThread, array $hasOfficialByChannel): array
    {
        return $channels->map(function (DialogueChannel $c) use ($unreadByThread, $hasOfficialByChannel) {
            $unreadCount = 0;
            foreach ($c->threads as $thread) {
                $unreadCount += $unreadByThread[$thread->id] ?? 0;
            }

            return [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
                'description' => $c->description,
                'is_public' => (bool) $c->is_public,
                'unread_count' => $unreadCount,
                'has_official_reply' => $hasOfficialByChannel[$c->id] ?? false,
                'constitution_links' => [
                    'zanupf' => $c->defaultZanupfSection ? [
                        'section_id' => $c->defaultZanupfSection->id,
                        'title' => $c->defaultZanupfSection->title,
                    ] : null,
                    'zimbabwe' => $c->defaultZimbabweSection ? [
                        'section_id' => $c->defaultZimbabweSection->id,
                        'title' => $c->defaultZimbabweSection->title,
                    ] : null,
                ],
            ];
        })->all();
    }
}
