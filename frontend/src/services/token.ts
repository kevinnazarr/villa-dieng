/**
 * Sanctum personal-access token persistence.
 *
 * Single place for token storage. Local storage is the current project
 * decision (backend tokens have no expiry/rotation yet). AuthContext (later
 * commit) owns login/logout flows; the API client only reads via getToken().
 */

const TOKEN_KEY = 'villa_dieng_token';

function storage(): Storage | null {
  try {
    return window.localStorage;
  } catch {
    return null;
  }
}

export function getToken(): string | null {
  return storage()?.getItem(TOKEN_KEY) ?? null;
}

export function setToken(token: string): void {
  storage()?.setItem(TOKEN_KEY, token);
}

export function clearToken(): void {
  storage()?.removeItem(TOKEN_KEY);
}
