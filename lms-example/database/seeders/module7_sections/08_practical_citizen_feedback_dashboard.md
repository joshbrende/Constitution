[STEP] Build the citizen feedback and satisfaction dashboard — step by step

## Practical: Citizen Feedback and Satisfaction Dashboard

**Tools:** **Google Sheets** or **Microsoft Excel**.

This dashboard supports **citizen satisfaction** and **complaints** KPIs (Module 7 theory) and will feed (or align with) your **service delivery** or **customer care** dashboard (Module 5). It should use only **aggregated** data for reporting and for any **AI** tasks—**POPI**: no names, IDs, or contact details in summaries or public AI.

### Step 1: Create the workbook and sheets

1. Open **Google Sheets** or **Excel** (you can add sheets to your Faults tracker workbook or create a new one).
2. Create sheets: **Complaints summary**, **Satisfaction** (if you have survey data), and **Cover**.
3. **Cover:** Title "Citizen Feedback and Satisfaction Dashboard — [Municipality]", "Data as at [date]", "Updated: [frequency]", "Owner: [e.g. Customer Care or Performance Management]".

### Step 2: Complaints summary sheet — structure

In the **Complaints summary** sheet:

- **Row 1:** Headers: `Category / service` | `Complaints (period)` | `Resolved (period)` | `Resolved in X days %` | `Target (days)` | `Status` | `Top themes (short)`.
- **Rows 2–8:** One row per **category** aligned with your complaint system, e.g.:
  - *Water*
  - *Sanitation*
  - *Electricity*
  - *Roads*
  - *Waste*
  - *Billing*
  - *Other / total*

- **Resolved in X days %:** Of those resolved, % within your target (e.g. 7 or 14 days). **Top themes:** 2–3 recurring themes from **anonymised** review (e.g. "No water; slow response; attitude"). You can use **AI** in the AI Application section to **suggest themes** from **anonymised** text—you **validate**. **Status:** Traffic lights.

### Step 3: Satisfaction sheet — structure (if you have survey data)

In the **Satisfaction** sheet:

- **Row 1:** Headers: `Service or question` | `Score (1–5 or %)` | `Target` | `Prior period` | `Status` | `Note`.
- **Rows 2–8:** One row per **service** or **overall** (Water, Sanitation, Electricity, Roads, Waste, Overall, and any other you track). **Score:** Average or % satisfied from your **survey**. **Prior period:** For trend. **Status:** Traffic lights. **Note:** One line (e.g. "Water down from last quarter; link to interruptions").

- If you **do not** have surveys yet, leave this sheet as a **template** and fill "No survey this period" or "Planned for [date]". The **Complaints summary** still gives you useful feedback.

### Step 4: Data sources and refresh

- **Complaints:** From your **complaint** or **call-centre** system. **Refresh:** Weekly or monthly. For **themes**, you can do a **batch** review (e.g. monthly) of **anonymised** samples and update "Top themes" by category.
- **Satisfaction:** From **surveys** (own or partner). **Refresh:** Quarterly or annual is common. **Owner** approves definitions and targets.

### Step 5: One-sentence "What needs attention"

At the **bottom** of the **Complaints summary** sheet (and optionally **Satisfaction**), add: *"What needs attention?"* e.g. "Water complaints up; resolution time for roads below target; satisfaction down in Ward 2." Use **aggregated** wording only—no identifiable detail.

[STEP] Example and POPI reminder

## Example: Citizen Feedback Dashboard (Illustrative)

**Complaints summary (extract):**

| Category | Complaints | Resolved | In 7 days % | Target | Status | Top themes          |
|----------|------------|----------|-------------|--------|--------|---------------------|
| Water    | 95         | 88       | 72          | 80     | Amber  | No water; slow fix. |
| Roads    | 60         | 55       | 85          | 80     | Green  | Potholes; drainage. |

**Satisfaction (extract):**

| Service | Score (1–5) | Target | Prior | Status | Note        |
|---------|-------------|--------|-------|--------|-------------|
| Water   | 3.2         | 3.5    | 3.4   | Red    | Down; link to outages. |
| Overall | 3.5         | 3.5    | 3.4   | Green  | On target.   |

### POPI reminder

- **Complaints and surveys:** For **internal** resolution you may need IDs and contact details—keep them **secure** and **access-controlled**. For **this dashboard**, **reports**, and **AI**: use only **counts**, **categories**, **scores**, and **anonymised** themes. **Never** put **identifiable** data into **public AI** (ChatGPT, Claude).

---

**Next:** Module 7: AI Application — Infrastructure and Predictive Maintenance — use AI to support fault prioritisation and maintenance rules.
