# Constitution

Laravel backend, Expo mobile app, and project docs. **Documentation index:** [`docs/README.md`](docs/README.md).

## GitNexus (optional)

This repo is indexed with [GitNexus](https://github.com/abhigyanpatwari/GitNexus) for code intelligence (graph, MCP tools, generated skills). The index is **not** committed (`.gitignore` includes `.gitnexus/`).

**After clone**, from the repo root:

```bash
npm install
npm run gitnexus:analyze
```

- **Check freshness:** `npx gitnexus status`
- **Force full rebuild:** `npm run gitnexus:analyze:force`
- **Register for global MCP listing:** `npx gitnexus index .`

**Cursor / agents:** follow the GitNexus section in [`AGENTS.md`](AGENTS.md). Re-run `npm run gitnexus:analyze` after substantial code changes so the graph stays aligned with `HEAD`.

**Semantic search in the graph (optional, slower):** `npm run gitnexus:analyze -- --embeddings`
