# Quiz editor: Short answer question type

Knowledge Check questions can be **Multiple choice**, **True/False**, or **Short answer**. Short answer uses a text input; grading is case‑insensitive trimmed match against one or more accepted answers.

## Question `type` (DB)

- **`short_answer`** — `options` = `[]`; `correct_answers` = `["accepted1","accepted2",...]` (one or more strings). Match is case‑insensitive and trimmed; empty learner answer is wrong.

## Quiz edit (units/quiz-edit)

- **Type select:** "Short answer" (`questions[i][type]`).
- **Short answer block** (`.question-sa-opts`): textarea `questions[i][correct_text]`, "Correct answer(s) (one per line)". One answer per line; lines are trimmed and empty lines dropped.

## UnitController::updateQuiz

- Validation: `questions.*.type` = `in:multiple_choice,true_false,short_answer`; `questions.*.correct_text` = `nullable|string|max:1000`; `questions.*.correct_index` = `nullable`.
- For **short_answer:** split `correct_text` by newlines, trim, filter empty → `correct_answers`; `options` = `[]`; `type` = `'short_answer'`.

## Question::isCorrectAnswer

- For `short_answer`: `$v = strtolower(trim($value))`; if empty, return false. Return true if `$v` equals `strtolower(trim($c))` for any `$c` in `correct_answers`.

## Learn (taking the quiz)

- `@elseif($q->type === 'short_answer')`: `<input type="text" name="answers[{{ $q->id }}]" required maxlength="500" placeholder="Your answer">`.
- Results: "Your answer" shows the submitted string as-is.

## Add-question template and JS

- Template: "Short answer" in type select; `.question-sa-opts` (textarea) hidden by default. JS type handler shows `.question-sa-opts` when `short_answer`, hides `.question-mc-opts` and `.question-tf-opts`.
