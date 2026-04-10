[STEP] AI Application 1: Assess data quality from a description

## AI Application 1: Data Quality Assessment from a Description

**Tool:** [ChatGPT](https://chat.openai.com) or [Claude](https://claude.ai)

### Task: Get a structured quality assessment of a dataset you describe

1. Open **ChatGPT** or **Claude** in a new chat.
2. Describe a **non-confidential** dataset (no real names, IDs, or sensitive figures). Example:

   > We have a municipal dataset: monthly billing and payment records. Fields include: account number, area/ward, amount billed, amount paid, date of payment, arrears balance. The data comes from our billing system; Finance extracts it monthly into Excel. We sometimes have missing ward codes and duplicate rows when a payment is split. Arrears is recalculated and sometimes does not match the sum of unpaid bills.

3. Send this prompt:

   > For the dataset described above: (a) list the main data quality risks (e.g. completeness, accuracy, consistency, uniqueness) and for each give one specific example from the description; (b) suggest 3–5 simple checks or rules we could run to detect these issues (e.g. "flag rows with blank ward"; "flag duplicate account+month"); (c) suggest who should own fixing each type of issue (role, not name). Use a table for (a) and (b); short bullets for (c).

4. **Use the output:** This is a **draft** quality assessment. Share with your data owner or IT. The checks can be turned into Excel formulas, SQL, or validation rules in a system. **Do not paste real data** with personal information into public AI.

[STEP] AI Application 2: Generate data quality rules for a KPI

## AI Application 2: Generate Data Quality Rules for a KPI

**Tool:** [ChatGPT](https://chat.openai.com) or [Claude](https://claude.ai)

### Task: Get validation rules for the data that feeds a KPI

1. In a **new chat**, send (edit the KPI to match yours):

   > We have a KPI: "Collection rate = cash collected / amount billed × 100, for the financial year." The data comes from our billing system: we have a table of billed amounts by month and a table of payments by month, both by account. What are 5–7 data quality checks we should run before we calculate this KPI? For each check: (1) what we are checking, (2) how to do it (e.g. formula, query, or manual step), (3) what to do if it fails (e.g. "exclude from numerator until fixed" or "flag for Finance"). Use a table.

2. **Use the output:** These are **suggested** rules. Your CFO or Revenue Manager should validate: Do the definitions match Treasury or your policy? Adopt or adapt. This helps ensure your collection rate is defensible for audits and SALGA.

---

**Next:** Module 4: AI Application — Data Cataloging and Discovery — use AI to create catalog templates and discovery prompts.
