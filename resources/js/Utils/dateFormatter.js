import moment from 'moment'

/**
 * Safely format a date using moment.js
 * Handles various date formats and provides fallbacks
 * 
 * @param {any} date - The date to format
 * @param {string} format - The format to use (default: 'fromNow')
 * @param {string} locale - The locale to use (default: 'en')
 * @returns {string} - Formatted date string or fallback
 */
export function formatDate(date, format = 'fromNow', locale = 'en') {
  if (!date) return '-'
  
  try {
    let momentDate;
    
    // Handle different input types
    if (moment.isMoment(date)) {
      momentDate = date;
    } else if (typeof date === 'string') {
      // Try parsing with common formats first
      momentDate = moment(date, [
        'YYYY-MM-DDTHH:mm:ss.SSSZ',
        'YYYY-MM-DDTHH:mm:ssZ',
        'YYYY-MM-DDTHH:mm:ss',
        'YYYY-MM-DD HH:mm:ss',
        'YYYY-MM-DD',
        'MMM Do, YYYY',
        'MMMM Do, YYYY',
        'Do MMMM, YYYY',
        'Do MMM, YYYY',
        'DD/MM/YYYY',
        'MM/DD/YYYY',
        'DD-MM-YYYY',
        'MM-DD-YYYY'
      ], true);
      
      // If strict parsing failed, try default parsing
      if (!momentDate.isValid()) {
        momentDate = moment(date);
      }
    } else if (date instanceof Date) {
      momentDate = moment(date);
    } else {
      // Convert to string and try parsing
      momentDate = moment(String(date));
    }
    
    // Check if the moment object is valid
    if (!momentDate.isValid()) {
      console.warn('Invalid date format:', date);
      return '-';
    }
    
    // Set locale
    if (locale && locale !== 'en') {
      momentDate = momentDate.locale(locale);
    }
    
    // Return formatted date based on format
    switch (format) {
      case 'fromNow':
        return momentDate.fromNow();
      case 'fromNowShort':
        return momentDate.fromNow(true);
      case 'date':
        return momentDate.format('MMM DD, YYYY');
      case 'datetime':
        return momentDate.format('MMM DD, YYYY HH:mm');
      case 'time':
        return momentDate.format('HH:mm');
      case 'iso':
        return momentDate.toISOString();
      default:
        return momentDate.format(format);
    }
    
  } catch (error) {
    console.error('Error formatting date:', error, 'Date:', date);
    return '-';
  }
}

/**
 * Check if a date is valid
 * @param {any} date - The date to check
 * @returns {boolean} - True if valid, false otherwise
 */
export function isValidDate(date) {
  if (!date) return false;
  return moment(date).isValid();
}

/**
 * Get relative time (e.g., "2 hours ago")
 * @param {any} date - The date to format
 * @param {string} locale - The locale to use
 * @returns {string} - Relative time string
 */
export function getRelativeTime(date, locale = 'en') {
  return formatDate(date, 'fromNow', locale);
}

/**
 * Get short relative time (e.g., "2h ago")
 * @param {any} date - The date to format
 * @param {string} locale - The locale to use
 * @returns {string} - Short relative time string
 */
export function getShortRelativeTime(date, locale = 'en') {
  return formatDate(date, 'fromNowShort', locale);
}

/**
 * Get formatted date (e.g., "Jan 15, 2024")
 * @param {any} date - The date to format
 * @param {string} locale - The locale to use
 * @returns {string} - Formatted date string
 */
export function getFormattedDate(date, locale = 'en') {
  return formatDate(date, 'date', locale);
}

/**
 * Get formatted datetime (e.g., "Jan 15, 2024 14:30")
 * @param {any} date - The date to format
 * @param {string} locale - The locale to use
 * @returns {string} - Formatted datetime string
 */
export function getFormattedDateTime(date, locale = 'en') {
  return formatDate(date, 'datetime', locale);
}

export default {
  formatDate,
  isValidDate,
  getRelativeTime,
  getShortRelativeTime,
  getFormattedDate,
  getFormattedDateTime
}
