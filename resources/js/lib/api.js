import axios from 'axios'

const api = axios.create({
  baseURL: '/api',
  headers: { 'Accept': 'application/json' },
})

export default api

export const categoriesApi = {
  list: () => api.get('/categories').then(r=>r.data.data||r.data),
  create: (p) => api.post('/categories', p).then(r=>r.data),
  update: (id,p) => api.put(`/categories/${id}`, p).then(r=>r.data),
  remove: (id) => api.delete(`/categories/${id}`),
}

export const diaryCategoriesApi = {
  list: () => api.get('/diary-categories').then(r=>r.data.data||r.data),
  create: (p) => api.post('/diary-categories', p).then(r=>r.data),
  update: (id,p) => api.put(`/diary-categories/${id}`, p).then(r=>r.data),
  remove: (id) => api.delete(`/diary-categories/${id}`),
}

export const tasksApi = {
  list: () => api.get('/tasks').then(r=>r.data.data||r.data),
  create: (p) => api.post('/tasks', p).then(r=>r.data),
  update: (id,p) => api.put(`/tasks/${id}`, p).then(r=>r.data),
  remove: (id) => api.delete(`/tasks/${id}`),
  occurrences: (p) => api.get('/occurrences', { params: p }).then(r=>r.data),
}

export const notesApi = {
  list: () => api.get('/notes').then(r=>r.data.data||r.data),
  create: (p) => api.post('/notes', p).then(r=>r.data),
  update: (id,p) => api.put(`/notes/${id}`, p).then(r=>r.data),
  remove: (id) => api.delete(`/notes/${id}`),
}

export const journalApi = {
  list: () => api.get('/journal-entries').then(r=>r.data.data||r.data),
  create: (p) => api.post('/journal-entries', p).then(r=>r.data),
  update: (id,p) => api.put(`/journal-entries/${id}`, p).then(r=>r.data),
  remove: (id) => api.delete(`/journal-entries/${id}`),
}


