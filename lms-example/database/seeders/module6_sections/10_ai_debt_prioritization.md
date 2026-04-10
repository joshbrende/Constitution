[STEP] AI Application 2: Debt prioritization — concepts

## AI Application: Debt Prioritization

**Tool:** [ChatGPT](https://chat.openai.com) or [Claude](https://claude.ai)

**Important:** Use only **aggregated** data. **Never** put debtor names, account numbers, or IDs into public AI. AI can help you **design** prioritisation rules and **segment** logic; it cannot run on your live debtor list. Implementation happens in **your** systems with **your** data.

[STEP] Task A — Generate a debtor segmentation framework

### Task A: Generate a Debtor Segmentation Framework

1. Open **ChatGPT** or **Claude** in a new chat.
2. Send (edit as needed):

   > We are a South African municipality. We need a **debtor segmentation framework** to prioritise collection. We have: ageing (current, 30, 60, 90+ days); we can group by type (e.g. residential, commercial, government, indigent); and we have total balance. We want to decide: who to contact first, who gets a payment arrangement, who goes to legal, and who we might write off. Create a segmentation matrix: one axis = ageing (e.g. 0–30, 31–60, 61–90, 90+), the other = type or value band (e.g. residential vs commercial vs government; or high/medium/low value). For each cell, suggest: (1) priority (1=highest), (2) recommended action (e.g. reminder, arrangement, legal, assess for write-off), (3) one caution (e.g. "Check indigent status first"). Use a table.

3. **Use the output:** Adapt to your **policy** (indigent, arrangements, disconnection, write-off). The **Revenue Manager** and **CFO** should approve. Use it to **label** the segments in your **Debt** tracker and to brief staff on prioritisation.

[STEP] Task B — Draft a debt prioritisation "score" concept

### Task B: Draft a Debt Prioritisation "Score" Concept

1. In a **new chat**, send:

   > We want a simple **prioritisation score** for debtors so we can rank who to contact first. We will NOT put any personal data into AI—we only want the **concept**. Suggest 4–6 **factors** we could use (e.g. balance, ageing, payment history, type) and for each: (1) the factor, (2) how we might weight it (e.g. 1–5 or %), (3) how higher or lower would affect the score (e.g. older = higher priority). Then give a formula in words, e.g. "Score = (Weight1 × Factor1) + (Weight2 × Factor2) + …" and an example of how two hypothetical segments would compare. We will implement this in our own system with our own data.

2. **Use the output:** This is a **design** for a scoring approach. Your **IT** or **revenue system** vendor can implement it **on your data**, **in your environment**. AI helps you **think through** the factors; you **own** the weights and the implementation.

[STEP] Task C — Checklist for write-off assessment

### Task C: Checklist for Write-Off Assessment

1. In a **new chat**, send:

   > We are a South African municipality. We have a write-off policy for irrecoverable debt. We want a **checklist** that a revenue officer can use when assessing whether to recommend an account (we will NOT put account details in AI) for write-off. The checklist should cover: (1) evidence that the debt is irrecoverable (e.g. prescribed, debtor deceased, company liquidated, cannot be traced); (2) what collection actions were already taken; (3) what approval level is needed (e.g. by amount); (4) any MFMA or municipal requirements we should mention. Use 8–10 short questions with Yes/No or a one-line answer. Format as a simple checklist.

2. **Use the output:** Align with your **written write-off policy**. Add your **approval** thresholds and **delegations**. Use it in your **revenue** procedures. **AI** does not decide write-offs—humans do, using this checklist and policy.

---

**Next:** Module 6: AI Application — Fraud Red Flags and Variance Explanations — use AI to support fraud awareness and variance narrative.
