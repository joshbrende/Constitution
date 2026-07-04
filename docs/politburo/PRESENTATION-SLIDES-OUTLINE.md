# Presentation outline — Politburo briefing (45 minutes)

Use this script to build slides in PowerPoint or Google Slides. Suggested visual identity: party green, gold accent, official logo.

---

## Slide 1 — Title

**ZANU PF Constitution & Academy Digital Platform**  
Prepared for the Politburo  
[Date] | National ICT

---

## Slide 2 — Why now

- Constitutional literacy is strategic.
- Membership certification must be **credible** and **auditable**.
- Provinces need tools that work with **real connectivity** (offline mobile reader).

---

## Slide 3 — What we built

- **Mobile app** for members (Android / iOS).
- **Web portal** for administrators and public verification.
- **One national database** with provincial delegation.

*Diagram: Members → API → Database ← Admins*

---

## Slide 4 — Member experience

- Read constitution **offline**.
- Guest browse without account (constitution only).
- Register → Academy → assessment → **payment receipt**.
- Track application until **physical certificate collection**.

---

## Slide 5 — Certificate workflow (political alignment)

1. Pass assessment  
2. Pay at provincial office (offline)  
3. Provincial admin confirms  
4. **Presidium approves**  
5. Print and collect  

**Not** instant digital certificates — preserves party control.

---

## Slide 6 — Provincial administration

- Provincial admins see **only their province** (district when assigned).
- National roles retain oversight.
- Every sensitive action **audit-logged**.

---

## Slide 7 — Security posture

- Setup wizard locked with install token.
- Encrypted mobile token storage.
- Public certificate verification.
- National ID: **collected today; government verification when integrated**.

---

## Slide 8 — Hardening completed

Reference [GAP-REMEDIATION-LOG.md](./GAP-REMEDIATION-LOG.md):

- Install lock, district scoping, admin route hardening.
- Production Docker profile, mobile CI, guest browse, secure storage.

---

## Slide 9 — Pilot proposal

- **Harare + Bulawayo**, **90 days** (Phase 1 live).
- Provincial admins: `harare.pilot@zanupf.org.zw`, `bulawayo.pilot@zanupf.org.zw`.
- Presidium approver: `presidium.pilot@zanupf.org.zw` (certificate queue).
- Metrics: registrations, profile completion, applications, zero security incidents.

---

## Slide 10 — Decision points

1. Approve pilot provinces.  
2. Endorse Presidium SLA for approvals.  
3. Authorize ICT to proceed with store publication.  
4. Mandate communication to provincial structures.

---

## Slide 11 — Q&A

See [STAKEHOLDER-FAQ.md](./STAKEHOLDER-FAQ.md).

---

## Appendix slides (optional)

- RBAC role table (simplified from RBAC-MATRIX).
- Screenshots: home page, mobile constitution, admin certificate queue.
- Timeline Phase 1 → 3.

---

## Presenter notes

- Emphasize **Presidium gate** — leadership retains certificate authority.
- Do not claim government ID verification until integration is live.
- Offer ICT office hours for provincial chair follow-ups after the meeting.
