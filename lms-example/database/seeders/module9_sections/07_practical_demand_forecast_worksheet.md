[STEP] Build the demand forecast worksheet — step by step

## Practical: Demand Forecast Worksheet

**Tools:** **Google Sheets** or **Microsoft Excel**.

This worksheet supports **demand** forecasting (Module 9 theory) and links to **IDP**, **budget**, and **SDBIP**. It should use **drivers** (population, connections, consumption) and **assumptions** you can **defend** to **Planning** and **CFO**. **AI** can help **design** the **structure**—you **populate** with **your** data and **sources**; **never** put **confidential** or **person-specific** data into public AI.

### Step 1: Create the workbook and sheets

1. Open **Google Sheets** or **Excel**, new workbook.
2. Create sheets: **Assumptions**, **Demand (e.g. Water)**, **Demand (e.g. Roads or other)**, and **Cover**.
3. **Cover:** Title "Demand Forecast — [Municipality]", "Data as at [date]", "Owner: [e.g. IDP or Planning]", "For: [IDP / MTREF / both]".

### Step 2: Assumptions sheet — structure

In the **Assumptions** sheet:

- **Row 1:** Headers: `Assumption` | `Current / base` | `Source` | `Growth or trend` | `Note`.
- **Rows 2–12:** One row per **assumption**, e.g.:
  - *Population (total)*
  - *Household size (persons per household)*
  - *Households*
  - *Water connections (or % with access)*
  - *Water consumption (kl/connection/year)*
  - *Collection rate (for revenue)*
  - *Tariff increase assumption (%)*
  - *Other* (electricity, roads, waste—add as needed)

- **Source:** Stats SA, **community survey**, **billing**, **your** **estimate**. **Growth or trend:** e.g. "1.5% p.a." or "Flat." **Note:** e.g. "Conservative; Stats SA mid-year."

### Step 3: Demand (Water) sheet — structure (example)

In the **Demand (Water)** sheet (or **Water**):

- **Rows 1–2:** **Years** across columns (Y0, Y1, Y2, Y3, Y4, Y5 or 2024–2029).
- **Rows 3–10:** One row per **metric**, with **formulas** where possible, e.g.:
  - *Population* — from **Assumptions** (e.g. base × (1+growth)^year).
  - *Households* — Population ÷ household size.
  - *Connections* — e.g. Households × % with access; or **explicit** **new** **connections** **target** from **IDP**.
  - *Consumption (kl)* — Connections × consumption per connection.
  - *Revenue (R)* — Consumption × tariff × collection rate (simplified; adjust for **structure**).
  - *Bulk need (if applicable)* — Consumption ÷ (1 − NRW) or similar.

- **Document** **formulas** or **logic** in a **Note** row or second sheet. **AI** can help **suggest** **formula** **logic** from **generic** descriptions—you **implement** in **your** sheet.

### Step 4: Replicate for other services (optional)

- **Demand (Roads)** or **Waste:** Same **pattern**: **assumptions** (e.g. traffic growth, tonnage per capita); **metric** rows (e.g. **vehicles**, **tonnage**); **link** to **budget** or **capex** if needed.
- **Link to SDBIP:** **Annual** **outputs** (e.g. "New water connections Y1") can be **read** from the **Demand** sheet **Year 1** **row** for **connections** **growth**.

### Step 5: One-sentence "Key assumption" or "Sensitivity"

At the **bottom** of **Assumptions**, add: *"Key assumption to watch:"* e.g. "Population growth 1.5%—if 1% or 2%, demand and revenue shift by ±X%." This supports **scenario** and **sensitivity** (next Practical).

[STEP] Example (illustrative)

## Example: Demand Forecast (Illustrative)

**Assumptions (extract):**

| Assumption   | Base   | Source   | Growth | Note    |
|--------------|--------|----------|--------|---------|
| Population   | 250 000| Stats SA | 1.5%   | Mid-year |
| HH size      | 3.5    | Stats SA | Flat   |         |
| Water conn.  | 55 000 | Billing  | +2%    | From IDP target |

**Demand Water (extract):**

| Metric     | Y0    | Y1     | Y2     | Y3     |
|------------|-------|--------|--------|--------|
| Population | 250 000| 253 750| 257 556| 261 420|
| Connections| 55 000 | 56 100 | 57 222 | 58 366 |
| Consumption (kl)| 4 400 000| 4 488 000| … | … |

### Use this

- **In your municipality:** Use **your** **sources** (Stats SA, **billing**, **IDP**). **Planning** and **CFO** should **approve** **assumptions**. **AI** can help **suggest** **driver** **formulas**—you **own** the **numbers** and **model**.

---

**Next:** Module 9: Practical — Scenario Planning Template — build base, upside, downside.
