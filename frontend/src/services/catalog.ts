import { apiGet } from './api';
import type { Cabin, CollectionEnvelope, Property } from '../types/api';

/**
 * Public catalog reads. Thin wrappers around the shared client — no caching,
 * no state. Response shapes mirror the Laravel Resources 1:1.
 */
export function getProperties(): Promise<CollectionEnvelope<Property>> {
  return apiGet<CollectionEnvelope<Property>>('/properties');
}

export function getPropertyCabins(
  propertySlug: string,
): Promise<CollectionEnvelope<Cabin>> {
  return apiGet<CollectionEnvelope<Cabin>>(
    `/properties/${encodeURIComponent(propertySlug)}/cabins`,
  );
}
