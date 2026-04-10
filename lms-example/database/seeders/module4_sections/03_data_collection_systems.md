[STEP] Where municipal data comes from

## Data Collection Methods and Systems

Municipalities produce and collect vast amounts of data. The **source** and **method** of collection affect quality, timeliness, and how easily it can be used for AI and dashboards.

### Common Municipal Data Sources

| Domain | Examples of data | Typical systems or methods |
|--------|------------------|----------------------------|
| **Revenue and billing** | Consumption, billing, payments, debtors, collection rate | Billing system (e.g. municipal or outsourced); spreadsheets for some smaller municipalities. |
| **Assets** | Infrastructure (water, sewer, roads, electricity); condition; location | Asset registers; GIS; CMMS (maintenance); sometimes paper or Excel. |
| **Complaints and requests** | Faults, service requests, customer feedback | Call centre, CRM, logging systems; sometimes spreadsheets or paper. |
| **Planning and performance** | IDP, SDBIP, APP, budget, quarterly reports | Word/PDF; sometimes spreadsheets for KPIs; performance or BI tools in larger municipalities. |
| **HR** | Headcount, leave, performance agreements, training | HR/payroll system; sometimes manual returns. |
| **Projects** | Capital projects; milestones; expenditure | Project registers; PM systems; Excel; SAP or similar in metros. |
| **Council and governance** | Resolutions, agendas, committee reports | Document management; often PDF. |

[STEP] Manual vs automated collection

## Manual vs Automated Collection

| | **Manual** | **Automated** |
|---|------------|---------------|
| **Examples** | Paper forms; Excel data capture; typing from one system into another. | Meters (AMR); sensors; system-to-system feeds; online forms into a database. |
| **Pros** | Flexible; can work where systems are weak. | Faster; fewer transcription errors; more timely. |
| **Cons** | Error-prone; slow; hard to integrate. | Depends on systems being correct and available. |

**For AI and dashboards:** Automated, structured data is easier to use. The goal is not to remove all manual steps at once, but to **identify** which data is manual, which is automated, and where improving collection would have the highest impact (e.g. KPIs that feed SALGA reports or audit).

[STEP] Data collection and POPI

## Data Collection and POPI

When data includes **personal information** (names, IDs, contact details, account numbers), collection and use must comply with **POPI**:

- **Purpose:** Collect only for a defined, legitimate purpose (e.g. billing, service delivery, employment).
- **Consent or justification:** Where required, have a lawful basis (consent, contract, legal obligation, etc.).
- **Minimisation:** Do not collect more than you need.
- **Security and retention:** Protect data; do not keep it longer than necessary.

For **AI**: Sending person-level data to **public** tools (e.g. ChatGPT, Claude) is generally not advisable. Use **anonymised, aggregated, or synthetic** data for prompts, or use in-house/approved AI with proper agreements. Module 2 covers this in more detail.

---

**Next:** Module 4: Data Integration Challenges — silos, formats, legacy systems, and how to think about integration.
