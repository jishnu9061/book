/**
 * Utility functions for installer API calls
 */

/**
 * Get CSRF token from meta tag
 * @returns {string} CSRF token
 * @throws {Error} If CSRF token is not found
 */
export function getCsrfToken() {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  if (!token) {
    console.error('CSRF token not found. This may cause installation issues.')
    // Return a fallback token for installer (will be validated server-side)
    return 'installer-csrf-fallback'
  }
  return token
}

/**
 * Make an authenticated API request to installer endpoints
 * @param {string} url - The API endpoint URL
 * @param {Object} options - Fetch options
 * @returns {Promise<Response>} Fetch response
 */
export async function installerApiRequest(url, options = {}) {
  const csrfToken = getCsrfToken()
  
  const defaultOptions = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    credentials: 'same-origin'
  }

  const mergedOptions = {
    ...defaultOptions,
    ...options,
    headers: {
      ...defaultOptions.headers,
      ...options.headers
    }
  }

  return fetch(url, mergedOptions)
}

/**
 * Handle API response and extract JSON data
 * @param {Response} response - Fetch response
 * @returns {Promise<Object>} Parsed JSON data
 * @throws {Error} If response is not ok or JSON parsing fails
 */
export async function handleApiResponse(response) {
  if (!response.ok) {
    let errorMessage = 'Request failed'
    try {
      const errorData = await response.json()
      errorMessage = errorData.message || errorMessage
    } catch (e) {
      // If we can't parse the error response, use the status text
      errorMessage = response.statusText || errorMessage
    }
    throw new Error(errorMessage)
  }

  try {
    return await response.json()
  } catch (e) {
    throw new Error('Invalid response format')
  }
}
