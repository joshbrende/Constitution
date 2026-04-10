[STEP] Silos and fragmented data

## Data Integration Challenges

Even when individual systems have reasonable data, **combining** them for performance reports, dashboards, or AI is often difficult. Understanding these challenges helps you set realistic expectations and prioritise integration efforts.

### 1. Data Silos

**Silos** mean data lives in separate systems or departments with no shared view. Billing has one view of “customers”; Technical Services has another for “connections”; Community Services has another for “complaints.” The same person or place may have different IDs, names, or status in each. For a **golden thread** or a **citizen-centric** view, you need to link them—which requires common keys, agreements on ownership, and often IT or integration projects.

### 2. Inconsistent Formats and Standards

| Problem | Example |
|---------|---------|
| **Different formats** | Dates as DD/MM/YYYY in one system, YYYY-MM-DD in another, or “Q1” in a report. |
| **Different codes** | Ward “07” vs “7” vs “Ward 7”; product codes that differ between billing and assets. |
| **Different definitions** | “Collection rate” calculated differently in Finance vs a national survey. |

Without **standards** (and enforcement), integration and AI suffer. A first step is to **document** how each system defines and stores key fields (a **data dictionary** or catalog—see AI Application).

[STEP] Legacy systems and ownership

### 3. Legacy Systems and Technical Debt

Many municipalities run **older systems** that:

- Have limited or no APIs for automated extraction.
- Use proprietary or outdated formats.
- Are poorly documented; the people who built them may have left.
- Are critical to operations, so “switching off” is not an option.

**Practical approach:** Identify which legacy data is **essential** for your priority KPIs. For those, plan either (a) periodic manual or semi-manual extraction into a shared format (e.g. CSV, database), or (b) a focused integration project with IT. Do not try to integrate everything at once.

### 4. Ownership and Accountability

- **Who decides** how “collection rate” is defined? Finance, National Treasury, or the system?
- **Who is allowed** to combine billing and complaints data? POPI and internal policies may restrict.
- **Who fixes** errors? If no one owns a combined dataset, errors persist.

Governance (Module 4, Data Quality and Governance) establishes owners. Integration projects should assign **ownership for the integrated view** or the data pipeline, not only for source systems.

[STEP] POPI and integration

### 5. POPI and Data Sharing

When integrating, you may need to **link** or **share** personal data across departments or systems. This must be done in line with POPI:

- **Purpose limitation:** Use only for the stated purpose.
- **Security:** Secure transmission and storage; access controls.
- **Minimisation:** Do not integrate more personal data than needed. Often **aggregated** or **anonymised** data is enough for dashboards and many AI use cases.

For **AI**: Prefer inputs that are **aggregated, anonymised, or synthetic** when using cloud or public tools. Keep person-level data in controlled, compliant systems.

---

**Next:** Module 4: Big Data in the Municipal Context — what “big data” means for municipalities and how it relates to AI.
