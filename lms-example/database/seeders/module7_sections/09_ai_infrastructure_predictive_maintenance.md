[STEP] AI Application 1: Infrastructure and predictive maintenance — concepts

## AI Application: Infrastructure and Predictive Maintenance

**Tool:** [ChatGPT](https://chat.openai.com) or [Claude](https://claude.ai)

**Important:** Use only **aggregated, non-confidential** data (e.g. "120 water faults this month," "78% resolved in 48h," "10 faults 31+ days in Ward X" — only if Ward cannot identify a person). **Never** put **identifiable** complainant or resident data into public AI. AI cannot **run** your fault system or **receive** live IoT streams. It can: (1) **suggest** prioritisation and **maintenance rules** from **aggregated** fault and ageing patterns you describe; (2) **draft** a structure for a "when to inspect or replace" logic you implement in **your** systems; (3) **generate** a short briefing or "what to watch" from **summary** numbers.

[STEP] Task A — Suggest fault prioritisation rules

### Task A: Suggest Fault Prioritisation Rules

1. Open **ChatGPT** or **Claude** in a new chat.
2. Paste this prompt (edit the [bracketed] parts to match your context):

   > We are a South African municipality. We have faults for water, sanitation, electricity, and roads. We want **prioritisation rules** so we can decide which faults to fix first when we have limited crews. We have: ageing (0–7, 8–30, 31+ days); service type; and we can distinguish critical (e.g. no water to many, dangerous) vs standard. Give 6–8 rules in the form: "IF [condition] THEN [priority 1–4 or High/Medium/Low]". Consider: critical/standard, ageing, and service (e.g. water and sanitation may be health-related). Use a table: Rule | Condition | Priority | Reason.

3. **Use the output:** **Validate** with your **technical** or **service delivery** manager. Adapt to your **SDBIP** targets and **resource** realities. Use it to **label** or **sort** faults in your **Faults and Resolution Tracker** or in your fault system. **AI** suggests; **you** implement.

[STEP] Task B — Draft a simple "when to inspect" logic for assets

### Task B: Draft a Simple "When to Inspect" Logic for Assets

1. In a **new chat**, send:

   > We are a South African municipality. We want a simple **predictive maintenance** logic: when to **schedule an inspection** or **planned maintenance** for infrastructure (water pipes, pumps, substations, roads) based on **data we have**: fault history (number of faults in last 12 months for an asset or area), age of asset (if known), and condition (good/fair/poor if we have it). We will NOT put any asset IDs or locations into AI—we only want the **concept**. Suggest 5–7 rules in the form: "IF [e.g. fault count &gt; X in 12 months OR age &gt; Y years OR condition = poor] THEN [action: e.g. schedule inspection within Z weeks]". Use a table. We will implement this in our own asset or maintenance system with our own data.

2. **Use the output:** This is a **design** for a **rule-based** predictive step. Your **asset** or **maintenance** team can **implement** it in **your** systems. **AI** helps you **think through** the logic; you **own** the thresholds and the data.

[STEP] Task C — One-paragraph "what to watch" from fault summary

### Task C: One-Paragraph "What to Watch" from Fault Summary

1. In a **new chat**, provide **aggregated** numbers only. Example:

   > Our municipality's fault summary this month: Water 120 reported, 105 resolved, 78% within 48h (target 90%); backlog 45 open, 10 older than 31 days. Roads 85 reported, 80 resolved, 88% within target; backlog 12, 2 older than 31 days. Electricity and sanitation on target.

2. Then send:

   > Using only the numbers I provided, write a **one-paragraph "What to watch"** for the Municipal Manager and Technical Director. Include: (1) which service needs attention and why; (2) one recommended priority action; (3) one sentence on what is on track. Use formal, professional language. Do not add information we did not provide. We will verify and approve.

3. **Use the output:** **Edit** for **accuracy**. The **technical** or **service delivery** lead **approves** before it goes to the MM. **AI** helps you **turn numbers into narrative**; you **own** the data and the message.

---

**Next:** Module 7: AI Application — Sentiment Analysis of Citizen Feedback — use AI to categorise and summarise **anonymised** feedback.
