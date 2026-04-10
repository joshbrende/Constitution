[STEP] Build the compliance checklist and tracker — step by step

## Practical: Compliance Checklist and Tracker

**Tools:** **Google Sheets** or **Microsoft Excel**.

This tracker supports **MFMA** and **legislative** compliance (Module 8 theory) and feeds your **compliance** or **governance** dashboard. It should align with the **obligations** your **CFO**, **Treasury** liaison, and **Risk/Legal** recognise. **AI** can help **design** the checklist structure—you **populate** with **your** obligations and **never** put **confidential** Treasury or AG documents into public AI.

### Step 1: Create the workbook and sheets

1. Open **Google Sheets** or **Excel**, new workbook.
2. Create sheets: **MFMA / Treasury**, **Legislative (other)**, and **Cover**.
3. **Cover:** Title "Compliance Checklist and Tracker — [Municipality]", "Data as at [date]", "Updated: [frequency]", "Owner: [e.g. CFO or Risk Manager]".

### Step 2: MFMA / Treasury sheet — structure

In the **MFMA / Treasury** sheet:

- **Row 1:** Headers: `Obligation` | `Source (e.g. MFMA S71)` | `Due date / frequency` | `Jan` | `Feb` | … | `Dec` | `Status` | `Owner` | `Note`.
- **Rows 2–15:** One row per **obligation**, e.g.:
  - *Section 71 — monthly*
  - *Section 72 — mid-year*
  - *Section 72 — year-end / adjustments*
  - *Annual report / AFS*
  - *Other treasury returns* (list as per your context)

- In **Jan–Dec** (or **month 1–12**): **Y** if submitted on time, **N** or **L** (late) if not, **—** if not applicable that month. Or use **date submitted** if you prefer. **Status:** Green (on track), Amber (due soon; not yet), Red (overdue). **Owner:** CFO or delegate. **Note:** e.g. "Treasury query on Oct; resolved."

### Step 3: Legislative (other) sheet — structure

In the **Legislative (other)** sheet:

- **Row 1:** Headers: `Obligation` | `Legislation / source` | `Due date / frequency` | `Last done` | `Next due` | `Status` | `Owner` | `Note`.
- **Rows 2–12:** One row per **obligation**, e.g.:
  - *IDP adoption / review*
  - *SDBIP adoption*
  - *Ward committee reports*
  - *PAIA / POPI* (annual or as required)
  - *LRA / bargaining* (as applicable)
  - *OHS* (as required)
  - *Other* (add per your compliance calendar)

- **Last done**, **Next due:** Dates. **Status:** Green / Amber / Red. **Owner:** e.g. Director Corporate, Legal, HR.

### Step 4: Early-warning column (optional)

- Add a column **Alert** or **Early warning**: e.g. "If due in 5 days and not Y, flag." You can **manually** check or use a **formula** (e.g. `=IF(AND(next_due-TODAY()&lt;=5, last_done=""), "FLAG", "")`). **AI** can help **suggest** trigger rules—you **implement** in **your** sheet.

### Step 5: One-sentence "What needs attention"

At the **bottom** of the **MFMA / Treasury** sheet, add: *"What needs attention?"* e.g. "Section 71 Nov submitted late; Section 72 mid-year due in 2 weeks—CFO to finalise." This practises **narrative** for the MM or council.

[STEP] Example (illustrative)

## Example: Compliance Tracker (Illustrative)

**MFMA / Treasury (extract):**

| Obligation    | Source | Due    | Jan | Feb | … | Status | Owner |
|---------------|--------|--------|-----|-----|---|--------|-------|
| Section 71    | MFMA   | 10th   | Y   | Y   | … | Green  | CFO   |
| Section 72 MY | MFMA   | Jun    | —   | —   | … | Amber  | CFO   |

### Use this

- **In your municipality:** Use the **actual** list from **Treasury** and your **compliance calendar**. **CFO** and **Risk** should **approve** the structure. **Never** paste **confidential** Treasury or AG text into **public AI**—use **only** your **own** obligation names and **aggregated** status (e.g. "8 of 12 on time") if you ask AI to **draft** a status sentence.

---

**Next:** Module 8: Practical — Risk Register and Early Warning — build a risk register with triggers.
