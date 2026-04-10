[STEP] Build the risk register and early-warning triggers — step by step

## Practical: Risk Register and Early Warning

**Tools:** **Google Sheets** or **Microsoft Excel**.

This builds on **risk** and **early warning** theory (Module 8). It gives you a **risk register** and a simple **early-warning** view. **AI** can help **suggest** risk categories, **scoring** rules, and **trigger** ideas—you **populate** with **your** risks and **never** put **confidential** or **sensitive** risk detail into public AI.

### Step 1: Risk register sheet — structure

1. New sheet **Risk register**.
2. **Row 1:** Headers: `ID` | `Risk description` | `Category` | `Likelihood (1-5)` | `Impact (1-5)` | `Score` | `Owner` | `Treatment` | `Status` | `Next review` | `Early-warning trigger (short)`.
3. **Rows 2–20:** One row per **risk**. **Score** = Likelihood × Impact (or use your matrix). **Treatment:** One line (e.g. "Monthly revenue review; escalate if &lt;95%."). **Status:** e.g. Mitigating, Monitored, Accepted. **Next review:** Date. **Early-warning trigger:** One line (e.g. "Collection rate &lt;70%; or Section 71 not submitted 2 days before due").

### Step 2: Early-warning summary sheet (optional)

- **Sheet: Early warning**. Columns: `Trigger` | `Source (risk ID or KPI)` | `Threshold / rule` | `When to alert` | `Escalate to` | `Last checked` | `Status`.
- **Rows:** One per **trigger** you can **operationalise** (e.g. "Section 71 due in 5 days and not submitted" → Alert CFO; "Collection rate &lt;72%" → Alert CFO and MM). **Last checked:** Date of last look. **Status:** Green (OK), Amber (watch), Red (triggered; action taken).

### Step 3: Link to your other trackers

- **Financial** (Module 6): Collection rate, 90+ debt, variance—these can **feed** **risk** and **triggers**.
- **Service** (Module 7): Fault backlog, satisfaction—same.
- **Compliance** (this module): Section 71, other due dates—**direct** trigger source.

**You** can **manually** check **thresholds** from your **Revenue**, **Faults**, and **Compliance** trackers; or **later** use **formulas** or **BI** to **auto-flag**. **Start simple.**

### Step 4: One-sentence "What needs attention"

At the **bottom** of the **Risk register** or **Early warning** sheet: *"What needs attention?"* e.g. "R3 (revenue shortfall): collection at 71%; R7 (Section 71): Feb submission due in 3 days. CFO and MM notified."

[STEP] Example (illustrative)

## Example: Risk Register (Illustrative)

| ID | Risk              | Cat   | L | I | Score | Owner | Treatment           | Trigger (short)        |
|----|-------------------|-------|---|---|-------|-------|---------------------|------------------------|
| R1 | Revenue shortfall | Fin   | 4 | 4 | 16    | CFO   | Monthly review      | Collection &lt;72%     |
| R2 | Section 71 late   | Comp  | 3 | 3 | 9     | CFO   | Pre-due checklist   | Not submitted 2d before|
| R3 | Water backlog     | Oper  | 3 | 4 | 12    | Tech  | Weekly fault review | 31+ &gt;15             |

### Use this

- **In your municipality:** Use **your** **risk register** from **Risk** or **Internal Audit** if one exists; **align** with **council** or **audit committee** format. **Do not** put **confidential** legal or **person-specific** risks into **public AI**. **AI** can help **suggest** **categories**, **scoring**, and **trigger** **wording**—you **own** the final content.

---

**Next:** Module 8: AI Application — Automated Compliance Checking — use AI to design checklists and compliance rules.
