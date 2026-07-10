import { openDB } from 'idb';

const DB_NAME = 'zanupf-constitution';
const DB_VERSION = 1;

async function getDb() {
  return openDB(DB_NAME, DB_VERSION, {
    upgrade(db) {
      if (!db.objectStoreNames.contains('parts')) db.createObjectStore('parts');
      if (!db.objectStoreNames.contains('chapters')) db.createObjectStore('chapters');
      if (!db.objectStoreNames.contains('sections')) db.createObjectStore('sections');
    },
  });
}

export async function readCache(store, key) {
  try {
    const db = await getDb();
    return (await db.get(store, key)) ?? null;
  } catch {
    return null;
  }
}

export async function writeCache(store, key, payload) {
  try {
    const db = await getDb();
    await db.put(store, { payload, savedAt: new Date().toISOString() }, key);
  } catch {
    // ignore quota errors
  }
}
