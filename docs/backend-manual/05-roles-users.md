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
| `stakeholder` | Analytics read-only (oversight / briefing) |
| `provincial_admin` | User and member tools scoped to assigned province |
| `audit_viewer` | Audit logs read-only |

## 5.2 Custom roles

Admin area **Roles** (`admin.roles.*`): **system_admin** only. After creating a role, add its slug to `config/admin.php` under the right `sections` keys.

## 5.3 Assigning roles (System Administrator guide)

This section explains, step by step, how a System Administrator assigns roles so that each user has the correct access to admin sections.

### Who can assign roles

- **System Administrator** — can assign any role, including `system_admin` and `presidium`. Only System Administrators can **invite** or **create** new backend dashboard users (see §5.6).
- **User Manager** — can assign roles on **existing** users except `system_admin` and `presidium`. Cannot invite or create backend staff.

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
| Stakeholder | Read-only analytics for oversight / briefing |
| Audit Viewer | Read-only audit logs |
| Dialogue Moderator | Moderate dialogue channels and threads |
| Provincial Admin | User/member oversight (province scope as configured) |

### Technical note

The form submits role IDs; the controller runs `$user->roles()->sync(...)` on the `role_user` pivot table.

## 5.6 Provisioning backend staff (System Administrator only)

Use this when onboarding new back-office users who need admin dashboard access.

### Invite by email (recommended)

1. **Admin → Users** → **Invite backend user**
2. Enter email and tick one or more **provisionable** roles (roles with admin section access)
3. The invitee receives an email with login URL, assigned duties, admin areas, and an activation link
4. They set their password on first visit; roles apply immediately

**Routes:** `admin.users.invite.create`, `admin.users.invite.store`  
**Service:** `BackendRoleDutiesService` — filters roles and builds duty briefs for UI/email

### Create with temporary password

1. **Admin → Users** → **Create backend user**
2. Enter name, surname, email, and roles
3. System generates a 16-character temporary password and emails it with duties
4. User should change password after first login

**Routes:** `admin.users.create-backend`, `admin.users.store-backend`  
**Audit:** `admin.users.backend_created`

### What is *not* granted automatically

Selecting scoped roles (e.g. `dialogue_moderator`, `academy_manager`) grants only the admin sections mapped in `config/admin.php`. Full platform access requires explicit assignment of `system_admin`.

**User Managers** and **Provincial Admins** cannot access invite/create screens (403).

## 5.7 Members versus users

- **Users** — all accounts.
- **Members** — users who have at least one **certificate** (`MembersController` uses `whereHas('certificates')`).

## 5.8 Related docs

- [04-admin-rbac.md](./04-admin-rbac.md)
- [14-members-users.md](./14-members-users.md)

---

*Last reviewed: documentation generation pass.*
