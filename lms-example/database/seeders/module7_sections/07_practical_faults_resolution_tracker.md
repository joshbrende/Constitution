[STEP] Build the faults and resolution tracker — step by step

## Practical: Faults and Resolution Tracker

**Tools:** **Google Sheets** or **Microsoft Excel**.

This tracker supports **infrastructure and fault** KPIs (Module 7 theory) and will feed (or align with) your **service delivery dashboard** (Module 5). It should match the **data sources** in your **data–KPI mapping** (Module 4). If you have a **fault or call-centre system**, this can be a **summary** view; if not, it can be a **manual** or **semi-manual** tracker until you automate.

### Step 1: Create the workbook and sheets

1. Open **Google Sheets** or **Excel**, new workbook.
2. Create sheets (tabs): **Faults summary**, **Backlog ageing**, and **Cover**.
3. **Cover:** Title "Faults and Resolution Tracker — [Municipality]", "Data as at [date]", "Updated: [frequency, e.g. weekly]", "Owner: [e.g. Director: Technical or Service Delivery]".

### Step 2: Faults summary sheet — structure

In the **Faults summary** sheet:

- **Row 1:** Headers: `Service / type` | `Faults reported (period)` | `Faults resolved (period)` | `Resolved within target %` | `Target (e.g. 48h)` | `Status` | `Note`.
- **Rows 2–7:** One row per **service or type**, e.g.:
  - *Water ( leaks, no water, quality )*
  - *Sanitation ( blockages, overflows )*
  - *Electricity ( faults, outages )*
  - *Roads ( potholes, damage )*
  - *Other*
  - *Total*

- **Resolved within target %:** Of those resolved, how many were within your target (e.g. 48 hours)? [Resolved in time ÷ Resolved] × 100. **Target** column: your SDBIP or policy target (hours or days). **Status:** Traffic lights (e.g. Green ≥90%, Amber 75–89%, Red &lt;75%). **Note:** One line on cause or action (e.g. "Water: backlogs in Ward 3; extra crew assigned").

### Step 3: Backlog ageing sheet — structure

In the **Backlog ageing** sheet:

- **Row 1:** Headers: `Service` | `Open (total)` | `0–7 days` | `8–30 days` | `31+ days` | `Oldest (days)` | `Status` | `Action`.
- **Rows 2–6:** One row per **service** (Water, Sanitation, Electricity, Roads, Other) and **Total**.
- **Open (total):** Number of faults not yet resolved. **0–7, 8–30, 31+:** How many in each ageing bucket. **Oldest:** Age of the oldest open fault in days. **Status:** Traffic lights (e.g. Red if any 31+ or if total open &gt; X). **Action:** One line (e.g. "Prioritise 31+ water; escalate to technical manager").

### Step 4: Data sources and refresh

- In a **Source** column or a separate note, write where each number comes from (e.g. "Fault logging system", "Call centre report", "Manual count from work orders").
- **Refresh:** Weekly is typical for operational use; monthly for SDBIP-style reporting. **Owner** signs off on definitions (e.g. what counts as "resolved") and target rules.

### Step 5: One-sentence "What needs attention"

At the **bottom** of the **Faults summary** sheet, add: *"What needs attention this period?"* e.g. "Water resolution below target; roads backlog 31+ up; focus on Ward 3 and 5." This practises turning numbers into a short narrative (you will use **AI for NLG** in the AI Application section).

[STEP] Example structure (illustrative)

## Example: Faults and Resolution Tracker (Illustrative)

**Faults summary (extract):**

| Service    | Reported | Resolved | Resolved within 48h % | Target | Status | Note                    |
|------------|----------|----------|------------------------|--------|--------|-------------------------|
| Water      | 120      | 105      | 78                     | 90%    | Red    | Backlog in Ward 3.      |
| Roads      | 85       | 80       | 88                     | 85%    | Green  | On track.               |

**Backlog ageing (extract):**

| Service | Open | 0–7 | 8–30 | 31+ | Oldest | Status | Action              |
|---------|------|-----|------|-----|--------|--------|---------------------|
| Water   | 45   | 20  | 15   | 10  | 52     | Red    | Escalate 31+; Ward 3. |

### Use this

- **In your municipality:** Populate with **real** data from your fault or work-order system. Align **service types** and **targets** with your **SDBIP**. Get the **technical or service delivery** lead to approve the traffic-light rules.
- **For AI Application:** This tracker gives you **aggregated** numbers you can safely use in prompts (e.g. "Water resolution 78% vs 90% target; 10 faults 31+ days. Suggest three prioritisation rules."). **Never** put **identifiable** complainant or location details into public AI if it could identify a person—use **aggregated** counts and **types** only.

---

**Next:** Module 7: Practical — Citizen Feedback and Satisfaction Dashboard — build a dashboard for complaints and satisfaction.
