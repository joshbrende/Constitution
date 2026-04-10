# Icon rollout plan (central repository)

Goal: migrate all icon usage to the centralized **workflow icon repository** so icons are practical, uniform, and politically relevant.

## Rule
UI code should use **semantic keys** (e.g. `academy.course`) and never hardcode library-specific icon names (Ionicons) or ad-hoc SVGs in random screens/views.

## Current: implemented
- Academy (mobile + admin)
  - Mobile: `AcademyScreen`, `CourseDetailScreen`
  - Admin: `admin/academy/*` key views now use `<x-icons.workflow-icon ...>`

## Next: mobile (highest visibility)
1. Home tiles (entry points)
   - `mobile/src/screens/HomeScreen.js`
   - Replace tile icon names with semantic keys:
     - `home.tile.presidium`, `home.tile.constitution`, `home.tile.academy`, `home.tile.library`, `home.tile.party`, `home.tile.partyOrgans`, `home.tile.chat`, `home.tile.priorityProjects`
2. Presidium role icons (fallback avatars)
   - `mobile/src/screens/PresidiumScreen.js`
   - Replace the inline role→Ionicon mapping with semantic keys:
     - `role.president`, `role.vicePresident`, `role.nationalChairperson`, `role.secretaryGeneral`, `role.fallback`
3. Library list icons
   - `mobile/src/screens/LibraryScreen.js`, `mobile/src/screens/LibraryDocumentScreen.js`
   - Semantic keys for document types/actions: `library.document`, `library.category`, `action.open`, `action.download` (as needed)
4. Party / Party organs
   - `mobile/src/screens/PartyScreen.js`, `mobile/src/screens/PartyOrgansScreen.js`, `mobile/src/screens/PartyOrganDetailScreen.js`, `mobile/src/screens/PartyLeagueDetailScreen.js`
   - Keys like `party.organ`, `party.league`, `party.flag`
5. Dialogue / Chat
   - `mobile/src/screens/ChatHomeScreen.js`, `ChatChannelScreen.js`, `ChatThreadScreen.js`
   - Keys like `dialogue.channel`, `dialogue.thread`, `action.send`, `status.official`
6. Certificates
   - `mobile/src/screens/CertificatesScreen.js`
   - Keys like `certificate.ribbon`, `certificate.revoked`, `certificate.active`
7. Reader (Section detail actions)
   - `mobile/src/screens/SectionDetailScreen.js`
   - Keys like `action.bookmark`, `action.share`, `action.copy`, `action.settings`, `action.comments`

## Next: admin web UI (consistency pass)
1. Admin dashboard tiles / nav where icons exist (if any)
2. Academy remaining screens
   - `backend/resources/views/admin/academy/**` (forms/show pages) if any ad-hoc icons exist
3. Presidium icons
   - `backend/resources/views/admin/presidium/index.blade.php` (already has mixed inline SVG + image)
   - Migrate remaining role icons to `<x-icons.workflow-icon key="role.*" ...>`
4. Guide pages that use emoji icons
   - `backend/resources/views/admin/guide/documentation.blade.php` (🎓 etc.) to semantic keys, where desired

## Repository growth policy
- Add keys in **one place** (mobile registry + admin component + docs).
- Prefer stable, political/workflow semantics:
  - “course”, “assessment”, “membership”, “organ”, “office”, “resolution”, “certificate”
- Avoid “visual semantics” like “yellowStar” or “bigIcon”.

