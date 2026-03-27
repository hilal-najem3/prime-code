/**
|--------------------------------------------------------------------------
| Environment Config (Injected from App)
|--------------------------------------------------------------------------
*/

let env: Record<string, any> = {};

export function setEnv(config: Record<string, any>) {
  env = config;
}

export function getEnv(key: string, fallback?: any) {
  return env[key] ?? fallback;
}
