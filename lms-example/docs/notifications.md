# Notifications

In-app database notifications and email for assignment graded and Q&A (facilitator) reply. Users see a bell in the navbar, a notifications index, and receive mail (when `MAIL_*` is configured). Both notification classes implement `ShouldQueue` and use `Queueable`, so sending (database + mail) is queued; run a queue worker for mail and DB writes to be processed asynchronously.

## Queuing

`AssignmentGradedNotification` and `QAReplyNotification` implement `Illuminate\Contracts\Queue\ShouldQueue` and use `Illuminate\Bus\Queueable`. When `notify()` is called, the notification is pushed to the default queue (e.g. `database` when `QUEUE_CONNECTION=database`). Run a worker so jobs are processed:

- `php artisan queue:work`

Ensure the `jobs` table exists (`php artisan queue:table` and `migrate` if using the database driver) and that `QUEUE_CONNECTION` in `.env` is set (e.g. `database` or `redis`). If the queue is not running, notifications will remain in the queue until the worker processes them.

## Channels

- **database** — Stored in `notifications`; shown in the bell dropdown and index. `via()` returns `['database', 'mail']`.
- **mail** — Email via `toMail()` using `MailMessage`; uses `MAIL_*` from `.env`. Subject: “Your assignment has been graded” / “New reply to your question”; action button links to the learn URL.

## Migration

Run `php artisan migrate` to create the `notifications` table (uuid `id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `timestamps`).

## Notification types

### Assignment graded

- **When:** A facilitator grades an assignment (`SubmissionsController::update`).
- **Recipient:** The submission’s author (`$submission->user`).
- **Notification:** `App\Notifications\AssignmentGradedNotification` (implements `ShouldQueue`). `via`: `['database','mail']`. `data`: `variant=assignment_graded`, `message`, `action_url` (learn course + unit), `course_id`, `unit_id`, `course_title`, `score`, `max_points`.

### Q&A reply

- **When:** A facilitator (or admin) posts a reply in facilitator chat (`FacilitatorChatController::store` with `type=reply`).
- **Recipient:** The author of the parent message (`$parent->user_id`), unless they are the replier.
- **Notification:** `App\Notifications\QAReplyNotification` (implements `ShouldQueue`). `via`: `['database','mail']`. `data`: `variant=qa_reply`, `message`, `action_url` (learn course + unit if set), `course_id`, `unit_id`, `course_title`.

## Routes

- `GET /notifications` → `notifications.index` — list (paginated), “Mark all as read” when there are unread.
- `GET /notifications/{id}/read-and-go` → `notifications.read-and-go` — mark one as read, redirect to `data.action_url` (or index).
- `POST /notifications/mark-all-read` → `notifications.mark-all-read` — mark all as read.

## UI

- **Navbar (app, facilitator):** Bell dropdown with unread count, last 5 unread, “View all”.
- **Navbar (learn):** Bell link with unread badge.
- **Index** (`notifications/index`): List of notifications, “View” → read-and-go, “Mark all as read” when any unread.

## Trigger points

- `SubmissionsController::update`: after `$submission->update(...)`, `$submission->user?->notify(new AssignmentGradedNotification($submission))`.
- `FacilitatorChatController::store`: when `type === 'reply'` and `$parent->user_id != $user->id`, `User::find($parent->user_id)?->notify(new QAReplyNotification($m, $parent))`.
