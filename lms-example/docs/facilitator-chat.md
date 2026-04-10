# Facilitator chat / Q&A

Course Q&A: **questions** (from learners), **replies** (from facilitator), **announcements** (from facilitator). Threaded via `in_reply_to_id`. Questions have `status`: `pending`, `answered`, `dismissed`. Used in the learn view and on a dedicated **instructor Q&A page**. Reply to a question sends `QAReplyNotification` to the question author (unless they are the replier).

## Model and migration

**`facilitator_chat_messages`**: `id`, `course_id`, `unit_id` (nullable), `user_id`, `body`, `type` (`question`|`reply`|`announcement`), `in_reply_to_id` (nullable, self FK), `status` (`pending`|`answered`|`dismissed`, nullable), `timestamps`.

**`FacilitatorChatMessage`**: `course()`, `unit()`, `user()`, `inReplyTo()`, `replies()`; `isQuestion()`, `isReply()`, `isAnnouncement()`.

## Routes

| Method | URI | Name | Who |
|--------|-----|------|-----|
| GET | `/learn/{course}/facilitator-chat` | `learn.facilitator-chat.index` | JSON: list messages (optional `?unit_id=`) |
| POST | `/learn/{course}/facilitator-chat` | `learn.facilitator-chat.store` | JSON: create question/reply/announcement |
| PATCH | `/learn/{course}/facilitator-chat/{message}` | `learn.facilitator-chat.update` | JSON: update question `status` |
| GET | `/instructor/courses/{course}/facilitator-chat` | `instructor.facilitator-chat` | HTML: standalone Q&A page (facilitator) |

## Access

- **`ensureAccess($course)`:** User must be logged in and either enrolled or `canEditCourse($course)`.

## Index (JSON)

- **Query:** `unit_id` optional: filter to that unit or `unit_id` null (course-level). Roots: `in_reply_to_id` null, ordered by `CASE type WHEN 'announcement' THEN 0 WHEN 'question' THEN 1 END`, then `created_at` desc. Replies: `in_reply_to_id` not null, grouped by parent.
- **Response:** `items` (nested `replies`), `can_reply`, `can_announce`, `can_update_status` (all true when `canEditCourse`).

## Store (JSON)

- **Body:** `body` (required, max 4000), `type` (required, `question`|`reply`|`announcement`), `in_reply_to_id` (nullable, for `reply`), `unit_id` (nullable).
- **Reply:** `in_reply_to_id` required; only `canEditCourse` can reply; `unit_id` inherited from parent. On create: if `parent.user_id != current user`, send `QAReplyNotification($m, $parent)` to `parent.user_id`.
- **Announcement:** only `canEditCourse`; `in_reply_to_id` forced null.
- **Question:** `in_reply_to_id` null; `unit_id` from request (e.g. current unit in learn). `status` set to `pending` for questions.

## Update (JSON)

- Only facilitator; only messages with `type=question`. `status` required: `answered` or `dismissed`.

## Instructor page

- `instructorPage($course)`: `canEditCourse($course)`. Renders `facilitator.facilitator-chat` with `$course`; that view uses the same JSON endpoints for list/post/update.

## UI

- **Learn view:** Q&A panel (or tab) calling `learn.facilitator-chat` index; form to post question (or reply/announcement when facilitator). Optional `unit_id` from current unit.
- **Instructor:** Link from course/instructor dashboard to `instructor.facilitator-chat` for that course.
