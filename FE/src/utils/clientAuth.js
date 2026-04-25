export const CLIENT_PROFILE_UPDATED_EVENT = 'client-profile-updated'
export const CLIENT_REDIRECT_STORAGE_KEY = 'client_redirect_after_login'
const CLIENT_TOKEN_KEY = 'client_token'
const LEGACY_ACCESS_TOKEN_KEY = 'access_token'

export function getClientAccessToken() {
  const token = localStorage.getItem(CLIENT_TOKEN_KEY) || localStorage.getItem(LEGACY_ACCESS_TOKEN_KEY) || ''

  // Keep old sessions compatible while standardizing reads to client_token.
  if (token && !localStorage.getItem(CLIENT_TOKEN_KEY)) {
    localStorage.setItem(CLIENT_TOKEN_KEY, token)
  }

  return token
}

export function saveClientSession(token, user = {}) {
  if (!token) return

  localStorage.setItem(CLIENT_TOKEN_KEY, token)
  localStorage.setItem(LEGACY_ACCESS_TOKEN_KEY, token)
  localStorage.setItem('client_id', user.id || '')
  localStorage.setItem('client_ten', user.ten || user.name || '')
  localStorage.setItem('client_avatar', user.anh_dai_dien || user.avatar || '')

  window.dispatchEvent(new CustomEvent(CLIENT_PROFILE_UPDATED_EVENT))
}

export function clearClientSession() {
  localStorage.removeItem(CLIENT_TOKEN_KEY)
  localStorage.removeItem(LEGACY_ACCESS_TOKEN_KEY)
  localStorage.removeItem('client_id')
  localStorage.removeItem('client_ten')
  localStorage.removeItem('client_avatar')
  localStorage.removeItem(CLIENT_REDIRECT_STORAGE_KEY)

  window.dispatchEvent(new CustomEvent(CLIENT_PROFILE_UPDATED_EVENT))
}

export function saveClientRedirectPath(path) {
  if (!path) return
  localStorage.setItem(CLIENT_REDIRECT_STORAGE_KEY, path)
}

export function getClientRedirectPath() {
  return localStorage.getItem(CLIENT_REDIRECT_STORAGE_KEY) || '/'
}

export function clearClientRedirectPath() {
  localStorage.removeItem(CLIENT_REDIRECT_STORAGE_KEY)
}
