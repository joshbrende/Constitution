# Provincial rollout plan

## Phase 0 — National ICT (complete before any pilot)

- [ ] Production server hardened ([PRODUCTION-HARDENING.md](../PRODUCTION-HARDENING.md))
- [ ] `SETUP_ACCESS_TOKEN` generated and install completed on secure network
- [ ] HTTPS certificate installed
- [ ] SMTP for member emails
- [ ] Queue worker + cron (`schedule:run`) running
- [ ] Mobile app submitted to stores (or internal distribution for pilot)
- [ ] Provincial admin accounts created with correct `province_id` / `district_id`
- [ ] Presidium approvers trained on certificate queue

## Phase 1 — Pilot (2 provinces, 90 days)

**Selected provinces:** **Harare** and **Bulawayo** (metropolitan pilot pair).

Operational runbook: [PILOT-PHASE-1-HARARE-BULAWAYO.md](./PILOT-PHASE-1-HARARE-BULAWAYO.md)

**Week 1–2: Preparation**

- Select pilot provinces and provincial ICT focal persons.
- Train provincial admins: users, payment confirmation, certificate applications.
- Launch member communication: register → complete profile → academy.

**Week 3–8: Live operations**

- Monitor daily: registrations, assessment errors, application backlog.
- Weekly national ICT stand-up with provincial reports.
- Presidium SLA: approve within agreed days.

**Week 9–12: Review**

- Metrics vs targets (see below).
- Security audit sample (audit logs, access reviews).
- Go/no-go for Phase 2.

### Pilot success criteria

| Metric | Target (indicative) |
|--------|---------------------|
| Profile completion (province + ID) | > 70% of active learners |
| Assessment completion rate | Baseline established |
| Certificate applications with confirmed payment | > 50 in pilot provinces |
| Critical security incidents | 0 |
| Provincial scope violations (audit) | 0 |

## Phase 2 — Expansion (6–8 additional provinces)

- Repeat Phase 1 playbook per province cohort.
- Refine training materials from pilot feedback.
- Enable store badges and public site URL on national home page.

## Phase 3 — National rollout

- All provinces live.
- National ID integration (if MOU signed).
- Optional: branch-level admin scoping.
- Push notifications for certificate status.
- SMS/USSD channel evaluation for members without smartphones.

## Rollback plan

- Disable new registrations (maintenance mode) if critical defect found.
- Preserve database; no member data deleted on rollback.
- Communicate via provincial structures and home banners API.

## Governance

| Body | Responsibility |
|------|----------------|
| Politburo | Approve phase transitions |
| Presidium | Certificate approval policy |
| National ICT | Platform operations |
| Provincial chairs | Member adoption + provincial admin conduct |
