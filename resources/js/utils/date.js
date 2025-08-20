import { addDays, addMonths, startOfDay, endOfDay, startOfWeek, endOfWeek, startOfMonth, endOfMonth, isSameDay, differenceInMinutes } from 'date-fns'

export { addDays, addMonths, startOfDay, endOfDay, startOfWeek, endOfWeek, startOfMonth, endOfMonth, isSameDay }

export function minutesBetween(a,b){ return Math.max(1, differenceInMinutes(b,a)) }
export function overlap(aStart,aEnd,bStart,bEnd){ return aStart<=bEnd && aEnd>=bStart }


