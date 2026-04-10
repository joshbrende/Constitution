[STEP] Why a dashboard specification?

## Practical: Dashboard Specification and Wireframe

**Tools:** **Word**, **Google Docs**, or **Excel** (table).

A **dashboard specification** is a short document that defines **what** the dashboard is for, **who** it is for, **which KPIs**, **where the data comes from**, **how often** it is updated, and **who owns** it. IT, a BI developer, or you (when you hand over to a colleague) can use it to build or maintain the dashboard without guessing.

### Step 1: One-page spec table

Create a table or section with these elements:

| Element | What to write |
|---------|---------------|
| **Dashboard name** | e.g. “Executive SDBIP Overview” or “Revenue KPI Dashboard.” |
| **Purpose** | One sentence: e.g. “To give the MM and mayoral committee an at-a-glance view of top SDBIP KPIs and where action is needed.” |
| **Primary audience** | e.g. MM, mayoral committee, council; or Director: Finance, revenue team. |
| **KPIs (list)** | 4–8 KPIs by name, with a one-line definition and the **data source** (from your data–KPI mapping). |
| **Traffic-light rules** | For each KPI or as a general rule: Green / Amber / Red thresholds. |
| **Refresh frequency** | e.g. Monthly, every Monday, daily. |
| **Data source and pipeline** | e.g. “Billing system extract, monthly, by Finance; loaded into this Sheet by [date].” |
| **Owner** | Role: e.g. “CFO” or “Performance Management Unit.” Who is responsible for accuracy, design changes, and answering questions. |
| **“As at” and disclosure** | How to show “Data as at [date]” and any caveats (e.g. “Preliminary; final after month-end close”). |

[STEP] Simple wireframe — layout in words or a sketch

### Step 2: Simple Wireframe (Layout in Words or a Sketch)

Describe or sketch the **layout**:

- **Top:** Title, “Data as at,” refresh note.
- **Block 1 (left or full width):** KPI 1 — big number, target, traffic light.
- **Block 2:** KPI 2 — same.
- **Block 3–4 (or 5–6):** Remaining KPIs in a grid.
- **Middle or bottom:** One chart (describe: “Line chart, collection rate by month, last 12 months”).
- **Bottom:** One line: “What needs attention” (narrative).

You can draw this on paper or in a slide: **boxes with labels**. The goal is to agree the **structure** before building. For a BI tool, the developer will translate this into the actual layout.

[STEP] Use the spec

### Step 3: Use the Spec

- **Handover:** When you or a colleague (or IT) builds the dashboard in Power BI, Tableau, or another tool, the spec is the **brief**. No spec often leads to mismatch and rework.
- **Governance:** The spec states the **owner** and **data source**. That supports audits and avoids “orphan” dashboards no one maintains.
- **Change control:** When you add a KPI or change a rule, **update the spec** and get the owner’s sign-off.

---

**Next:** Module 5: AI Application — Dashboard Design and KPI Selection — use ChatGPT or Claude to generate specs and suggest KPIs.
