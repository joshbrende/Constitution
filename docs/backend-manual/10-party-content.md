# 10. The Party, Party Organs, Party Leagues, Presidium (admin)

## 10.1 The Party (landing)

- **Admin:** `admin.party.index`, `admin.party.update`, related constitution sections attach/detach/order (`party.related-sections.*`).
- **Controller:** `App\Http\Controllers\Admin\PartyController`
- **Web:** `party.home` — `WebPartyController`
- **API:** `GET /api/v1/party/profile` — `ApiPartyController@profile`

## 10.2 Party Organs

- **Admin:** `admin.party-organs.*` — CRUD for organs (Congress, Politburo, etc.)
- **Controller:** `Admin\PartyOrgansController`
- **Web:** `party-organs.home`, `party-organs.show`
- **API:** `GET /api/v1/party-organs`, `party-organs/{party_organ}`

## 10.3 Party Leagues

- **Admin:** `admin.party-leagues.*`
- **Controller:** `Admin\PartyLeaguesController`

## 10.4 Presidium (content list for app)

- **Admin:** `admin.presidium.*` — CRUD for Presidium members (display list)
- **Controller:** `Admin\PresidiumAdminController`
- **API:** `GET /api/v1/presidium` — public published list

---

*Last reviewed: documentation generation pass.*
