/**
 * App config - values from data attributes on <html>
 */
export function getConfig() {
  const el = document.documentElement;
  return {
    apiUrl: el.dataset.apiUrl || '/api',
    homeUrl: el.dataset.homeUrl || '/home',
    loginUrl: el.dataset.loginUrl || '/',
    googleClientId: el.dataset.googleClientId || '',
    debug: el.dataset.appDebug === 'true',
  };
}
