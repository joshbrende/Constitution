# 18. Web reader features (non-admin)

Authenticated users (`auth` middleware) can use Blade **reader** routes separate from `/admin`.

| Route | View / controller | Purpose |
|-------|-------------------|---------|
| `constitution.home` | `WebConstitutionController` | Multi-doc constitution reader (`zanupf`, `zimbabwe`, `amendment3`) |

### Constitution reader toolbar (web)

- **Edit / Amendments:** Shown only when the signed-in user may access constitution admin (`AdminAccessService`).
- **Search in article:** Runs in-page find scoped to the current clause body (`#const-reader-body`) via the browser’s `window.find` when available; otherwise the user is prompted to use Ctrl+F / Cmd+F.
- **Highlights, notes, translation, read-aloud:** Not implemented on web. The UI states that these features are available in the **mobile app** (avoids non-functional buttons).

For amendment bill (`amendment3`), the reader also shows governance copy, optional **official PDF** link, and “relates to Zimbabwe constitution” links when relations exist.
| `academy.home` | Closure | Course listing; manage link if Academy section access |
| `library.home`, `library.document` | `WebLibraryController` | Library browser |
| `party.home` | `WebPartyController` | The Party landing |
| `party-organs.home`, `party-organs.show` | `WebPartyOrgansController` | Organs listing and detail |
| `dialogue.home` | Static view `sections.dialogue` | Entry to dialogue (app-heavy; web is simplified) |
| `certificate.preview` | Closure | PDF preview using `CertificatePdfService` |

## Public (no login)

| Route | Purpose |
|-------|---------|
| `/` | Welcome page |
| `certificate.verify` | `CertificateVerificationController` — public certificate lookup (throttled) |
| `health` | Health check |

---

*Last reviewed: documentation generation pass.*
