import { apiClient } from './client';

export async function getLibraryCategories() {
  const { data } = await apiClient.get('/library/categories');
  return data.data;
}

export async function getLibraryDocuments(params = {}) {
  const { data } = await apiClient.get('/library/documents', { params });
  return data;
}

export async function getLibraryDocument(id) {
  const { data } = await apiClient.get(`/library/documents/${id}`);
  return data.data;
}
