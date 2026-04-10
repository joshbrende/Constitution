# 11. Dialogue (admin)

## 11.1 Purpose

Moderate **channels**, **threads**, and **messages** for Opinion and Dialogue (Presidium and League conversations in policy).

## 11.2 Admin routes

| Action | Route |
|--------|--------|
| Channel list | `admin.dialogue.index` |
| Threads | `admin.dialogue.threads.index`, `admin.dialogue.threads.show` |
| Create thread | `admin.dialogue.threads.store` |
| Lock / unlock | `admin.dialogue.threads.lock`, `.unlock` |
| Post as admin | `admin.dialogue.messages.store` |
| Pin / unpin | `admin.dialogue.messages.pin`, `.unpin` |
| Delete message | `admin.dialogue.messages.destroy` |

**Controller:** `App\Http\Controllers\Admin\DialogueController`

## 11.3 Access

Roles with `dialogue` section: see `config/admin.php` — includes `dialogue_moderator`, `moderator`, plus full editors.

## 11.4 API (mobile)

Authenticated users: channels, threads, messages — see [24-api-dialogue.md](./24-api-dialogue.md).

---

*Last reviewed: documentation generation pass.*
