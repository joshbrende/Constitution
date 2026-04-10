---
name: offline
description: "Skill for the Offline area of constitution. 29 symbols across 8 files."
---

# Offline

29 symbols | 8 files | Cohesion: 79%

## When to Use

- Working with code in `mobile/`
- Understanding how ChapterDetailScreen, isLikelyOnline, loadPartsForToc work
- Modifying offline-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `mobile/src/offline/constitutionCacheStore.js` | nowIso, writeEnvelope, readEnvelope, readPartsCache, writePartsCache (+7) |
| `mobile/src/offline/networkMonitor.js` | isLikelyOnline, mapState, emit, getNetworkSnapshot, subscribeToNetwork (+1) |
| `mobile/src/offline/constitutionRepository.js` | OfflineNoCacheError, loadPartsResilient, loadChapterResilient, scheduleWarmChapters, warmChaptersInBackground |
| `mobile/src/api/sectionCache.js` | getSection, tryCache |
| `mobile/src/screens/ChapterDetailScreen.js` | ChapterDetailScreen |
| `mobile/src/offline/loadPartsForToc.js` | loadPartsForToc |
| `mobile/src/context/NetworkContext.js` | NetworkProvider |
| `mobile/src/screens/SectionDetailScreen.js` | refetchSection |

## Entry Points

Start here when exploring this area:

- **`ChapterDetailScreen`** (Function) — `mobile/src/screens/ChapterDetailScreen.js:14`
- **`isLikelyOnline`** (Function) — `mobile/src/offline/networkMonitor.js:36`
- **`loadPartsForToc`** (Function) — `mobile/src/offline/loadPartsForToc.js:7`
- **`loadPartsResilient`** (Function) — `mobile/src/offline/constitutionRepository.js:33`
- **`loadChapterResilient`** (Function) — `mobile/src/offline/constitutionRepository.js:88`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `OfflineNoCacheError` | Class | `mobile/src/offline/constitutionRepository.js` | 19 |
| `ChapterDetailScreen` | Function | `mobile/src/screens/ChapterDetailScreen.js` | 14 |
| `isLikelyOnline` | Function | `mobile/src/offline/networkMonitor.js` | 36 |
| `loadPartsForToc` | Function | `mobile/src/offline/loadPartsForToc.js` | 7 |
| `loadPartsResilient` | Function | `mobile/src/offline/constitutionRepository.js` | 33 |
| `loadChapterResilient` | Function | `mobile/src/offline/constitutionRepository.js` | 88 |
| `readPartsCache` | Function | `mobile/src/offline/constitutionCacheStore.js` | 41 |
| `writePartsCache` | Function | `mobile/src/offline/constitutionCacheStore.js` | 45 |
| `readChapterCache` | Function | `mobile/src/offline/constitutionCacheStore.js` | 51 |
| `writeChapterCache` | Function | `mobile/src/offline/constitutionCacheStore.js` | 55 |
| `getNetworkSnapshot` | Function | `mobile/src/offline/networkMonitor.js` | 42 |
| `subscribeToNetwork` | Function | `mobile/src/offline/networkMonitor.js` | 46 |
| `startNetworkMonitoring` | Function | `mobile/src/offline/networkMonitor.js` | 54 |
| `NetworkProvider` | Function | `mobile/src/context/NetworkContext.js` | 13 |
| `refetchSection` | Function | `mobile/src/screens/SectionDetailScreen.js` | 95 |
| `writeSectionCache` | Function | `mobile/src/offline/constitutionCacheStore.js` | 76 |
| `getSection` | Function | `mobile/src/api/sectionCache.js` | 13 |
| `readSectionCache` | Function | `mobile/src/offline/constitutionCacheStore.js` | 72 |
| `readLegacySectionCache` | Function | `mobile/src/offline/constitutionCacheStore.js` | 99 |
| `migrateLegacySectionIfNeeded` | Function | `mobile/src/offline/constitutionCacheStore.js` | 109 |

## Execution Flows

| Flow | Type | Steps |
|------|------|-------|
| `SectionDetailScreen → NowIso` | cross_community | 7 |
| `SectionDetailScreen → ReadEnvelope` | cross_community | 6 |
| `SectionDetailScreen → GetSectionIndex` | cross_community | 6 |
| `SectionDetailScreen → ReadLegacySectionCache` | cross_community | 5 |
| `ConstitutionListScreen → ReadEnvelope` | cross_community | 4 |
| `SectionDetailScreen → IsLikelyOnline` | cross_community | 3 |
| `SectionDetailScreen → Get` | cross_community | 3 |

## Connected Areas

| Area | Connections |
|------|-------------|
| Feature | 5 calls |
| Screens | 1 calls |

## How to Explore

1. `gitnexus_context({name: "ChapterDetailScreen"})` — see callers and callees
2. `gitnexus_query({query: "offline"})` — find related execution flows
3. Read key files listed above for implementation details
