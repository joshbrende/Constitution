import SHONA_WORDS from '@mobile-data/shonaWords';

let sortedWords;

function words() {
  if (!sortedWords) {
    sortedWords = [...SHONA_WORDS].sort((a, b) => a.localeCompare(b, 'sn'));
  }
  return sortedWords;
}

export function currentFragment(text) {
  const match = (text || '').match(/(\S+)$/);
  return match ? match[1] : '';
}

export function getShonaSuggestions(text, limit = 6) {
  const fragment = currentFragment(text);
  if (fragment.length < 2) return [];
  const lower = fragment.toLowerCase();
  const matches = [];
  for (const word of words()) {
    if (word.toLowerCase().startsWith(lower)) {
      matches.push(word);
      if (matches.length >= limit) break;
    }
  }
  return matches;
}

export function applySuggestion(text, word) {
  const fragment = currentFragment(text);
  if (!fragment) return `${text}${word} `;
  const prefix = text.slice(0, text.length - fragment.length);
  return `${prefix}${word} `;
}
