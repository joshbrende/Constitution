# Workflow icon repository

This repo uses a **semantic icon mapping** so the mobile app and admin UI stay consistent and politically relevant.

## Rules
- Use **semantic keys** (e.g. `academy.course`) in UI code.
- Do **not** sprinkle raw Ionicons names or ad-hoc SVGs across screens/views.
- If you need a new icon, add it to the repository first, then use the key everywhere.

## Icon conventions (guardrails)
- **Mobile**:
  - Screens/components must use `WorkflowIcon` + a semantic key from `mobile/src/ui/icons/workflowIcons.js`.
  - Do **not** import `@expo/vector-icons/Ionicons` directly in screens/components (ESLint warns on this).
  - Only `mobile/src/ui/icons/WorkflowIcon.js` may import Ionicons.
- **Admin web UI**:
  - Blade templates should use `x-icons.workflow-icon` keys.
  - Do **not** paste emoji icons or inline SVGs into individual pages. Add/adjust icons centrally in `backend/resources/views/components/icons/workflow-icon.blade.php`.

## Current mappings (Academy)
- `academy.course`: learning material / curriculum
- `academy.assessment`: evaluation / examination
- `academy.membership`: membership credential
- `academy.badge`: achievement / recognition criteria

## Implementations
- **Mobile**: `mobile/src/ui/icons/workflowIcons.js` + `mobile/src/ui/icons/WorkflowIcon.js`
- **Admin**: `backend/resources/views/components/icons/workflow-icon.blade.php`

