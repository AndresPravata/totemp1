import { type ClassValue, clsx } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export const HOST = "http://veterinaria.local:5000";
export const SOCKET = "http://veterinaria.local:5000";
