# 14. Members & Users (admin)

## 14.1 Users

- **Route:** `admin.users.index` — paginated list, search by name/surname/email
- **Edit:** `admin.users.edit`, `admin.users.update` — assign roles (checkbox list)
- **Invite backend user:** `admin.users.invite.create`, `admin.users.invite.store` — **system_admin** only; email invitation with duties
- **Create backend user:** `admin.users.create-backend`, `admin.users.store-backend` — **system_admin** only; immediate account + welcome email

**Controller:** `App\Http\Controllers\Admin\UsersController`  
**Provisioning:** `BackendRoleDutiesService`, `BackendUserInvitationController` (guest accept flow)

## 14.2 Members

- **Route:** `admin.members.index` — users who have **at least one certificate** (`whereHas('certificates')`)

**Controller:** `App\Http\Controllers\Admin\MembersController`

## 14.3 RBAC

See [04-admin-rbac.md](./04-admin-rbac.md) — `user_manager` and `provincial_admin` may access Users/Members per `config/admin.php`.

---

*Last reviewed: documentation generation pass.*
