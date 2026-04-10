[STEP] AI Application 3: Complaint categorisation, routing, and NLP — concepts

## AI Application: Complaint Categorisation, Routing, and NLP

**Tool:** [ChatGPT](https://chat.openai.com) or [Claude](https://claude.ai)

**Important:** Use **only** **anonymised** **complaint** **descriptions** (no **names**, **IDs**, **addresses**, **contact** **details**). **AI** can: (1) **suggest** **routing** **rules** (IF **category** X THEN **route** to **team** Y); (2) **propose** **keywords** or **patterns** to **auto-categorise** from **description** (to be **implemented** in **your** **system** with **your** **data**); (3) **draft** an **NLP** **structure** for **summarising** **public** **submissions** (e.g. **IDP**)—**only** **anonymised** **text** in **public** **AI**. **You** **implement** **routing** and **categorisation** in **your** **complaint** **system**; **validate** with **Customer Care**.

[STEP] Task A — Suggest routing rules from category to team

### Task A: Suggest Routing Rules from Category to Team

1. Open **ChatGPT** or **Claude** in a new chat.
2. Send:

   > We are a South African municipality. We have a **complaint** **system** with **categories**: Water, Sanitation, Electricity, Roads, Waste, Billing, Other. We want **routing** **rules**: which **department** or **team** handles each. Our **teams** are: Water and Sanitation; Electricity; Roads and Transport; Waste; Finance (billing); Corporate (other). Suggest **routing** **rules** in the form: "IF category = [X] THEN route to [Team]". Add one **rule** for "**Unclear** or **multi-issue**" (e.g. route to **Customer Care** for **triage**). Use a table. We will implement in our system—we are NOT putting citizen data into AI.

2. **Use the output:** **Align** with your **structure** (rename **teams** if needed). **Implement** in **your** **complaint** or **CRM** **system**. **AI** suggests; **you** **own** the **routing** **logic**.

[STEP] Task B — Propose keywords for auto-categorisation (concept)

### Task B: Propose Keywords for Auto-Categorisation (Concept)

1. In a **new chat**, send:

   > We want to **auto-categorise** **complaints** from **free-text** **descriptions** into: Water, Sanitation, Electricity, Roads, Waste, Billing, Other. We will NOT put our data into AI. For each **category**, suggest **5–8** **keywords** or **short** **phrases** that often **indicate** that category (e.g. Water: "leak", "no water", "pressure", "burst", "meter"). We will use these as a **first** **draft** in our **own** **system** and **refine** with our **data**—we may use **AI** **in-house** later. Use a table.

2. **Use the output:** **Refine** with **Customer Care** (add **local** **terms**, **typos**). **Implement** as **rules** or **training** **hints** in **your** **system**. **Human** **review** for **low-confidence** **or** **Other**. **AI** suggests **concepts**; **you** **own** the **implementation**.

[STEP] Task C — Draft NLP structure for summarising public submissions (IDP)

### Task C: Draft NLP Structure for Summarising Public Submissions (IDP)

1. In a **new chat**, send:

   > We receive **written** **submissions** during **IDP** **consultation** (e.g. 30–100). We want to **summarise** them for **council** without losing **input**. We will **anonymise** before any **AI**—we only want the **structure**. Suggest: (1) **fields** to **extract** or **create** for each **submission** (e.g. **Source** (individual/org/ward), **Main topic** (1–3 themes), **Key request or point** (1–2 sentences), **IDP relevance** (which **priority** or **programme**)); (2) **one** **paragraph** on how to **aggregate** into a **council** **summary** (e.g. "Group by theme; count; cite 1–2 **anonymised** **representative** **points** per theme"). We will do the **actual** **summarising** with **anonymised** **text** in our **process**—we are NOT putting identifiable submissions into public AI.

2. **Use the output:** **Adopt** as **template** for **IDP** **submission** **analysis**. When you **run** **NLP** or **AI** on **anonymised** **submissions** (in-house or **carefully** **chosen** **tool**), use this **structure**. **Ensure** **POPI** and **no** **identifiable** **disclosure** in **council** **papers**. **AI** suggests **structure**; **you** **own** the **process** and **data**.

---

**Next:** Module 10: AI Application — Predictive Citizen Satisfaction — use AI to design predictive satisfaction concepts.
