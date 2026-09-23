import { isAxiosError } from 'axios';
export const BOOKING_CONFLICT_CODE = 'BOOKING_CONFLICT';
export type ValidationErrors = Record<string, string[]>;

export class ApiError extends Error {
  readonly status: number;
  readonly code?: string;
  readonly errors?: ValidationErrors;
  readonly retryAfter?: number;

  constructor(
    message: string,
    status: number,
    options?: { code?: string; errors?: ValidationErrors; retryAfter?: number },
  ) {
    super(message);
    this.name = 'ApiError';
    this.status = status;
    this.code = options?.code;
    this.errors = options?.errors;
    this.retryAfter = options?.retryAfter;
  }
}

export function isApiError(error: unknown): error is ApiError {
  return error instanceof ApiError;
}

export function isBookingConflict(error: unknown): boolean {
  return (
    isApiError(error) &&
    error.status === 409 &&
    error.code === BOOKING_CONFLICT_CODE
  );
}

function isRecord(value: unknown): value is Record<string, unknown> {
  return typeof value === 'object' && value !== null;
}

function parseValidationErrors(value: unknown): ValidationErrors | undefined {
  if (!isRecord(value)) return undefined;
  const parsed: ValidationErrors = {};
  for (const [field, messages] of Object.entries(value)) {
    if (
      Array.isArray(messages) &&
      messages.every((m): m is string => typeof m === 'string')
    ) {
      parsed[field] = messages;
    } else {
      return undefined;
    }
  }
  return parsed;
}

function parseRetryAfter(value: unknown): number | undefined {
  if (typeof value === 'number' && Number.isFinite(value)) return value;
  if (typeof value === 'string') {
    const parsed = Number(value);
    if (value.trim() !== '' && Number.isFinite(parsed)) return parsed;
  }
  return undefined;
}

function fallbackMessage(status: number): string {
  switch (status) {
    case 401:
      return 'Unauthenticated.';
    case 403:
      return 'Forbidden.';
    case 404:
      return 'Not Found.';
    case 409:
      return BOOKING_CONFLICT_CODE;
    case 422:
      return 'The given data was invalid.';
    case 429:
      return 'Too many requests.';
    default:
      return 'Server Error.';
  }
}

export function normalizeApiError(error: unknown): ApiError {
  if (error instanceof ApiError) return error;

  if (isAxiosError(error)) {
    if (!error.response) {
      return new ApiError(
        'Network error. Please check your connection and try again.',
        0,
      );
    }
    const status = error.response.status;
    const data: unknown = error.response.data;

    let message: string | undefined;
    let errors: ValidationErrors | undefined;
    if (isRecord(data)) {
      if (typeof data['message'] === 'string' && data['message'].length > 0) {
        message = data['message'];
      }
      if (status === 422 && 'errors' in data) {
        errors = parseValidationErrors(data['errors']);
      }
    }

    const headers = error.response.headers as unknown;
    let rawRetryAfter: unknown;
    if (headers !== null && typeof headers === 'object') {
      if (typeof (headers as { get?: unknown }).get === 'function') {
        try {
          rawRetryAfter = (headers as { get: (k: string) => unknown }).get(
            'retry-after',
          );
        } catch {
          rawRetryAfter = undefined;
        }
      } else {
        rawRetryAfter = (headers as Record<string, unknown>)['retry-after'];
      }
    }

    const code =
      status === 409 && message === BOOKING_CONFLICT_CODE
        ? BOOKING_CONFLICT_CODE
        : undefined;

    return new ApiError(message ?? fallbackMessage(status), status, {
      code,
      errors,
      retryAfter: parseRetryAfter(rawRetryAfter),
    });
  }

  if (error instanceof Error) {
    return new ApiError(error.message || 'Unexpected error.', 0);
  }
  return new ApiError('Unexpected error.', 0);
}
