import api from './apiClient'
import type { Timesheet } from '../types/models'

export async function submitTimesheet(payload: Timesheet) {
    // if backend missing, this will fail — currently acts as example
    const res = await api.post('/timesheets', payload)
    return res.data
}