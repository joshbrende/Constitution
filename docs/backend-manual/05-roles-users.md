# 5. Roles and user management

## 5.1 Seeded roles

Defined in `database/seeders/RoleSeeder.php` (run `php artisan db:seed --class=RoleSeeder`):

| Slug | Typical use |
|------|-------------|
| `member` | Granted after passing membership assessment (`MembershipService`) |
| `student` | Default on registration |
| `instructor` | Academy (policy-dependent) |
| `moderator` | Legacy; dialogue also uses `dialogue_moderator` |
| `content_editor` | Constitution and library content preparation |
| `approver` | Publishing / approval (content policy) |
| `presidium` | Constitutional amendment approval and Presidium UI |
| `system_admin` | Full admin, role CRUD, technical access |
| `academy_manager` | Academy-only admin |
| `dialogue_moderator` | Dialogue moderation |
| `user_manager` | Users list and role assignment |
| `analytics_viewer` | Analytics read-only |
| `provincial_admin` | User and member tools (province scoping may be added later) |
| `audit_viewer` | Audit logs read-only |

## 5.2 Custom roles

Admin area **Roles** (`admin.roles.*`): **system_admin** only. After creating a role, add its slug to `config/admin.php` under the right `sections` keys.

## 5.3 Assigning roles (System Administrator guide)

This section explains, step by step, how a System Administrator assigns roles so that each user has the correct access to admin sections.

### Who can assign roles

- **System Administrator** — can assign any role, including `system_admin` and `presidium`.
- **User Manager** — can assign roles except `system_admin` and `presidium`. Those two are restricted to System Administrators only (per `config/admin.php`).

### Step-by-step flow (explicit)

1. **Log in** as a user with System Administrator (or User Manager) access.
2. **Open Admin → Users** in the sidebar.
3. **Find the user** — use the search box (name, surname, or email) if needed.
4. **Open the edit screen** — click the user’s name in the list (or the Edit link).
5. **Assign roles** — each checkbox corresponds to one role. Check the roles this user should have (e.g. Academy Manager, Content Editor, User Manager). Uncheck any role you want to remove. The user will lose access to that role's sections after you save.
6. **Click "Update roles"** — changes are not saved until you click the green button. There is no automatic save.
7. **Effect is immediate** — once saved, the user gains or loses access on their next page load. No re-login is required.

### What each role grants (quick reference)

| Role | Access |
|------|--------|
| Academy Manager | Academy courses, modules, assessments only |
| User Manager | Users & Members lists, role assignment (except System Admin / Presidium) |
| Presidium | Approve or reject constitutional amendments |
| Content Editor | Edit constitution and library content |
| Analytics Viewer | Read-only reports and exports |
| Audit Viewer | Read-only audit logs |
| Dialogue Moderator | Moderate dialogue channels and threads |
| Provincial Admin | User/member oversight (province scope as configured) |

### Technical note

The form submits role IDs; the controller runs `$user->roles()->sync(...)` on the `role_user` pivot table.

## 5.4 Members versus users

- **Users** — all accounts.
- **Members** — users who have at least one **certificate** (`MembersController` uses `whereHas('certificates')`).

## 5.5 Related docs

- [04-admin-rbac.md](./04-admin-rbac.md)
- [14-members-users.md](./14-members-users.md)

---

*Last reviewed: documentation generation pass.*
