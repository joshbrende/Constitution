[STEP] AI Application 4: Suggest anomaly rules — what to flag

## AI Application 4: Suggest Anomaly Rules (What to Flag)

**Tool:** [ChatGPT](https://chat.openai.com) or [Claude](https://claude.ai)

**Data and ethics:** Use only **generic** KPI names and **aggregated** or example thresholds; **never** confidential targets or person-specific data. You **validate** rules with the KPI owner or CFO before implementation.

### Task: Get simple “when to flag” rules for your KPIs

1. Open **ChatGPT** or **Claude** in a new chat.
2. Send (edit the KPIs to match yours):

   > We are a South African municipality with a KPI dashboard. Our main KPIs include: (1) Collection rate (target 75%), (2) Debtors 90+ days (we want it to decrease), (3) Revenue vs budget YTD, (4) Faults resolved within 30 days (%). For each, suggest 2–3 simple "anomaly" or "alert" rules: when should we flag this for attention? For example: "Flag when collection rate drops more than 2 percentage points from the previous month" or "Flag when 90+ debtors increase by more than 10% month on month." For each rule: (1) what we are checking, (2) the threshold or condition, (3) who should be notified (role). Use a table. Keep it simple enough to implement in Excel or a basic BI tool.

3. **Use the output:** These are **suggested** rules. Your **CFO** or **data owner** should validate: Do the thresholds match your risk tolerance? Implement the ones you can (e.g. in Excel with conditional formatting or a “Flags” column, or in a BI tool with alerts). Start with 1–2 per KPI; add more as you refine.

[STEP] AI Application 5: Alert logic and escalation

## AI Application 5: Alert Logic and Escalation (Concept)

**Tool:** [ChatGPT](https://chat.openai.com) or [Claude](https://claude.ai)

### Task: Get a simple escalation concept for dashboard alerts

1. In a **new chat**, send:

   > We have a municipal KPI dashboard. When a KPI goes Red (off target) or when we detect an anomaly (e.g. big drop in collection, or 90+ debtors up 15% in a month), we want to trigger an alert. Suggest a simple escalation: (1) who gets the first alert (role), (2) what they should do (e.g. "Review and report to Director within 2 working days"), (3) when it should escalate to the next level (e.g. "If not resolved or explained within 5 days, escalate to MM office"). Use 2 levels only. Keep it practical for a municipality with limited systems (we may do this by email or a shared report at first).

2. **Use the output:** This is a **concept** for governance. Adapt to your structure (who is “Director,” who is “MM office”). Document it in your **dashboard specification** or in a separate “Dashboard and alert governance” one-pager. It helps avoid alerts that no one acts on.

[STEP] AI Application 6: Predictive “leading” indicators

## AI Application 6: Suggest Predictive (Leading) Indicators

**Tool:** [ChatGPT](https://chat.openai.com) or [Claude](https://claude.ai)

### Task: Get ideas for leading indicators that predict a lagging KPI

1. In a **new chat**, send (edit the lagging KPI):

   > Our municipality tracks "Collection rate" as a key lagging KPI. What 3–5 **leading** indicators could we add to the dashboard to get early warning that collection might drop? For each: (1) the leading indicator, (2) how we would measure it, (3) why it tends to predict collection rate, (4) what data source we might need. Use a table. We may not have all the data yet—that is OK; we want to know what to aim for.

2. **Use the output:** Use this to **extend your dashboard** or your **data–KPI mapping**. Where you already have the data, add the leading indicator. Where you do not, note it as a **gap** to work on with the data owner (Module 4).

---

**Next:** Module 5: AI Application — Natural Language Generation for Insights — use AI to turn KPI numbers into short narrative insights.
