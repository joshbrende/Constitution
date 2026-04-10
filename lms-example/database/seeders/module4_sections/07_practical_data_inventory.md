[STEP] Build a data inventory — step by step

## Practical: Data Inventory and Quality Checklist

**Tools:** **Google Sheets**, **Excel**, or **Word** (table).

### Step 1: Choose one area

Pick **one** area: e.g. **Revenue/Billing**, **Water and Sanitation assets**, **Complaints**, **IDP/SDBIP and performance**, or **HR (headcount and performance agreements)**. Use one you know or have access to.

### Step 2: Create a Data Inventory table

Create a table with these columns: **Dataset / source** | **Where it lives** | **Owner (role)** | **How often updated** | **Format (e.g. DB, Excel, PDF)** | **Personal data? (Y/N)** | **Notes**.

### Step 3: Fill one area (5–10 rows)

- **Dataset/source:** e.g. “Billing—consumption and payments,” “Asset register—water pipes,” “Complaint log—faults.”
- **Where it lives:** System name, server, or “Finance Excel on shared drive.” Be specific enough that someone could find it.
- **Owner:** The role accountable (e.g. CFO, Director: Technical Services). If unknown, write “TBC.”
- **How often updated:** Daily, monthly, on request, or “when we remember.”
- **Format:** Database, Excel, PDF, paper. This affects whether it can be easily used for dashboards or AI.
- **Personal data?** Y if it contains names, IDs, contact details, account numbers that can identify a person; N if it is aggregated or fully anonymised. Important for POPI and for using in public AI.
- **Notes:** e.g. “Legacy; no API”; “Good for KPIs”; “Inconsistent ward codes.”

[STEP] Add a quality checklist

## Step 4: Add a Data Quality Checklist

On a **second sheet** or **second table**, list the **quality dimensions** and score your top 3–5 datasets (from the inventory) as **Good / Partial / Poor** for each:

| Dataset | Accuracy | Completeness | Timeliness | Consistency | Validity | Uniqueness |
|---------|----------|--------------|------------|-------------|----------|------------|
| Billing—payments | Good | Partial | Good | Partial | Good | Good |
| Asset register—pipes | Partial | Poor | Partial | Poor | Partial | Partial |

**Use this:** The inventory tells you *what exists*; the quality checklist tells you *what to improve first*. Share with the data owner. This is a living document—update as systems or ownership changes.

[STEP] Example (illustrative)

## Example: Extract from a Revenue Data Inventory (Illustrative)

| Dataset / source | Where it lives | Owner | How often updated | Format | Personal data? | Notes |
|------------------|----------------|-------|-------------------|--------|----------------|-------|
| Billing—consumption | Billing system, server X | CFO | Daily | DB | Y | Good for collection KPIs; API exists. |
| Payments received | Billing system | CFO | Daily | DB | Y | Links to consumption by account. |
| Debtors ageing | Excel, Finance shared drive | Revenue Manager | Monthly | Excel | Y | Manual extract from billing; sometimes late. |
| Collection rate (calculated) | SDBIP spreadsheet | PMU | Quarterly | Excel | N | Formula; verify numerator/denominator with Treasury. |

### Use this

- **In your municipality:** Replace with your real sources. Involve the owner to validate. The inventory is the basis for **data–KPI mapping** (next Practical) and for **data cataloging** (AI Application).
- **For the AI Application:** We will use AI to generate a **data catalog template** and **quality assessment prompts** you can tailor to your inventory.

---

**Next:** Module 4: Practical — Data Source–KPI Mapping — map your KPIs to data sources and find gaps.
