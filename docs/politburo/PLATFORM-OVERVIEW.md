# Platform overview — stakeholders

## Architecture (non-technical)

```
Members (mobile app)  ──►  Secure API  ──►  Central database
Administrators (web)  ──►  Admin portal ──►  Same database
Public (web)          ──►  Home / verify certificate
```

- **Mobile** (`mobile/`) — member-facing: constitution, academy, dialogue, profile.
- **Backend** (`backend/`) — Laravel: API, admin CMS, certificates, audit.
- **Deployment** — Docker (recommended) or WAMP for provincial data centres.

## Member journey

1. **Register** (web or mobile) — accept terms; province added later in profile.
2. **Study** — constitution offline on mobile; library and party content online.
3. **Academy** — enrol, complete assessments (requires national ID + province).
4. **Certificate application** — receive payment receipt PDF; pay at provincial office.
5. **Provincial confirmation** — admin confirms payment.
6. **Presidium approval** — national gate before printing.
7. **Collection** — physical certificate; public verification via web.

## Administrative roles (summary)

| Role | Typical holder | Scope |
|------|----------------|-------|
| System administrator | National ICT | Full platform |
| Provincial administrator | Provincial chair ICT | Users/applications in province (district when assigned) |
| Academy manager | National/provincial education | Courses, payment confirmation |
| Presidium | Leadership | Certificate approval |
| Content editor | HQ communications | Constitution, library, banners |

Full matrix: [RBAC-MATRIX.md](../RBAC-MATRIX.md).

## Geographic model

- **Province** — primary delegation unit for members and certificate applications.
- **District** — when a provincial admin is assigned a district, lists are further scoped.
- **Branch / cell** — fields on member profile; full admin scoping is a future phase.

## Public surfaces

- **Home page** — professional introduction, log in, app store links (when configured).
- **Certificate verification** — anyone can verify authenticity with number + code.
- **Legal pages** — privacy, terms, cookies.

## What members do *not* get from the app

- Instant downloadable membership certificate (by design — aligns with physical issuance).
- Admin or content management (web only).

## Success metrics for pilot

- Registrations and profile completion (province + national ID).
- Academy enrolments and pass rates per province.
- Certificate applications through each workflow stage.
- Audit log review — no unauthorized provincial data access.
