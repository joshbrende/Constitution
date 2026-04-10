# 16. Analytics & reports (admin)

## 16.1 Purpose

Dashboard for **key metrics**: registered members (users who **passed** at least one assessment), academy stats, province leaderboard context, dialogue counts, certificates, exports.

## 16.2 Routes

- `admin.analytics.index` — main analytics view
- `admin.analytics.export.enrolments` — CSV export
- `admin.analytics.export.attempts` — CSV export

**Controller:** `App\Http\Controllers\Admin\AdminAnalyticsController`

## 16.3 Services

- **`ProvinceStatsService`** — batched province stats (members who passed, attempts, enrolments, certificates) to avoid N+1 queries.

## 16.4 Metric definitions (important)

- **Registered members** — Count of users with at least one **passing** graded assessment (not merely app registration).
- **Members column (by province)** — Users in province with at least one passing attempt.

## 16.5 Access

`analytics_viewer` and full editorial roles per `config/admin.php`.

---

*Last reviewed: documentation generation pass.*
