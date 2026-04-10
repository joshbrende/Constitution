# 25. API — Dialogue

**Controller:** `App\Http\Controllers\Api\DialogueController`

Requires **Sanctum**.

| Method | Path | Purpose |
|--------|------|---------|
| GET | `/api/v1/dialogue/channels` | List channels (batched unread counts) |
| GET | `/api/v1/dialogue/channels/{channel}/threads` | Threads |
| POST | `/api/v1/dialogue/channels/{channel}/threads` | Create thread |
| GET | `/api/v1/dialogue/threads/{thread}/messages` | Messages |
| POST | `/api/v1/dialogue/threads/{thread}/messages` | Post message |

**Service:** `DialogueChannelService` used for efficient unread counts.

---

*Last reviewed: documentation generation pass.*
