import { apiClient } from './client';

export async function getPriorityProjects() {
  const res = await apiClient.get('/priority-projects');
  return res.data?.data ?? [];
}

export async function likePriorityProject(id) {
  const res = await apiClient.post(`/priority-projects/${id}/like`);
  return res.data?.data;
}

export async function getPriorityProject(id) {
  const res = await apiClient.get(`/priority-projects/${id}`);
  return res.data?.data;
}
