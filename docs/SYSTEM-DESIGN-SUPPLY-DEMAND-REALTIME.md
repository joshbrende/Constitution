# System Design: Supply–Demand & Real-Time Engineering

This document applies **supply–demand** and **real-time engineering** thinking to the ZANU PF Constitution app so the experience stays smooth under load and users get clear, live feedback instead of silent waits or timeouts.

---

## 1. Demand vs supply (conceptual model)

| **Demand** (what users do) | **Supply** (what the system provides) | **Smoothing goal** |
|----------------------------|---------------------------------------|--------------------|
| List courses, open course, start assessment | API + DB read capacity | Match read supply to demand via **caching** and pre-fetch. |
| Submit assessment, request certificate PDF | API + DB write; PDF generation (CPU-heavy) | Decouple demand spike from supply: **queue** PDF work; serve from cache/disk when ready. |
| Many concurrent assessment starts (e.g. campaign day) | Web workers, DB connections | **Throttle** or queue non-critical work; return 429/503 with Retry-After when overloaded. |
| Verification page hits (public, high read) | API + DB or cache | Serve from **cache** (e.g. Redis) by `verification_code`; minimal DB hit. |

**Principle:** Smooth **demand** (buffer, queue, cache) and signal **supply limits** (rate limits, queue position, ETA) so the experience stays predictable.

---

## 2. Real-time design: what the user should see

- **Immediate feedback**  
  Every action has an immediate UI response (e.g. “Request submitted”, “Generating your certificate…”), not a blank screen.

- **Progress when work is async**  
  For certificate download: show “Generating…” then “Ready – tap to download” or “Position in queue: 3” (if implemented), instead of a long spinner with no context.

- **Transparent degradation**  
  If the server is busy: “High demand – try again in a moment” with optional retry countdown, rather than a generic error or timeout.

- **Offline / sync**  
  Where applicable (e.g. constitution browser, bookmarks): show cached data and queue writes; sync when back online and surface “Synced” / “Pending sync” so the user understands state.

---

## 3. Backend: supply–demand and real-time enablers

### 3.1 Certificate PDF: queue + status (demand smoothing)

- **Current:** PDF generated **synchronously** on `GET certificates/{id}/pdf` → demand spike = CPU spike and slow responses.
- **Target:**
  - `POST certificates/{id}/generate` (or trigger on first request) dispatches a **job** to generate the PDF.
  - PDF written to disk or object storage; path/URL stored on the certificate (e.g. `pdf_path` or `pdf_url`).
  - **Status:** `GET certificates/{id}` (or a small status endpoint) returns `pdf_status: pending | generating | ready` and, when ready, download streams from file.
- **Real-time:** Mobile polls status every 2–3 s while `pending`/`generating`, then shows “Download” when `ready`; optional “Position in queue: N” if the backend exposes queue depth.

### 3.2 Read-heavy endpoints: cache (increase effective supply)

- **Courses list, course detail (with modules/lessons), membership course:**  
  Cache in Redis with TTL (e.g. 5–15 minutes); invalidate on course/content update.
- **Constitution parts/chapters/sections (public):**  
  Cache by section/chapter ID; long TTL unless content is updated.
- **Verification page (by verification_code):**  
  Cache verification result (valid/invalid + minimal display data) by code; short TTL (e.g. 1–5 minutes) to reduce DB load on viral shares.

### 3.3 Rate limiting (cap demand to protect supply)

- Apply per-user and/or per-IP limits on:
  - Certificate generation/download (e.g. 5/min per user).
  - Assessment start/submit (e.g. 10/min per user to prevent scripted abuse).
  - Public verification endpoint (e.g. 60/min per IP).
- Return **429 Too Many Requests** with `Retry-After` header; mobile shows “Too many requests – try again in X seconds.”

### 3.4 Graceful overload response

- When the server is under heavy load (e.g. queue depth above threshold, or high CPU):
  - Optionally return **503 Service Unavailable** with `Retry-After` for non-critical endpoints.
- Mobile: show “High demand – we’ll retry in a moment” and auto-retry with backoff.

---

## 4. Mobile: real-time and demand smoothing

1. **Certificate flow**  
   On “Download certificate”: call backend; if response is “job queued” / status `pending`, show “Generating your certificate…” and poll status until `ready`, then show “Download” and stream PDF. Avoid long blocking request with no progress.

2. **Assessment flow**  
   After “Start assessment”, assessment payload is already fetched; keep it in memory/state for the session. Optional: cache course list and membership course summary (short TTL) so repeat opens feel instant.

3. **Constitution / content**  
   Use existing offline-first strategy: cache content locally; background sync when online. Show “Last updated …” or “Synced” so users have a sense of freshness.

4. **Errors and retries**  
   Map 429 → “Too many requests – try again in X s.”  
   Map 503 → “High demand – retrying in X s.” with exponential backoff.  
   Show a single “Retry” action where appropriate instead of leaving the user on a dead error screen.

---

## 5. Implementation order (recommended)

| Priority | Item | Impact |
|----------|------|--------|
| 1 | Certificate PDF: queue job + status endpoint + mobile poll → download when ready | Removes PDF demand spike from request path; clear real-time feedback. |
| 2 | Rate limiting on certificate and assessment endpoints | Protects supply under abuse or viral load. |
| 3 | Redis cache for courses list, course detail, verification by code | Increases effective read supply; smoother experience on list/detail and verification. |
| 4 | 429/503 handling and retry UI on mobile | Real-time behaviour under load; users understand what’s happening. |
| 5 | Optional: queue depth or ETA for certificate (e.g. “Position in queue: 2”) | Extra transparency when demand is high. |

---

## Summary

- **Supply–demand:** Use **queues** for heavy work (PDF), **caches** for read-heavy data (courses, verification), and **rate limits** to cap demand so supply (workers, DB, CPU) is not overwhelmed.
- **Real-time:** Give **immediate feedback**, **progress/status** for async work, and **clear messages** (429/503 + retry) so the app feels responsive and predictable even under load.

This design aligns with the existing plan (Laravel Queues, Redis cache) and extends it with concrete flows and implementation order.
