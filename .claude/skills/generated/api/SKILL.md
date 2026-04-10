---
name: api
description: "Skill for the Api area of constitution. 23 symbols across 11 files."
---

# Api

23 symbols | 11 files | Cohesion: 69%

## When to Use

- Working with code in `backend/`
- Understanding how handleRegister, handleLogin, setAuthToken work
- Modifying api-related functionality

## Key Files

| File | Symbols |
|------|---------|
| `backend/app/Http/Controllers/Api/DialogueController.php` | storeThread, storeMessage, reportMessage, reportThread, blockUser (+1) |
| `mobile/src/api/authStorage.js` | saveAuthTokens, saveAuthToken, clearAuthTokens, clearAuthToken |
| `backend/app/Http/Controllers/Api/CertificateController.php` | preview, index, download |
| `backend/app/Services/CertificatePdfService.php` | templateExists, canGenerate |
| `mobile/src/screens/ProfileScreen.js` | handleLogout, handleDeleteAccount |
| `backend/app/Models/DialogueMessage.php` | user |
| `lms-example/app/Models/Certificate.php` | user |
| `mobile/src/screens/RegisterScreen.js` | handleRegister |
| `mobile/src/screens/LoginScreen.js` | handleLogin |
| `mobile/src/api/client.js` | setAuthToken |

## Entry Points

Start here when exploring this area:

- **`handleRegister`** (Function) — `mobile/src/screens/RegisterScreen.js:44`
- **`handleLogin`** (Function) — `mobile/src/screens/LoginScreen.js:42`
- **`setAuthToken`** (Function) — `mobile/src/api/client.js:107`
- **`saveAuthTokens`** (Function) — `mobile/src/api/authStorage.js:5`
- **`saveAuthToken`** (Function) — `mobile/src/api/authStorage.js:41`

## Key Symbols

| Symbol | Type | File | Line |
|--------|------|------|------|
| `handleRegister` | Function | `mobile/src/screens/RegisterScreen.js` | 44 |
| `handleLogin` | Function | `mobile/src/screens/LoginScreen.js` | 42 |
| `setAuthToken` | Function | `mobile/src/api/client.js` | 107 |
| `saveAuthTokens` | Function | `mobile/src/api/authStorage.js` | 5 |
| `saveAuthToken` | Function | `mobile/src/api/authStorage.js` | 41 |
| `handleLogout` | Function | `mobile/src/screens/ProfileScreen.js` | 86 |
| `handleDeleteAccount` | Function | `mobile/src/screens/ProfileScreen.js` | 116 |
| `deleteAccount` | Function | `mobile/src/api/profileApi.js` | 12 |
| `clearAuthTokens` | Function | `mobile/src/api/authStorage.js` | 31 |
| `clearAuthToken` | Function | `mobile/src/api/authStorage.js` | 50 |
| `user` | Method | `backend/app/Models/DialogueMessage.php` | 26 |
| `storeThread` | Method | `backend/app/Http/Controllers/Api/DialogueController.php` | 64 |
| `storeMessage` | Method | `backend/app/Http/Controllers/Api/DialogueController.php` | 145 |
| `reportMessage` | Method | `backend/app/Http/Controllers/Api/DialogueController.php` | 185 |
| `reportThread` | Method | `backend/app/Http/Controllers/Api/DialogueController.php` | 210 |
| `blockUser` | Method | `backend/app/Http/Controllers/Api/DialogueController.php` | 233 |
| `unblockUser` | Method | `backend/app/Http/Controllers/Api/DialogueController.php` | 250 |
| `user` | Method | `lms-example/app/Models/Certificate.php` | 21 |
| `templateExists` | Method | `backend/app/Services/CertificatePdfService.php` | 34 |
| `canGenerate` | Method | `backend/app/Services/CertificatePdfService.php` | 53 |

## Connected Areas

| Area | Connections |
|------|-------------|
| Feature | 5 calls |
| Screens | 2 calls |
| Admin | 1 calls |

## How to Explore

1. `gitnexus_context({name: "handleRegister"})` — see callers and callees
2. `gitnexus_query({query: "api"})` — find related execution flows
3. Read key files listed above for implementation details
