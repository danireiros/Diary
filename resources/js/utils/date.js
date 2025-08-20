import { addDays, addMonths, startOfDay, endOfDay, startOfWeek as dfStartOfWeek, endOfWeek as dfEndOfWeek, startOfMonth, endOfMonth, isSameDay, differenceInMinutes } from 'date-fns'

export { addDays, addMonths, startOfDay, endOfDay, startOfMonth, endOfMonth, isSameDay }

export function startOfWeek(date){
  return dfStartOfWeek(date, { weekStartsOn: 1 })
}

export function endOfWeek(date){
  return dfEndOfWeek(date, { weekStartsOn: 1 })
}

export function minutesBetween(a,b){ return Math.max(1, differenceInMinutes(b,a)) }
export function overlap(aStart,aEnd,bStart,bEnd){ return aStart<=bEnd && aEnd>=bStart }


