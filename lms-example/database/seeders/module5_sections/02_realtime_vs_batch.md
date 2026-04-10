[STEP] Real-time vs batch — definitions

## Real-Time vs Batch Reporting

**Batch reporting** means data is collected, processed, and loaded into a report or dashboard on a **schedule**: daily, weekly, monthly, or quarterly. **Real-time** (or near real-time) means data is updated continuously or within minutes so the dashboard reflects the current state. The choice affects cost, complexity, and how quickly you can act.

| | **Batch** | **Real-time (or near real-time)** |
|---|-----------|-----------------------------------|
| **Update frequency** | Daily, weekly, monthly, quarterly | Minutes to hours; or on-demand refresh |
| **Examples** | Monthly SDBIP extract; quarterly collection rate; annual report | Live billing payments; fault tickets; some revenue or complaint totals |
| **Data flow** | Extract from source → transform → load into report/dashboard on a schedule | Continuous or frequent sync; or direct query to source |
| **Typical tools** | Excel, scheduled ETL, BI tools with nightly refresh | BI tools with live connections; APIs; operational systems with built-in dashboards |

[STEP] When to use each — municipal examples

## When to Use Each: Municipal Examples

| Use case | Often **batch** (why) | Or **real-time** (when it adds value) |
|----------|------------------------|----------------------------------------|
| **SDBIP / performance KPIs** | Monthly or quarterly is enough for council and oversight; data is often finalised after month-end. | Rarely needed; exception: operational “war rooms” during critical periods. |
| **Revenue and collection** | Monthly billed/collected is standard for reporting. | Daily or weekly **can** help revenue teams prioritise collection and spot drops early. |
| **Complaints and faults** | Batch for trend reports. | Near real-time for **operational** dashboards: open tickets, ageing, resolution rate. |
| **Compliance (e.g. MFMA submissions)** | Batch: submissions have due dates; you report after the fact. | Real-time only to show “submitted / not yet” for internal tracking. |
| **Council and committee** | Batch: reports are prepared in advance. | Not typically. |

**Rule of thumb:** Use **batch** when the decision can wait for the next cycle (e.g. monthly SDBIP, quarterly reviews). Use **real-time or near real-time** when acting *today* or *this week* changes the outcome (e.g. fault backlogs, daily collection focus, open compliance items).

[STEP] Trade-offs

## Trade-offs: Cost, Complexity, and Trust

| | **Batch** | **Real-time** |
|---|-----------|---------------|
| **Cost and complexity** | Lower: scheduled jobs, simpler pipelines. Excel or a BI tool with nightly refresh is often enough. | Higher: stable connections, more robust ETL or APIs, monitoring. |
| **Data quality** | Easier to **validate** before load: you can run checks on the extract. | Harder: bad data can appear quickly; need validation in the pipeline or at source. |
| **Governance** | Clear “as at” date: “SDBIP as at 30 September.” | Need to state “as at [time]” and have rules for when real-time is suitable for formal reporting. |
| **Audit and SALGA** | Most formal reporting (annual report, SDBIP, CoGTA) is **batch**. Real-time is for **management** use. | |

For most municipal **KPI dashboards** that support SALGA outcomes and the golden thread, **batch** (daily, weekly, or monthly) is the right starting point. Add real-time only where the benefit clearly outweighs the cost and where you have the data and systems to support it.

---

**Next:** Module 5: Dashboard Design Principles — purpose, audience, and link to your performance framework.
