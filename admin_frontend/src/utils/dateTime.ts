import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import localizedFormat from 'dayjs/plugin/localizedFormat';

dayjs.extend(relativeTime);
dayjs.extend(localizedFormat);

/**
 * Jul 15, 2026
 */
export const formatDate = (
  date?: string | Date | null,
  format = 'MMM DD, YYYY'
): string => {
  if (!date) return '-';
  return dayjs(date).format(format);
};

/**
 * 11:30 PM
 */
export const formatTime = (
  date?: string | Date | null,
  format = 'hh:mm A'
): string => {
  if (!date) return '-';
  return dayjs(date).format(format);
};

/**
 * Jul 15, 2026 11:30 PM
 */
export const formatDateTime = (
  date?: string | Date | null,
  format = 'MMM DD, YYYY hh:mm A'
): string => {
  if (!date) return '-';
  return dayjs(date).format(format);
};

/**
 * 2 minutes ago
 * 5 hours ago
 * 3 days ago
 */
export const timeAgo = (date?: string | Date | null): string => {
  if (!date) return '-';
  return dayjs(date).fromNow();
};

/**
 * Today
 * Yesterday
 * Jul 15, 2026
 */
export const humanDate = (date?: string | Date | null): string => {
  if (!date) return '-';

  const d = dayjs(date);

  if (d.isSame(dayjs(), 'day')) {
    return `Today ${d.format('hh:mm A')}`;
  }

  if (d.isSame(dayjs().subtract(1, 'day'), 'day')) {
    return `Yesterday ${d.format('hh:mm A')}`;
  }

  return d.format('MMM DD, YYYY hh:mm A');
};