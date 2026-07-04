# Stakeholder FAQ

## General

**What is this platform?**  
A national system for constitutional education, academy learning, and membership certification under party governance.

**Who uses the mobile app?**  
Members and learners. Administrators use the web portal.

**Can someone read the constitution without registering?**  
Yes on mobile (“Browse constitution without signing in”) and via public web content APIs.

## Membership and certificates

**Do members download certificates from the phone?**  
No. They receive a payment receipt, complete offline payment, and collect a **physical certificate** after Presidium approval.

**Who approves certificates?**  
Presidium (national gate), after provincial payment confirmation.

**How does the public verify a certificate?**  
Web page: certificate number + verification code.

## Provincial operations

**What can a provincial administrator see?**  
Users, members, and certificate applications in their province — or district if they are assigned one.

**Can provinces edit the national constitution text?**  
No. Content editing is limited to national content roles unless explicitly granted.

## Security and privacy

**Is the National ID verified with government systems?**  
Not yet. IDs are validated for format and stored for membership; live verification requires the Gov ICT integration (planned).

**What if a phone is lost?**  
Member should change password from another device when possible; tokens are stored in the device secure enclave.

**Are admin actions recorded?**  
Yes — audit logs with integrity verification.

## Technical

**Where is data hosted?**  
On infrastructure designated by national ICT (party server or approved data centre).

**What is needed to install?**  
Setup wizard with install token, Docker or WAMP, MySQL, Redis for queues, HTTPS, and email.

## Rollout

**Why pilot first?**  
To train provincial admins, validate certificate workflow, and confirm security before national scale.

**When can all provinces go live?**  
After Phase 1 review and Politburo approval of Phase 2/3 per [PROVINCIAL-ROLLOUT-PLAN.md](./PROVINCIAL-ROLLOUT-PLAN.md).
