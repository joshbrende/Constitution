[STEP] Chart types — right chart for the job

## Visual Analytics Best Practices

### 1. Chart Types: Right Chart for the Job

| Data and question | Good chart type | Avoid |
|-------------------|-----------------|-------|
| **Single value vs target** (e.g. collection rate 72% vs 75%) | Big number + traffic light; or gauge; or bar to target. | Pie chart for one value. |
| **Trend over time** (e.g. monthly collection, quarterly revenue) | **Line chart** (one or a few series). | Too many lines (more than 3–4); 3D. |
| **Compare categories** (e.g. revenue by source, faults by ward) | **Bar chart** (horizontal if long labels). | Pie with many slices; 3D. |
| **Part of whole** (e.g. spend by vote) | **Bar chart** or **stacked bar**; pie only if 2–4 segments. | Pie with 8+ slices. |
| **Distribution** (e.g. ageing buckets) | **Bar chart** or **histogram**. | Pie. |
| **Geographic** (e.g. by ward or region) | **Map** if you have coordinates or boundaries; otherwise a **table or bar** by area. | Map with wrong or misleading shading. |

[STEP] Traffic lights and colour

### 2. Traffic Lights and Colour

**Traffic lights** (Green / Amber / Red) give an at-a-glance status. Define the rules clearly:

| Rule type | Example |
|-----------|---------|
| **vs target** | Green: ≥ target; Amber: 90–99% of target; Red: &lt; 90%. |
| **vs prior period** | Green: improved; Amber: flat; Red: worsened. |
| **Absolute** | Green: e.g. 0–2 audit findings; Amber: 3–5; Red: 6+. |

- **Colour:** Use **green = good**, **red = needs action**. **Amber** for “watch” or “at risk.” Avoid red–green for colour-blind users if you can; some add patterns or icons. Be **consistent** across the dashboard.
- **Do not use colour for decoration.** If it does not signal good/warning/bad or a category, consider grey or a neutral.

[STEP] Clarity — what to avoid

### 3. Clarity: What to Avoid

- **3D charts:** Distort proportions; avoid.
- **Heavy grids or borders:** They add noise. Light or no grid is often clearer.
- **Too many decimals:** One decimal for percentages is usually enough (e.g. 72.3%, not 72.345%).
- **Jargon** without a short explanation: If the audience may not know “collection rate,” add a one-line definition or a tooltip.
- **Missing “as at” date:** Every dashboard should say **“Data as at [date]”** and **refresh frequency** (e.g. “Updated monthly”).

---

**Next:** Module 5: Mobile and Responsive Dashboards — why mobile matters and simple adaptations.
