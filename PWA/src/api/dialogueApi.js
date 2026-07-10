import { apiClient } from './client';

export async function getChannels() {
  const res = await apiClient.get('/dialogue/channels');
  return res.data?.data ?? [];
}

export async function getThreads(channelId) {
  const res = await apiClient.get(`/dialogue/channels/${channelId}/threads`);
  return res.data?.data ?? [];
}

export async function getMessages(threadId, { since } = {}) {
  const res = await apiClient.get(`/dialogue/threads/${threadId}/messages`, {
    params: since ? { since } : undefined,
  });
  return res.data?.data ?? [];
}

export async function sendMessage(threadId, body) {
  const res = await apiClient.post(`/dialogue/threads/${threadId}/messages`, { body });
  return res.data?.data;
}

export async function reportMessage(messageId, payload) {
  const res = await apiClient.post(`/dialogue/messages/${messageId}/report`, payload);
  return res.data;
}

export async function reportThread(threadId, payload) {
  const res = await apiClient.post(`/dialogue/threads/${threadId}/report`, payload);
  return res.data;
}

export async function blockUser(userId) {
  const res = await apiClient.post(`/users/${userId}/block`);
  return res.data;
}
