# Quiz editor: True/False question type

Knowledge Check (quiz) questions can be **Multiple choice** or **True/False**. The `type` is stored on each `Question` and used in the editor, when taking the quiz, and when showing results.

## Question `type` (DB)

- **`multiple_choice`** — 2–4 options; `options` JSON `[{"text":"...","value":"..."}, ...]`; `correct_answers` = `["value"]` of the correct option.
- **`true_false`** — Fixed two choices; `options` = `[{"text":"True","value":"1"},{"text":"False","value":"0"}]`; `correct_answers` = `["1"]` (True) or `["0"]` (False).

The learner form submits `"1"` or `"0"` for True/False. `Question::isCorrectAnswer()` compares the submitted value to `correct_answers`.

## Quiz edit (units/quiz-edit)

- **Type select** per question: "Multiple choice" | "True / False" (`questions[i][type]`).
- **Multiple choice:** "Options (at least 2; select the correct one)" with 4 option inputs and radios for `correct_index` 0–3.
- **True/False:** "Correct answer" with two radios: True (`correct_index` 0) and False (`correct_index` 1). Options are not edited; the controller builds fixed `options` and `correct_answers` from `correct_index`.
- JS toggles `.question-mc-opts` and `.question-tf-opts` when the type select changes; when switching, one radio in the visible block is checked.

## UnitController::updateQuiz

- Validation: `questions.*.type` = `nullable|in:multiple_choice,true_false`; `questions.*.options` = `nullable|array` (optional for True/False).
- For **true_false:** build `options` and `correct_answers` from `correct_index` (0 → `["1"]`, 1 → `["0"]`), set `type` = `'true_false'`.
- For **multiple_choice:** unchanged; require ≥2 non‑empty options, set `type` = `'multiple_choice'`.

## Learn (taking the quiz)

- `courses/learn` already branches on `$q->type === 'true_false'`: two radios with `value="1"` (True) and `value="0"` (False).
- Results block maps `'1'` → "True", `'0'` → "False" for display.

## Add-question template

- New questions default to "Multiple choice" with `.question-mc-opts` visible and `.question-tf-opts` hidden. The template includes the type select, both option blocks, and the type-change behavior is handled by the same delegated `change` handler.
