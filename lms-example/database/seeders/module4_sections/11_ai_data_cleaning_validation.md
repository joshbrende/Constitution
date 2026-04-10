[STEP] AI Application 6: Generate data cleaning steps

## AI Application 6: Generate Data Cleaning Steps

**Tool:** [ChatGPT](https://chat.openai.com) or [Claude](https://claude.ai)

### Task: Get a checklist of cleaning steps for a described dataset

1. Open **ChatGPT** or **Claude** in a new chat.
2. Describe a **generic** dataset and its known issues. Example:

   > We have an Excel export of monthly revenue: account number, ward, amount billed, amount paid, date. We know we have: (a) some blank wards, (b) dates in different formats (DD/MM/YYYY and YYYY-MM-DD), (c) some duplicate rows when we run the export twice, (d) ward sometimes as "7" and sometimes "07" and sometimes "Ward 7."

3. Send:

   > For the dataset and issues above, give a step-by-step data cleaning checklist. For each step: (1) what we are fixing, (2) how to do it in Excel or Google Sheets (e.g. formula, filter, Find & Replace), (3) in what order we should do the steps and why. Use a numbered list. Keep it practical for someone who uses Excel regularly.

4. **Use the output:** This is a **how-to** for your team. Test on a **copy** of real data. Do not use it as a substitute for fixing the **source system** (e.g. standardise ward in the billing system). Use it for **one-off** clean-ups or until the source is fixed.

[STEP] AI Application 7: Validation rules for a spreadsheet

## AI Application 7: Validation Rules for a Spreadsheet

**Tool:** [ChatGPT](https://chat.openai.com) or [Claude](https://claude.ai)

### Task: Get validation rules you can implement in Excel or Sheets

1. In a **new chat**, send (edit the fields to match your case):

   > We maintain a KPI tracking spreadsheet. Columns: KPI name, Target, Q1 actual, Q2 actual, Q3 actual, Q4 actual, Data source, Owner. We want to add validation to reduce errors. Give us 5–7 validation rules. For each: (1) what we are validating, (2) how to implement it in Excel or Google Sheets (e.g. Data Validation, conditional formatting, or a check formula in a separate cell), (3) what message or prompt to show when it fails. Be specific and simple.

2. **Use the output:** Implement the rules in your **KPI spreadsheet** or in the template you use for SDBIP tracking. Validation at the point of capture prevents errors from propagating into reports and dashboards.

[STEP] AI Application 8: Automate a simple quality report (concept)

## AI Application 8: Concept for an Automated Quality Report

**Tool:** [ChatGPT](https://chat.openai.com) or [Claude](https://claude.ai)

### Task: Get a structure for a monthly “data quality scorecard”

1. In a **new chat**, send:

   > We are a South African municipality. We want a one-page "data quality scorecard" to run monthly. It should cover 4–5 key datasets (e.g. billing, assets, complaints, SDBIP data). For each dataset we want to track: % complete (no missing required fields), % on time (updated by the due date), and one consistency or validity check. Give us: (1) the structure (table or section layout), (2) how we would get each number (e.g. "count blanks / count total" or "compare to agreed due date"), (3) how we could show Red/Amber/Green. We will do this in Excel or Google Sheets. No coding.

2. **Use the output:** This is a **concept** for a repeatable quality report. Start with one or two datasets. As you add more, you build a culture of **monitoring data quality**—which supports AI and dashboards (Modules 5–12).

---

**Next:** Module 4: Key Takeaways — consolidate what you have learned.
