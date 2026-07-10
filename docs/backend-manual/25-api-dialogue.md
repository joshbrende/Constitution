# 25. API — Dialogue

**Controller:** `App\Http\Controllers\Api\DialogueController`

Requires **Sanctum** + `dialogue:read` / `dialogue:write` abilities.

| Method | Path | Purpose |
|--------|------|---------|
| GET | `/api/v1/dialogue/channels` | List channels visible to user (role-scoped) |
| GET | `/api/v1/dialogue/channels/{channel}/threads` | Threads (`authorize` channel view) |
| POST | `/api/v1/dialogue/channels/{channel}/threads` | Create thread (editors) |
| GET | `/api/v1/dialogue/threads/{thread}/messages` | Messages |
| POST | `/api/v1/dialogue/threads/{thread}/messages` | Post message |
| POST | `/api/v1/dialogue/messages/{message}/report` | Report message (requires thread access) |
| POST | `/api/v1/dialogue/threads/{thread}/report` | Report thread |
| POST | `/api/v1/users/{userId}/block` | Block user |
| DELETE | `/api/v1/users/{userId}/block` | Unblock |
| POST | `/api/v1/broadcasting/auth` | Private channel auth (Reverb/Pusher) |

**Access model:** `DialogueChannel::canUserAccess` / `canUserPost` enforce **role** (`min_role_slug`). Province and `is_public` are not applied in access checks (payload may still expose `is_public`).

**Realtime:** When `BROADCAST_CONNECTION=reverb`, message create/update broadcasts `DialogueMessageChanged` on `private-dialogue.thread.{id}`. Clients fall back to polling if Reverb is down. See [PWA.md](../PWA.md) and [SYSTEM-DESIGN-SUPPLY-DEMAND-REALTIME.md](../SYSTEM-DESIGN-SUPPLY-DEMAND-REALTIME.md).

**Service:** `DialogueChannelService` for efficient unread counts; `DialogueInboxService` for notifications.

**Feature flag:** `SiteSetting` / `app-config` → `features.enable_dialogue` — PWA hides Chat when false.

---

*Last reviewed: 2026-07-10 — reports, blocks, realtime, access model.*
