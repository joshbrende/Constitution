[STEP] Map KPIs to data sources

## Practical: Data Source–KPI Mapping

**Tools:** **Google Sheets** or **Excel**. You will need your **KPI list** or **IDP–SDBIP linkage table** from Module 3 (or a subset).

### Step 1: List 5–10 of your KPIs

From your SDBIP, Logic Model, or linkage table, list 5–10 KPIs you are responsible for or care about. For each, write: **KPI name** | **Definition (what we measure)** | **Numerator / denominator (if ratio)** | **Current data source** | **Owner of the KPI**.

### Step 2: For each KPI, identify the data source(s)

- **Current data source:** Where does the number actually come from? (System, report, manual count, “we don’t have it.”)
- If it comes from **more than one** place (e.g. numerator from one system, denominator from another), note both. That is where integration and consistency matter.

### Step 3: Add a “Gap” column

For each KPI, score the **data** that feeds it:

- **Green:** Data exists, is timely, and quality is acceptable. We can report and use for AI/dashboards.
- **Amber:** Data exists but has issues: late, incomplete, or requires heavy manual work. We can report but with effort or risk.
- **Red:** Data is missing, wrong, or we do not know where it comes from. We cannot reliably report or automate.

### Step 4: Prioritise improvements

- **Reds** are the first priority: either find or create the data, or revise the KPI if it is not feasible.
- **Ambers:** Plan with the data owner to improve timeliness, completeness, or automation.
- **Greens:** Protect them. Document the source and the definition so they do not slip.

[STEP] Example mapping (illustrative)

## Example: Data Source–KPI Mapping (Illustrative)

| KPI | Definition | Data source(s) | Gap |
|-----|------------|----------------|-----|
| Collection rate | Cash collected / billed × 100, for the financial year | Billing system (both); formula in SDBIP spreadsheet | Amber: Billed amount sometimes revised late; need to lock definition. |
| New water connections | Number of new household connections in the year | Asset/connections register | Red: Register incomplete; many connections not yet captured. |
| Complaints resolved within 30 days | % of faults resolved within 30 days of logging | Complaint/fault system | Amber: Resolution date not always captured; manual check needed. |
| Section 56 performance agreements signed | % signed by 30 June | HR system | Green: HR captures; report available. |

[STEP] Golden thread and data

## Link Back to the Golden Thread

Your **IDP → SDBIP → Budget → scorecards** linkage (Module 3) defines *what* to measure. This mapping defines *whether you can* measure it with current data. When you report to council, CoGTA, or SALGA, you need to be able to say where each number comes from. The mapping supports **audit readiness** and **AI readiness**: you know which data is good enough to feed dashboards (Module 5) and analytics (Modules 6–10).

---

**Next:** Module 4: AI Application — Data Quality Assessment — use ChatGPT or Claude to assess data quality and generate quality rules.
