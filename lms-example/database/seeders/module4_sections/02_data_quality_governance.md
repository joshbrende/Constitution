[STEP] What is data quality?

## Data Quality and Governance

**Data quality** means data is **fit for purpose**: accurate, complete, timely, consistent, and usable. When you feed poor-quality data into a dashboard or an AI model, you get poor-quality results. “Garbage in, garbage out” applies to AI as much as to spreadsheets. For performance management, poor data means wrong KPIs, misleading reports, and audit findings.

### Dimensions of Data Quality

| Dimension | What it means | Example (municipal) |
|-----------|---------------|---------------------|
| **Accuracy** | Data reflects reality. | Billing system: customer address and consumption are correct. |
| **Completeness** | No missing values where they are required. | Asset register: all pipes in a zone are recorded; no “blank” critical fields. |
| **Timeliness** | Data is up to date for the use you need. | Monthly revenue: available within 5 working days of month-end. |
| **Consistency** | Same thing is represented the same way across systems. | “Water and Sanitation” vs “W&S” vs “Technical Services—Water”: one standard name. |
| **Validity** | Values fall within expected rules (format, range). | Dates in YYYY-MM-DD; percentages 0–100; no negative consumption. |
| **Uniqueness** | No unintended duplicates. | One record per meter; one record per complaint reference. |

In practice, you will often have to **prioritise**: improving completeness for revenue data may matter more than consistency of department names. The **Practical** section helps you build a checklist for your context.

[STEP] Why data quality matters for AI

## Why Data Quality Matters for AI

- **AI learns from data.** If training data or input data is biased, wrong, or incomplete, AI outputs will be too. A model trained on incomplete billing data will make poor collection predictions.
- **Generative AI and RAG.** When you use ChatGPT or Claude with your documents (e.g. IDP, SDBIP), the quality of the text and structure affects the quality of summaries and extractions. Messy, outdated, or inconsistent documents produce weaker results.
- **Automation amplifies errors.** If you automate KPI calculation from a database, a systematic error (e.g. wrong formula, duplicate records) is repeated every time. Fix the data and the rules first.
- **Accountability and audit.** CoGTA, the Auditor-General, and SALGA expect performance reports to be based on verifiable data. Poor data undermines trust and can lead to qualified audits.

[STEP] Data governance — roles and ownership

## Data Governance: Who Owns and Stewards Data?

**Data governance** is the set of roles, rules, and processes that ensure data is managed properly: who may access it, who is responsible for its quality, how it is protected (POPI), and how it is shared across the organisation.

| Role / concept | What it means in a municipality |
|----------------|---------------------------------|
| **Data owner** | The business owner (e.g. CFO for financial data; Director: Technical Services for water assets). Accountable for quality and use. |
| **Data steward** | Often a designated person in the department who ensures data is captured, cleaned, and used correctly. May liaise with IT. |
| **IT / systems** | Responsible for storing, securing, and making data available. Does not own the *meaning* or *quality* of the data—the business does. |
| **POPI and PAIA** | Protection of Personal Information Act: personal data must be collected and used lawfully; access and retention must be justified. PAIA governs access to information. |

### Governance in practice

- **Assign owners** for critical datasets: billing, assets, HR, IDP/SDBIP, complaints. Without an owner, quality drifts.
- **Define clear rules** for who may use data for AI or analytics. POPI and your own policies may restrict use of person-level data in external AI tools (see Module 2).
- **Document** what exists: a simple **data inventory** (Practical section) is a first step to governance.

---

**Next:** Module 4: Data Collection Methods and Systems — where municipal data comes from.
