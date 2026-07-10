import { apiClient } from './client';

/** Official gazetted / cabinet PDF for Amendment Bill No. 3 (if uploaded in admin). */
export async function getAmendment3OfficialPdfMeta() {
  const { data } = await apiClient.get('/constitution/official/amendment3');
  return data?.data ?? data ?? {};
}
