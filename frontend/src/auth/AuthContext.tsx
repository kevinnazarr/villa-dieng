import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useRef,
  useState,
  type ReactNode,
} from 'react';
import { apiPost, apiGet } from '../services/api';
import { clearToken, getToken, setToken } from '../services/token';
import { isApiError } from '../lib/errors';
import type { ApiError } from '../lib/errors';
import type { AuthPayload, DataEnvelope, User } from '../types/api';

export type AuthStatus = 'loading' | 'authenticated' | 'guest' | 'error';

export interface RegisterInput {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}

interface AuthContextValue {
  user: User | null;
  status: AuthStatus;
  isLoading: boolean;
  isAuthenticated: boolean;
  error: ApiError | null;
  login: (email: string, password: string) => Promise<User>;
  register: (input: RegisterInput) => Promise<User>;
  logout: () => Promise<void>;
  refresh: () => Promise<User | null>;
  clearError: () => void;
}

const AuthContext = createContext<AuthContextValue | null>(null);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [status, setStatus] = useState<AuthStatus>(() =>
    getToken() ? 'loading' : 'guest',
  );
  const [error, setError] = useState<ApiError | null>(null);
  const mountedRef = useRef(true);

  useEffect(() => {
    mountedRef.current = true;
    return () => {
      mountedRef.current = false;
    };
  }, []);

  const refresh = useCallback(async (): Promise<User | null> => {
    if (!getToken()) {
      if (mountedRef.current) {
        setUser(null);
        setStatus('guest');
      }
      return null;
    }
    if (mountedRef.current) {
      setStatus('loading');
    }
    try {
      const data = await apiGet<DataEnvelope<User>>('/me');
      if (!mountedRef.current) return null;
      setUser(data.data);
      setStatus('authenticated');
      setError(null);
      return data.data;
    } catch (err) {
      if (!mountedRef.current) return null;
      if (isApiError(err) && err.status === 401) {
        clearToken();
        setUser(null);
        setStatus('guest');
        setError(null);
        return null;
      }
      setUser(null);
      setStatus('error');
      setError(isApiError(err) ? err : null);
      return null;
    }
  }, []);

  useEffect(() => {
    let cancelled = false;
    async function hydrate() {
      if (!getToken()) {
        if (!cancelled && mountedRef.current) {
          setStatus('guest');
        }
        return;
      }
      if (mountedRef.current) {
        setStatus('loading');
      }
      try {
        const data = await apiGet<DataEnvelope<User>>('/me');
        if (cancelled || !mountedRef.current) return;
        setUser(data.data);
        setStatus('authenticated');
        setError(null);
      } catch (err) {
        if (cancelled || !mountedRef.current) return;
        if (isApiError(err) && err.status === 401) {
          clearToken();
          setUser(null);
          setStatus('guest');
          setError(null);
        } else {
          setUser(null);
          setStatus('error');
          setError(isApiError(err) ? err : null);
        }
      }
    }
    void hydrate();
    return () => {
      cancelled = true;
    };
  }, []);

  const applySession = useCallback((payload: AuthPayload) => {
    setToken(payload.token);
    setUser(payload.user);
    setStatus('authenticated');
    setError(null);
    return payload.user;
  }, []);

  const login = useCallback(
    async (email: string, password: string): Promise<User> => {
      const data = await apiPost<DataEnvelope<AuthPayload>>('/auth/login', {
        email,
        password,
      });
      if (!mountedRef.current) return data.data.user;
      return applySession(data.data);
    },
    [applySession],
  );

  const register = useCallback(
    async (input: RegisterInput): Promise<User> => {
      const data = await apiPost<DataEnvelope<AuthPayload>>(
        '/auth/register',
        input,
      );
      if (!mountedRef.current) return data.data.user;
      return applySession(data.data);
    },
    [applySession],
  );

  const logout = useCallback(async (): Promise<void> => {
    try {
      await apiPost('/auth/logout');
    } catch {
    } finally {
      clearToken();
      if (mountedRef.current) {
        setUser(null);
        setStatus('guest');
        setError(null);
      }
    }
  }, []);

  const clearError = useCallback(() => {
    if (mountedRef.current) {
      setError(null);
    }
  }, []);

  const value = useMemo<AuthContextValue>(
    () => ({
      user,
      status,
      isLoading: status === 'loading',
      isAuthenticated: status === 'authenticated',
      error,
      login,
      register,
      logout,
      refresh,
      clearError,
    }),
    [
      user,
      status,
      error,
      login,
      register,
      logout,
      refresh,
      clearError,
    ],
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth(): AuthContextValue {
  const ctx = useContext(AuthContext);
  if (!ctx) {
    throw new Error('useAuth must be used inside AuthProvider');
  }
  return ctx;
}
