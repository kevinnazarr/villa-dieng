import type { CabinImage } from '../types/api';

export function isUsableImageUrl(path: string): boolean {
  return (
    path.startsWith('https://') ||
    path.startsWith('http://') ||
    path.startsWith('/')
  );
}

export function sortImages(images: CabinImage[] | undefined): CabinImage[] {
  if (!images) return [];
  return [...images].sort((a, b) => a.sort_order - b.sort_order);
}

export function pickPrimaryImage(
  images: CabinImage[] | undefined,
): CabinImage | null {
  const sorted = sortImages(images);
  if (sorted.length === 0) return null;
  return sorted.find((image) => image.is_primary) ?? sorted[0] ?? null;
}

export function usableImages(images: CabinImage[] | undefined): CabinImage[] {
  return sortImages(images).filter((image) => isUsableImageUrl(image.path));
}
