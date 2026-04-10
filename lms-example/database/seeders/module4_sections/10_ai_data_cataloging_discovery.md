[STEP] AI Application 3: Generate a data catalog template

## AI Application 3: Data Catalog Template

**Tool:** [ChatGPT](https://chat.openai.com) or [Claude](https://claude.ai)

### Task: Get a template for a data catalog or inventory

1. Open **ChatGPT** or **Claude** in a new chat.
2. Send:

   > We are a South African municipality. We want to build a simple data catalog (inventory) of our main datasets so that we can improve data governance and prepare for AI and dashboards. Give us: (1) a list of 10–12 column headers for the catalog, (2) a one-line description of what goes in each column, and (3) one example row filled with [generic/illustrative] data for a "billing and payments" dataset. Use a table. Keep it simple so we can use it in Excel or Google Sheets.

3. **Use the output:** Compare to the **Data Inventory** you built in the **Practical** section. Merge any useful columns (e.g. “POPI classification,” “Update frequency,” “API or export available”). Use the template to expand your inventory to more areas.

[STEP] AI Application 4: Discover what data you need for a use case

## AI Application 4: Discover What Data You Need for a Use Case

**Tool:** [ChatGPT](https://chat.openai.com) or [Claude](https://claude.ai)

### Task: Get a list of data needed for a specific AI or reporting use case

1. In a **new chat**, send (edit the use case to match what you plan):

   > We are a South African municipality. We want to [e.g. build a dashboard for revenue collection KPIs / predict which accounts are likely to default / analyse citizen feedback themes and sentiment / track SDBIP progress automatically]. List 8–12 types of data or documents we would need. For each: (1) what it is, (2) why we need it, (3) who typically owns it in a municipality, (4) one data quality or governance consideration (e.g. "must be updated monthly," "contains personal data—POPI applies"). Use a table.

2. **Use the output:** This is a **discovery** list. Check against your **Data Inventory** and **Data–KPI Mapping**: What do we have? What is missing? What do we need to improve? Use it to prioritise data readiness for your chosen use case.

[STEP] AI Application 5: Data dictionary for one dataset

## AI Application 5: Data Dictionary for One Dataset

**Tool:** [ChatGPT](https://chat.openai.com) or [Claude](https://claude.ai)

### Task: Generate a simple data dictionary (field definitions) for a described dataset

1. In a **new chat**, describe a **generic** dataset (no real data). Example:

   > We have a "complaints and faults" dataset. Fields we use: reference number, date logged, ward, category (e.g. water, roads, electricity), description, status (open, in progress, resolved), date resolved, responsible department.

2. Send:

   > For the dataset above, create a short data dictionary. For each field: (1) name, (2) description (what it means), (3) example values or format, (4) whether it is required (can it be blank?), (5) one common quality issue to watch (e.g. "status not always updated when resolved"). Use a table.

3. **Use the output:** A data dictionary helps **consistency** and **onboarding**. Share with the data owner and those who capture or use the data. Update when the system or definitions change.

---

**Next:** Module 4: AI Application — Data Cleaning and Validation — use AI to generate cleaning steps and validation rules.
