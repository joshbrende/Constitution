---
name: screens
description: "Skill for the Screens area of constitution. 126 symbols across 51 files."
---

# Screens

126 symbols | 51 files | Cohesion: 77%

## When to Use

- Working with code in `mobile/`
- Understanding how catchMessage, StaticPageScreen, postComment work
- Modifying screens-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `mobile/src/api/academyApi.js` | submitAttempt, getAcademySummary, getCourses, getMembershipCourse, getAcademyBadges (+5) |
| `mobile/src/api/dialogueApi.js` | getThreads, createThread, getMessages, sendMessage, reportThread (+3) |
| `mobile/src/screens/ChatThreadScreen.js` | ChatThreadScreen, handleSend, handleReportMessage, submitReport, handleBlockUser (+1) |
| `mobile/src/screens/SectionDetailScreen.js` | postComment, SectionDetailScreen, exportToPdf, escapeHtml, buildHtml |
| `mobile/src/screens/AcademyScreen.js` | AcademyScreen, load, loadBadges, refreshBadges, onRefresh |
| `mobile/src/screens/CertificatesScreen.js` | downloadPdfAndShare, getCertStatus, downloadAndShareCertificate, CertificatesScreen, load |
| `mobile/src/utils/apiErrors.js` | catchMessage, describeApiError, looksLikeRawTransportMessage, shortMessage |
| `mobile/src/screens/PriorityProjectsScreen.js` | handleLike, renderItem, PriorityProjectsScreen, load |
| `mobile/src/screens/AssessmentScreen.js` | handleSubmit, AssessmentScreen, init, selectOption |
| `mobile/src/api/appConfigApi.js` | nowMs, readCache, writeCache, getAppConfig |

## Entry Points

Start here when exploring this area:

- **`catchMessage`** (Function) — `mobile/src/utils/apiErrors.js:148`
- **`StaticPageScreen`** (Function) — `mobile/src/screens/StaticPageScreen.js:5`
- **`postComment`** (Function) — `mobile/src/screens/SectionDetailScreen.js:154`
- **`handleSave`** (Function) — `mobile/src/screens/ProfileScreen.js:65`
- **`handleLike`** (Function) — `mobile/src/screens/PriorityProjectsScreen.js:43`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `catchMessage` | Function | `mobile/src/utils/apiErrors.js` | 148 |
| `StaticPageScreen` | Function | `mobile/src/screens/StaticPageScreen.js` | 5 |
| `postComment` | Function | `mobile/src/screens/SectionDetailScreen.js` | 154 |
| `handleSave` | Function | `mobile/src/screens/ProfileScreen.js` | 65 |
| `handleLike` | Function | `mobile/src/screens/PriorityProjectsScreen.js` | 43 |
| `renderItem` | Function | `mobile/src/screens/PriorityProjectsScreen.js` | 77 |
| `PartyOrganDetailScreen` | Function | `mobile/src/screens/PartyOrganDetailScreen.js` | 25 |
| `LibraryDocumentScreen` | Function | `mobile/src/screens/LibraryDocumentScreen.js` | 26 |
| `handleSubmit` | Function | `mobile/src/screens/ForgotPasswordScreen.js` | 20 |
| `ChatThreadScreen` | Function | `mobile/src/screens/ChatThreadScreen.js` | 19 |
| `handleSend` | Function | `mobile/src/screens/ChatThreadScreen.js` | 90 |
| `ChatChannelScreen` | Function | `mobile/src/screens/ChatChannelScreen.js` | 14 |
| `handleCreate` | Function | `mobile/src/screens/ChatChannelScreen.js` | 45 |
| `openThread` | Function | `mobile/src/screens/ChatChannelScreen.js` | 60 |
| `handleSubmit` | Function | `mobile/src/screens/AssessmentScreen.js` | 78 |
| `getStaticPage` | Function | `mobile/src/api/staticPagesApi.js` | 2 |
| `updateProfile` | Function | `mobile/src/api/profileApi.js` | 7 |
| `likePriorityProject` | Function | `mobile/src/api/priorityProjectsApi.js` | 7 |
| `getPartyOrgan` | Function | `mobile/src/api/partyOrgansApi.js` | 13 |
| `getLibraryDocument` | Function | `mobile/src/api/libraryApi.js` | 22 |

## Execution Flows

| Flow | Type | Steps |
|------|------|-------|
| `SectionDetailScreen → NowIso` | cross_community | 7 |
| `SectionDetailScreen → ReadEnvelope` | cross_community | 6 |
| `SectionDetailScreen → GetSectionIndex` | cross_community | 6 |
| `SectionDetailScreen → ReadLegacySectionCache` | cross_community | 5 |
| `SectionDetailScreen → IsLikelyOnline` | cross_community | 3 |
| `SectionDetailScreen → Get` | cross_community | 3 |

## Connected Areas

| Area | Connections |
|------|-------------|
| Feature | 23 calls |
| Offline | 2 calls |
| Api | 1 calls |

## How to Explore

1. `gitnexus_context({name: "catchMessage"})` — see callers and callees
2. `gitnexus_query({query: "screens"})` — find related execution flows
3. Read key files listed above for implementation details
