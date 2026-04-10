# Admin Academy Analytics Enhancement – Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add the missing Academy analytics features to the admin dashboard: average completion %, improvement indicators, total enrolments, membership growth graph, Recent Activity, Calendar, active users per day, assessments passed/failed, and inactive users.

**Architecture:** Extend `AdminAnalyticsController` with new queries; reuse existing `audit_logs` and `sessions` tables for activity/inactivity; add an Academy-focused section to the analytics view with new stat cards, graphs, and panels. No new migrations or dependencies—use existing data.

**Tech Stack:** Laravel 12, PHP 8.2, Blade, existing dashboard CSS (no Chart.js).

---

## File Structure

| File | Responsibility |
|------|----------------|
| `backend/app/Http/Controllers/Admin/AdminAnalyticsController.php` | Add new metrics queries and pass to view |
| `backend/resources/views/admin/analytics/index.blade.php` | Render new stat cards, graphs, Recent Activity, Calendar, Academy section |

---

## Task 1: Backend – New Metrics in AdminAnalyticsController

**Files:**
- Modify: `backend/app/Http/Controllers/Admin/AdminAnalyticsController.php`

- [ ] **Step 1: Add total enrolments and average completion %**

Compute:
- `$totalEnrolments` = `Enrolment::count()`
- `$completedEnrolments` = `Enrolment::whereNotNull('completed_at')->count()`
- `$avgCompletionPct` = `$totalEnrolments > 0 ? round(($completedEnrolments / $totalEnrolments) * 100, 1) : null`

Add to the `index()` method after the membership course block (around line 38). Add both variables to the `compact()` array.

- [ ] **Step 2: Add improvement indicators (prior period comparison)**

Compute previous 30 days vs current 30 days for key metrics:
- `$membersPrev30` = users created in `[$monthAgo->copy()->subMonth(), $monthAgo)`
- `$membersCurr30` = `$newMembersLast30` (already exists)
- `$membersImprovement` = `$membersPrev30 > 0 ? round((($membersCurr30 - $membersPrev30) / $membersPrev30) * 100, 1) : ($membersCurr30 > 0 ? 100 : 0)`
- `$certificatesPrev30` = certificates issued in prior 30 days
- `$certificatesCurr30` = `$certificatesLast30`
- `$certificatesImprovement` = same formula
- `$completionsPrev30` = enrolments with `completed_at` in prior 30 days
- `$completionsCurr30` = enrolments with `completed_at` in last 30 days
- `$completionsImprovement` = same formula

Add these to `index()` and `compact()`.

- [ ] **Step 3: Add assessments passed and failed counts**

We already have `$passedAttempts` and `$totalAttempts`. Add:
- `$failedAttempts` = `$totalAttempts - $passedAttempts`

Add to `compact()`.

- [ ] **Step 4: Add membership growth by month**

```php
$membersByMonth = User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as ym, COUNT(*) as total')
    ->where('created_at', '>=', $now->copy()->subMonths(5)->startOfMonth())
    ->groupBy('ym')
    ->orderBy('ym')
    ->pluck('total', 'ym');
```

Add to `compact()`.

- [ ] **Step 5: Add active users per day (last 7 days)**

Use `audit_logs` with login actions. Distinct users per day:

```php
$activeUsersByDay = \App\Models\AuditLog::whereIn('action', ['auth.web.logged_in', 'auth.api.logged_in'])
    ->where('created_at', '>=', $now->copy()->subDays(7)->startOfDay())
    ->selectRaw('DATE(created_at) as dt, COUNT(DISTINCT actor_user_id) as total')
    ->groupBy('dt')
    ->orderBy('dt')
    ->pluck('total', 'dt');
```

Add to `compact()`.

- [ ] **Step 6: Add inactive users count**

Users with no session in last 30 days. Sessions use `last_activity` (Unix timestamp).

```php
$activeUserIds = \DB::table('sessions')
    ->whereNotNull('user_id')
    ->where('last_activity', '>=', $now->copy()->subDays(30)->timestamp)
    ->distinct()
    ->pluck('user_id');
$inactiveUsersCount = User::whereNotIn('id', $activeUserIds)->count();
```

Add `$inactiveUsersCount` to `compact()`.

- [ ] **Step 7: Add Recent Activity from audit logs**

```php
$recentActivity = \App\Models\AuditLog::with('actor:id,name,email')
    ->latest()
    ->limit(20)
    ->get();
```

Add to `compact()`.

- [ ] **Step 8: Add calendar data (activity by date, last 35 days)**

For a simple calendar heatmap: count events per day (logins + completions + certificates):

```php
$activityByDate = collect();
// Logins per day
$loginsByDay = \App\Models\AuditLog::whereIn('action', ['auth.web.logged_in', 'auth.api.logged_in'])
    ->where('created_at', '>=', $now->copy()->subDays(34)->startOfDay())
    ->selectRaw('DATE(created_at) as dt, COUNT(*) as cnt')
    ->groupBy('dt')
    ->pluck('cnt', 'dt');
// Completions per day
$completionsByDay = Enrolment::whereNotNull('completed_at')
    ->where('completed_at', '>=', $now->copy()->subDays(34)->startOfDay())
    ->selectRaw('DATE(completed_at) as dt, COUNT(*) as cnt')
    ->groupBy('dt')
    ->pluck('cnt', 'dt');
// Certificates per day
$certsByDay = Certificate::where('issued_at', '>=', $now->copy()->subDays(34)->startOfDay())
    ->selectRaw('DATE(issued_at) as dt, COUNT(*) as cnt')
    ->groupBy('dt')
    ->pluck('cnt', 'dt');
// Merge into single keyed by date
$allDates = $now->copy()->subDays(34)->startOfDay();
for ($i = 0; $i < 35; $i++) {
    $d = $allDates->copy()->addDays($i)->format('Y-m-d');
    $activityByDate[$d] = ($loginsByDay[$d] ?? 0) + ($completionsByDay[$d] ?? 0) + ($certsByDay[$d] ?? 0);
}
```

Add `$activityByDate` to `compact()`.

---

## Task 2: View – Academy Section and New Stat Cards

**Files:**
- Modify: `backend/resources/views/admin/analytics/index.blade.php`

- [ ] **Step 1: Add Academy subsection with new stat cards**

After the existing key metrics grid, add an `<h3>Academy</h3>` and a grid containing:
- Average completion % (with improvement indicator if we have prior data)
- Total enrolments
- Assessments passed (with failed in subtitle)
- Inactive users

Use existing `.dash-stat` styling. For improvement, show e.g. `↑ 12%` or `↓ 5%` in green/red.

- [ ] **Step 2: Add improvement indicators to existing stats**

Add small improvement badges to: Registered members, Certificates issued, and (if membership course) Membership course completions. Reuse the improvement variables from Task 1.

- [ ] **Step 3: Add Membership growth graph**

Render `$membersByMonth` as a bar chart using the same pattern as "Certificates issued – last 6 months" (flex row of cards). Title: "Membership growth – last 6 months".

- [ ] **Step 4: Add Active users per day (last 7 days)**

Render `$activeUsersByDay` as a simple bar/row. Title: "Active users (logins) per day – last 7 days". Fill missing days with 0.

- [ ] **Step 5: Add Recent Activity panel**

New panel with title "Recent Activity". Loop `$recentActivity` and render each item: actor name, action (human-readable label), target if applicable, `created_at`. Map action keys to labels, e.g. `auth.web.logged_in` → "Logged in (web)".

- [ ] **Step 6: Add Calendar panel**

Simple grid: 5 columns × 7 rows for last 35 days. Each cell = one day. Show date (e.g. 22) and activity count. Use background opacity or color intensity based on count (0 = faint, high = stronger). Title: "Activity calendar – last 35 days".

---

## Task 3: Polish and Edge Cases

**Files:**
- Modify: `backend/app/Http/Controllers/Admin/AdminAnalyticsController.php`
- Modify: `backend/resources/views/admin/analytics/index.blade.php`

- [ ] **Step 1: Handle empty active users query**

When no sessions exist, `$activeUserIds` may be empty—`whereNotIn('id', [])` can behave differently across DBs. Use:

```php
$inactiveUsersCount = $activeUserIds->isEmpty()
    ? User::count()
    : User::whereNotIn('id', $activeUserIds)->count();
```

- [ ] **Step 2: Ensure all compact variables are passed**

Audit the controller `return view(..., compact(...))` and ensure every variable used in the view is listed.

- [ ] **Step 3: Responsive layout for new panels**

Ensure new sections don’t overflow on narrow viewports. Use `grid-template-columns: repeat(auto-fit, minmax(...))` for stat grids; stack panels vertically on small screens.

---

## Task 4: Verification

- [ ] **Step 1: Load /admin/analytics**

Visit `http://localhost:8000/admin/analytics` as admin. Confirm no PHP errors and all new sections render.

- [ ] **Step 2: Verify with empty data**

If possible, test with minimal data (no enrolments, no audit logs) to ensure no division-by-zero or empty-collection errors.

- [ ] **Step 3: Verify improvement indicators**

With data in both current and prior periods, confirm improvement percentages compute correctly (manual spot-check).

---

## Execution Handoff

After implementing:

**Plan saved to `docs/superpowers/plans/2026-03-22-admin-academy-analytics.md`.**

**Execution options:**

1. **Subagent-Driven (recommended)** – Dispatch a fresh subagent per task, review between tasks.
2. **Inline Execution** – Execute tasks in this session using executing-plans with checkpoints.

**Which approach?**
