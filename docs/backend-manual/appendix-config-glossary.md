# Appendix B — Config keys & glossary

## B.1 Important config files

| File | Purpose |
|------|---------|
| `config/admin.php` | Admin section → allowed role slugs |
| `config/role_workflows.php` | Dashboard responsibility copy per role |
| `config/operations.php` | Audit retention, cleanup |
| `config/app.php` | App name, URL, env |
| `config/database.php` | DB connections |
| `config/sanctum.php` | API token settings |
| `config/constitution.php` | Amendment Bill No. 3 display titles, law-reference string, official PDF storage path |

## B.2 Environment variables (sample)

| Variable | Used for |
|----------|----------|
| `APP_KEY` | Encryption |
| `APP_URL` | URL generation |
| `DB_*` | Database |
| `AUDIT_LOG_RETENTION_DAYS` | Optional override for audit pruning |
| `AMENDMENT3_CHAPTER_TITLE` | Optional override for amendment bill chapter title (see `config/constitution.php`) |
| `AMENDMENT3_LAW_REFERENCE` | Optional override for `law_reference` on amendment clause versions |
| `AMENDMENT3_SHORT_TITLE` | Optional override for short-title clause body text |
| `CORS_ALLOWED_ORIGINS` | Comma-separated browser origins allowed to call the API (required for production browser clients; see `config/cors.php`) |

## B.3 Glossary

| Term | Meaning in this system |
|------|-------------------------|
| **Registered member** (analytics) | User with at least one **passing** graded assessment (not only app signup). |
| **Member role** | `member` slug — often granted with certificate/membership path. |
| **Student** | Default learner role on registration. |
| **Section version** | `SectionVersion` — draft / in_review / published constitutional text. |
| **Direct publish** | Publishing from section editor without `in_review` — audited as bypass. |

---

*Last reviewed: documentation generation pass.*
