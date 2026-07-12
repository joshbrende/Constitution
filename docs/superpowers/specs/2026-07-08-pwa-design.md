# ZANUPF PWA Design Spec

**Date:** 2026-07-08  
**Status:** Implemented — operational guide: [../../PWA.md](../../PWA.md)  
**Source reference:** `mobile/` (Expo React Native app)  
**Install location:** `PWA/`  
**Production URL:** `http://localhost:8081/app/` (served from `backend/public/app/`)

## Goal

Recreate the ZANUPF mobile member app as a Progressive Web App with Expo-like UI and feature parity, built with React + Vite + React Router, phased delivery.

## Decisions

| Topic | Choice |
|-------|--------|
| Stack | React 19, Vite 7, React Router 7, Tailwind CSS 4, axios, zod |
| Scope | Phased (B) |
| Deployment | Same origin via Laravel/nginx at `/app/` (A) |
| Auth | Bearer tokens in localStorage (matches mobile web fallback) |
| UI | Mobile-first shell (428px max), dark theme tokens from mobile |

## Architecture

```
PWA/src/          → source
backend/public/app/ → vite build output
nginx             → /app/ static SPA fallback
/api/v1/          → existing Laravel API (unchanged)
```

Dev: Vite `:5173` with proxy to `:8081/api/v1`.  
Prod: relative API base `/api/v1` (same origin).

## Phases

### Phase 1 (current)
Auth, splash, shell (tabs + menu), home, constitutions/reader (basic), profile, presidium, static pages, about.

### Phase 2
Academy, library, party, priority projects, in-app notifications.

### Phase 3
Dialogue/chat + Reverb, Web Push, offline IndexedDB cache, reader polish (comments, PDF).

## Mobile parity reference

All screens, API modules, colors, and navigation map are documented from `mobile/` exploration. PWA ports logic from equivalent files under `mobile/src/`.
