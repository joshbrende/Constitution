[STEP] Build the revenue and debt tracker — step by step

## Practical: Revenue and Debt Tracker

**Tools:** **Google Sheets** or **Microsoft Excel**.

This tracker supports your **revenue** and **debt** KPIs (Module 6 theory) and will feed (or align with) your **financial dashboard** (Module 5). It should match the **data sources** in your **data–KPI mapping** (Module 4).

### Step 1: Create the workbook and sheets

1. Open **Google Sheets** or **Excel**, new workbook.
2. Name the first sheet (tab) **Revenue** and the second **Debt**.
3. Add a **Cover** or **Summary** sheet: title "Revenue & Debt Tracker — [Municipality]", "Data as at [date]", "Updated: [frequency, e.g. monthly]".

### Step 2: Revenue sheet — structure

In the **Revenue** sheet:

- **Row 1:** Headers: `Indicator` | `Current month` | `YTD` | `Budget / Target` | `Variance %` | `Status` | `Note`.
- **Rows 2–8 (or as needed):** Add indicators, for example:
  - *Total billed (R)*
  - *Total collected (R)*
  - *Collection rate (%)* — [Collected ÷ Billed] × 100
  - *Collection period (days)* — from your billing/ERP or: [Debtors ÷ (Billed ÷ 30)] if simplified
  - *Cost to collect (% of revenue)* — if you have it
  - *Revenue vs budget YTD (%)*
  - *Main revenue sources — brief note (rates, water, electricity, etc.)*

- In **Status**, use **traffic lights** (Green / Amber / Red) with rules you define (e.g. Collection rate: Green ≥85%, Amber 75–84%, Red &lt;75%). Use **conditional formatting** if you like.
- In **Note**, add one line on cause or action (e.g. "Below target; 90+ debtors up").

### Step 3: Debt sheet — structure

In the **Debt** sheet:

- **Row 1:** Headers: `Indicator` | `Amount (R)` | `% of total` | `Target / Benchmark` | `Status` | `Action / Note`.
- **Rows 2–10:** Add indicators, for example:
  - *Total debtors (R)*
  - *Current (0–30 days)*
  - *31–60 days*
  - *61–90 days*
  - *90+ days*
  - *Debt 90+ as % of total*
  - *Irrecoverable (estimate R or %)* — per your policy
  - *Top 3 debtor categories (e.g. residential, commercial, government)* — short text
  - *Collection actions this quarter (e.g. arrangements, legal, write-offs)* — short text

- **Status:** Traffic lights (e.g. 90+ % of total: Green &lt;25%, Amber 25–35%, Red &gt;35%). **Action / Note:** What is being done (e.g. "Focus on top 100 commercial").

[STEP] Link to data sources and refresh

### Step 4: Link to data sources and refresh

- In **Indicator** or a separate **Source** column, write where each number comes from (e.g. "Billing system — aged debtors", "CFO monthly", "Budget report").
- **Refresh:** Decide how often you update (e.g. monthly after month-end close). Put that on the Cover sheet.
- **Owner:** Assign an owner (e.g. Revenue Manager or CFO) for the tracker. They sign off on definitions and rules.

### Step 5: One-sentence "What needs attention"

At the **bottom** of the **Revenue** sheet and/or **Debt** sheet, add one sentence: *"What needs attention this month?"* e.g. "Collection rate below target; 90+ debt up; prioritising top 50 commercial accounts." This practises turning numbers into a short narrative (you will use **AI for NLG** in the AI Application section).

[STEP] Example structure (illustrative)

## Example: Revenue and Debt Tracker (Illustrative)

**Revenue (extract):**

| Indicator | Current | YTD | Target | Variance % | Status | Note |
|-----------|---------|-----|--------|------------|--------|------|
| Collection rate (%) | 71.2 | 72.1 | 75 | –3.8 | Red | 90+ ageing up. |
| Collection period (days) | 218 | 213 | 90 | adverse | Red | Focus on 60–90 segment. |

**Debt (extract):**

| Indicator | Amount (R 000) | % of total | Target | Status | Action |
|-----------|----------------|------------|--------|--------|--------|
| Total debtors | 125 000 | 100 | — | — | — |
| 90+ days | 52 000 | 41.6 | &lt;25% | Red | Top 100 list to legal. |

### Use this

- **In your municipality:** Populate with **real** data from your billing and budget reports. Align indicator names and definitions with your **SDBIP** and **Section 71** structure. Get the **CFO or Revenue Manager** to approve the traffic-light rules.
- **For the AI Application:** This tracker gives you **aggregated** numbers you can safely use in prompts (e.g. "Collection rate 72%; 90+ is 42% of debt. Suggest three prioritisation rules."). **Never** put debtor names or account-level data into public AI.

---

**Next:** Module 6: Practical — Budget Variance Worksheet — structure and analysis.
