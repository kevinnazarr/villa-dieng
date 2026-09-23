import axios, {
  type AxiosInstance,
  type AxiosRequestConfig,
} from 'axios';
import { normalizeApiError } from '../lib/errors';
import { getToken } from './token';

export interface ApiRequestOptions extends AxiosRequestConfig {
  /**
   * Sent as the `Idempotency-Key` header when provided. Never auto-generated:
   * callers pass an explicit key only for reservation/payment calls.
   */
  idempotencyKey?: string;
}

function buildConfig(options?: ApiRequestOptions): AxiosRequestConfig {
  const { idempotencyKey, headers, ...rest } = options ?? {};
  return {
    ...rest,
    headers: {
      ...(headers ?? {}),
      ...(idempotencyKey ? { 'Idempotency-Key': idempotencyKey } : {}),
    },
  };
}

export const api: AxiosInstance = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  timeout: 15000,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
});

api.interceptors.request.use((config) => {
  const token = getToken();
  if (token) {
    config.headers.set('Authorization', `Bearer ${token}`);
  }
  return config;
});

api.interceptors.response.use(
  (response) => response,
  (error: unknown) => Promise.reject(normalizeApiError(error)),
);

export async function apiGet<T>(path: string, options?: ApiRequestOptions): Promise<T> {
  const response = await api.get<T>(path, buildConfig(options));
  return response.data;
}

export async function apiPost<T>(
  path: string,
  body?: unknown,
  options?: ApiRequestOptions,
): Promise<T> {
  const response = await api.post<T>(path, body, buildConfig(options));
  return response.data;
}

export async function apiPatch<T>(
  path: string,
  body?: unknown,
  options?: ApiRequestOptions,
): Promise<T> {
  const response = await api.patch<T>(path, body, buildConfig(options));
  return response.data;
}

export async function apiDelete<T>(path: string, options?: ApiRequestOptions): Promise<T> {
  const response = await api.delete<T>(path, buildConfig(options));
  return response.data;
}
