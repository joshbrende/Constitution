[STEP] Why infrastructure monitoring matters

## Infrastructure Monitoring

Municipal **infrastructure**—water and sewer networks, electricity distribution, roads, waste treatment, buildings—is the backbone of service delivery. **Ageing** assets, **backlogs**, and **reactive** maintenance lead to more faults, interruptions, and cost. **Infrastructure monitoring** helps you: (1) know the **condition** and **risk** of assets; (2) **prioritise** maintenance and replacement; (3) **reduce** unplanned failures; (4) report on **SDBIP** and **IDP** infrastructure KPIs. **SALGA Outcome 5** and **6** both depend on reliable infrastructure.

### Key concepts

| Concept | What it means | Typical data |
|---------|---------------|--------------|
| **Asset register** | List of assets (pipelines, substations, roads, etc.) with location, age, type, value. | Asset management system; GIS; spreadsheets. |
| **Condition** | State of the asset (good, fair, poor, critical). | Inspections; condition assessments; age-based proxies. |
| **Faults** | Failures, leaks, outages, potholes—incidents that affect service. | Fault/ complaint system; call centre; technical logs. |
| **Maintenance** | **Reactive** (fix after failure) vs **Planned** (scheduled) vs **Predictive** (before failure, based on data). | Work orders; maintenance history; sensor data (if available). |

[STEP] Faults and resolution — what to measure

## Faults and Resolution: What to Measure

| Indicator | Definition | Good practice | Why it matters |
|-----------|------------|---------------|----------------|
| **Faults reported (volume)** | Number of faults in a period, by type (water, electricity, roads, etc.) and area. | Track trend; segment by type and ward. | Shows demand and where to focus. |
| **Resolution time** | Time from report to resolution (hours or days). | Target by priority (e.g. 24h critical, 48h standard, 7 days non-urgent). | **SDBIP** commonly uses this. |
| **Backlog (open faults)** | Number of faults not yet resolved; **ageing** (how long open). | Keep low; age by 0–7, 8–30, 31+ days. | Backlog = citizen frustration and risk. |
| **Repeat faults** | Same location or asset failing again within a short period. | Reduce; indicates underlying asset or work-quality issue. | **Predictive maintenance** can help. |

[STEP] Predictive maintenance — when it helps

## Predictive Maintenance: When It Helps

**Predictive maintenance** uses **data** (age, condition, fault history, sometimes **sensor** or **IoT** data) to **predict** when an asset is likely to fail, so you can **schedule** repair or replacement **before** failure. Benefits: fewer unplanned outages, better use of crews, lower cost than pure reactive.

- **Data needed:** Fault history by asset or area; age; condition (if available); sometimes flow, pressure, or vibration from **sensors**.
- **Simple start:** **Rules** (e.g. "If X faults in 12 months for this stretch, schedule inspection") or **age-based** ("Pump older than Y years: inspect every 6 months"). **AI** can help **suggest** rules or **prioritisation** from **aggregated** fault and age data—you implement in **your** systems.
- **Advanced:** **Machine learning** on historical fault and sensor data (run **in-house** or with a vendor on **your** data—not in public AI). This module focuses on **concepts** and **rule-based** ideas you can design with AI assistance.

---

**Next:** Module 7: Citizen Satisfaction Measurement — surveys, complaints, and feedback.
