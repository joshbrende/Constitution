# Performance Management AI Course (C1) — Documentation

This folder is the **source of truth** for unit content for the Performance Management and Monitoring Using AI course. It serves as the template for structuring all other courses.

## Structure

- **`module-1-sections.php`** — Manifest listing Module 1 units: file, title, duration. Used by `php artisan performance:sync-docs` to update the LMS.
- **`01_introduction.md`** … **`09_next_steps.md`** — Module 1 unit content in Markdown.

## Content format: [STEP] for panning view

Use **`[STEP]`** to create separate screens with **Next** / **Previous** navigation instead of long scrolling:

```
[STEP] Section title here

Content for this screen. Use Markdown: **bold**, lists, tables.

---

[STEP] Next section title

Content for the next screen.
```

- Each `[STEP]` block becomes one panel in the learn view. The learner clicks **Next** to move to the next section.
- **AI Application** in every module must be **practical**: which tools (e.g. ChatGPT, Claude, Excel), and **step-by-step how to use them**. Avoid generic statements like “AI can support this outcome through…”. Use: “1. Open ChatGPT. 2. Paste this prompt: … 3. Use the output to …”.

## Course-level reference (parent folder)

- **`../Performance_Management_AI_Course_2026.md`** — Full course overview, objectives, day-by-day breakdown.
- **`../SALGA_2026_Requirements_Analysis.md`** — SALGA strategic context and six outcomes (reference for drafting unit content).

## Future enhancements (template-ready)

The panning/stepper layout leaves room for:

- **Notes** — Per-step or per-lesson learner notes.
- **Q&A with facilitator** — e.g. Laravel Livewire chat or threaded Q&A per unit/step.

## Syncing to the LMS

After editing any `.md` file here, run:

```bash
php artisan performance:sync-docs
```

This finds the Performance Management course and updates the matching units’ `content` and `duration` from these files.
